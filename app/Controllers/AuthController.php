<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Models\UserDetailsModel;
use CodeIgniter\I18n\Time;

class AuthController extends BaseController
{
    protected UserModel $userModel;
    protected UserDetailsModel $userDetailsModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->userDetailsModel = new UserDetailsModel();
    }

   
    public function login()
{
    if (session()->get('logged_in')) {
        return redirect()->to('/logout');
    }

    $appName = $this->data['app_name'] ?? 'App';

    return view('auth/login', [
        'heading' => $appName
    ]);
}



public function store()
{
    // Ensure method is POST
    if ($this->request->getMethod() !== 'POST') {
        return redirect()->back()->withInput()->with('error', 'Invalid request method.');
    }

    $validation = \Config\Services::validation();

    $validation->setRules([
        'identifier' => 'required|valid_email',
        'password'   => 'required'
    ]);

    if (! $validation->withRequest($this->request)->run()) {
        return redirect()->back()
            ->withInput()
            ->with('error', $validation->getErrors());
    }

    $email    = trim($this->request->getPost('identifier'));
    $password = $this->request->getPost('password');

    // Fetch user
    $user = $this->userModel
        ->where('email', $email)
        ->where('active', 1)
        ->first();

    // Verify user existence and password
    if (! $user || ! password_verify($password, $user['password'])) {
        return redirect()->back()
            ->withInput()
            ->with('error', 'Invalid email or password.');
    }

    // Update last login timestamp
    try {
        $this->userModel->update($user['id'], [
            'last_login' => date('Y-m-d H:i:s')
        ]);
    } catch (\Exception $e) {
        log_message('error', 'Failed to update last_login for user ID ' . $user['id'] . ' — ' . $e->getMessage());
    }

    /**
     * 🔹 NEW LOGIC: Check if password needs to be changed
     * If password_changed is 0 (false), we redirect them immediately.
     */
    if ((int)$user['password_changed'] === 0) {
        // Set minimal session data so the change-password page knows who they are
        session()->set([
            'temp_user_id' => $user['id'],
            'temp_email'   => $user['email'],
            'must_change_password' => true
        ]);

        return redirect()->to('auth/change-password')
            ->with('info', 'Please change your password to proceed to your account.');
    }

    // 🔹 Standard Login Flow (If password is already changed)
    session()->regenerate();

    session()->set([
        'user_id'      => $user['id'],
        'uuid'         => $user['uuid'],
        'first_name'   => $user['first_name'],
        'email'        => $user['email'],
        'role'         => $user['role'],
        'access_level' => $user['access_level'],
        'logged_in'    => true
    ]);

    // Handle intended redirect
    $redirectTo = session()->get('intended_url');
    session()->remove('intended_url');

    if ($redirectTo && str_starts_with($redirectTo, base_url())) {
        return redirect()->to($redirectTo);
    }

    // Default fallback
    return redirect()->to('/index');
}


    
    public function logout()
    {
        session()->destroy();
        return redirect()->to('/');
    }

   
   
    public function create_account()
{
    return view('auth/create_account', [
        'title'  => 'Create Account',
        'action' => base_url('register'),
    ]);
}



public function register()
{
    if ($this->request->getMethod() !== 'POST') {
        return redirect()->back()->withInput()->with('error', 'Invalid request method.');
    }

    $validation = \Config\Services::validation();

    // Validation rules with custom error messages
    $validation->setRules([
        'first_name'       => 'required|min_length[2]|max_length[50]',
        'last_name'        => 'required|min_length[2]|max_length[50]',
        'email'            => 'required|valid_email|is_unique[users.email]',
        'national_id'      => 'required|numeric|min_length[6]|max_length[10]',
        'password'         => 'required|min_length[6]',
        'confirm_password' => 'required|matches[password]',
        'role'             => 'permit_empty|in_list[applicant]',
    ], [
        'first_name' => [
            'required'   => 'Please enter your first name.',
            'min_length' => 'First name must be at least 2 characters long.',
            'max_length' => 'First name cannot exceed 50 characters.',
        ],
        'last_name' => [
            'required'   => 'Please enter your last name.',
            'min_length' => 'Last name must be at least 2 characters long.',
            'max_length' => 'Last name cannot exceed 50 characters.',
        ],
        'email' => [
            'required'    => 'Email address is required.',
            'valid_email' => 'Please enter a valid email address.',
            'is_unique'   => 'This email is already registered. Please use another email.',
        ],
        'national_id' => [
            'required'   => 'National ID is required.',
            'numeric'    => 'National ID must contain only numbers.',
            'min_length' => 'National ID must be at least 6 digits.',
            'max_length' => 'National ID cannot exceed 10 digits.',
        ],
        'password' => [
            'required'   => 'Password is required.',
            'min_length' => 'Password must be at least 6 characters long.',
        ],
        'confirm_password' => [
            'required' => 'Please confirm your password.',
            'matches'  => 'Password confirmation does not match the password.',
        ],
        'role' => [
            'in_list' => 'Invalid role selected.',
        ],
    ]);

    if (!$validation->withRequest($this->request)->run()) {
        return redirect()->back()
            ->withInput()
            ->with('error', $validation->getErrors());
    }

    $role = $this->request->getPost('role') ?: 'applicant';

    // Generate activation token
    $activationToken = bin2hex(random_bytes(32));

    // Create user (inactive by default)
    $userData = [
        'first_name'       => $this->request->getPost('first_name'),
        'last_name'        => $this->request->getPost('last_name'),
        'email'            => $this->request->getPost('email'),
        'password'         => $this->request->getPost('password'),
        'role'             => $role,
        'access_level'     => 0,
        'password_changed' => 1,
        'active'           => 0,
        'activation_token' => $activationToken,
    ];

    $insertedId = $this->userModel->insert($userData);

    if (!$insertedId) {
        return redirect()->back()
            ->withInput()
            ->with('error', 'Unable to create account. Please try again.');
    }

    // Insert user details
    $this->userDetailsModel->insert([
        'user_id'     => $insertedId,
        'national_id' => $this->request->getPost('national_id'),
        'completed'   => 0,
        'active'      => 1,
    ]);

    // Prepare activation email
    $activationLink = base_url("/activate/{$activationToken}");
    $message = view('emails/welcome_activation', [
        'first_name'     => $this->request->getPost('first_name'),
        'appName'        => 'Kengen Recruitment Portal',
        'activationLink' => $activationLink,
    ]);

    // Queue the email using your send_email() function
    $emailQueued = send_email(
        $this->request->getPost('email'),
        'Activate Your Account',
        $message
    );

    if ($emailQueued !== true) {
        // Optional: log or notify admin if email failed to queue
        log_message('error', 'Failed to queue activation email for user ID: ' . $insertedId);
    }

    return redirect()->to('/login')->with(
        'success',
        'Account created successfully. Please check your email to activate your account before logging in.'
    );
}




public function activate($token = null)
{
    if (!$token) {
        return redirect()->to('/login')->with('error', 'Invalid activation link.');
    }

    $activated = $this->userModel->activateByToken($token);

    if ($activated) {
        return redirect()->to('/login')->with('success', 'Your account has been activated. You can now log in.');
    } else {
        return redirect()->to('/login')->with('error', 'Invalid or expired activation link.');
    }
}



/**
 * Generate a random secure password
 */
private function generateRandomPassword(int $length = 10): string
{
    $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()';
    return substr(str_shuffle(str_repeat($chars, ceil($length / strlen($chars)))), 0, $length);
}



    // ===== PASSWORD RESET =====
    public function forgotPassword()
    {
        return view('auth/password', [
             'title'      => 'Forgot Password',
             'heading'      => 'Activate Account',
            'subheading' => 'Enter your email address and we will send you a link to reset your password.',
            'showLinks' => false
        ]);
    }

  

    public function sendResetLink()
{
    $email = trim($this->request->getPost('email'));

    $userModel = new \App\Models\UserModel();
    $user = $userModel->where('email', $email)->first();

    if (!$user) {
        return redirect()->back()->with('error', 'No user found with that email address.');
    }

    // Since 'text' and your 'custom_email' helpers are autoloaded, 
    // we can use these functions directly.
    $newPassword = random_string('alnum', 10);
    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

    // 1. Update the database.
    // We set password_changed to 0 so the AuthFilter forces a reset on login.
    $updated = $userModel->update($user['id'], [
        'password'         => $hashedPassword,
        'password_changed' => 0, 
        'updated_at'       => date('Y-m-d H:i:s'),
    ]);

    if (!$updated) {
        return redirect()->back()->with('error', 'Database update failed.');
    }

    
    $loginUrl = base_url('/login');
    $subject  = 'Password Reset Request - Kengen Recruitment Portal';
    
    $message = "
        <div style='font-family: sans-serif; color: #333; max-width: 600px;'>
            <h2 style='color: #4b0ea5;'>Password Reset Successful</h2>
            <p>Hello <strong>{$user['first_name']}</strong>,</p>
            <p>Your password has been reset. Use the temporary password below to access your account:</p>
            <div style='background: #f8f9fa; padding: 20px; border: 1px dashed #ccc; text-align: center; font-size: 22px; font-weight: bold; letter-spacing: 2px;'>
                {$newPassword}
            </div>
            <p style='color: #4b0ea5; font-size: 14px;'><strong>Note:</strong> You will be required to change this password immediately after logging in.</p>
            <p style='margin-top: 30px;'>
                <a href='{$loginUrl}' style='background: #4b0ea5; color: #fff; padding: 12px 25px; text-decoration: none; border-radius: 4px;'>Login to Portal</a>
            </p>
            <br>
            <p>Regards,<br><strong>Kengen Recruitment Team</strong></p>
        </div>
    ";

    // 3. Send using your autoloaded helper function
    $status = send_email($email, $subject, $message);

    if ($status === true) {
        return redirect()->back()->with('success', 'A new password has been sent to ' . $email);
    } 

    // If it's not true, $status contains the debugger string from your helper
    log_message('error', "Email failed to $email. Debug: " . $status);
    return redirect()->back()->with('error', 'Password reset, but email delivery failed. Please contact admin.');
}


public function changePassword()
{
    $session = session();

    // Check for standard login OR the temporary "must change" flag we set in store()
    $isLoggedIn  = $session->get('logged_in');
    $mustChange  = $session->get('must_change_password');

    if (!$isLoggedIn && !$mustChange) {
        return redirect()->to('/login')->with('error', 'Please log in to access this page.');
    }

    // Load the view
    return view('auth/change_password', [
        'title'      => 'Change Password',
        'force_mode' => $mustChange // Pass this so your view can show a specific message
    ]);
}


public function updatePassword()
{
    if ($this->request->getMethod() !== 'POST') {
        return redirect()->back()->with('error', 'Invalid request method.');
    }

    $session = session();
    $mustChange = $session->get('must_change_password');
    $userId = $mustChange ? $session->get('temp_user_id') : $session->get('user_id');

    if (!$userId) {
        return redirect()->to('/login')->with('error', 'Session expired. Please log in again.');
    }

    $rules = [
        'new_password'         => 'required|min_length[8]',
        'confirm_new_password' => 'required|matches[new_password]'
    ];

    if (!$mustChange) {
        $rules['current_password'] = 'required';
    }

    if (!$this->validate($rules)) {
        return redirect()->back()->withInput()->with('error', $this->validator->getErrors());
    }

    $user = $this->userModel->find($userId);

    if (!$user) {
        return redirect()->to('/login')->with('error', 'User account not found.');
    }

    if (!$mustChange) {
        if (!password_verify($this->request->getPost('current_password'), $user['password'])) {
            return redirect()->back()->with('error', 'Current password is incorrect.');
        }
    }

    $newPassword = $this->request->getPost('new_password');
    $hashed = password_hash($newPassword, PASSWORD_DEFAULT);

    $updateData = [
        'password'         => $hashed,
        'password_changed' => 1, 
        'updated_at'       => date('Y-m-d H:i:s')
    ];

    if ($this->userModel->update($userId, $updateData)) {
        
        // 🔹 1. CRITICAL: Clear EVERY temporary flag immediately
        $session->remove('must_change_password');
        $session->remove('temp_user_id');
        $session->remove('temp_email');

        // 🔹 2. For standard users, update their session flag too
        $session->set('password_changed', 1);

        if ($mustChange) {
            // 🔹 3. Fetch FRESH user data after the update
            $freshUser = $this->userModel->find($userId);

            $session->set([
                'user_id'          => $freshUser['id'],
                'uuid'             => $freshUser['uuid'],
                'first_name'       => $freshUser['first_name'],
                'email'            => $freshUser['email'],
                'role'             => $freshUser['role'],
                'access_level'     => $freshUser['access_level'],
                'password_changed' => 1, // Explicitly set the new state
                'logged_in'        => true
            ]);
            
            // 🔹 4. Regenerate ID to finalize the "Full Login"
            $session->regenerate();
            
            return redirect()->to('index')->with('success', 'Password set successfully. Welcome!');
        }

        return redirect()->to('index')->with('success', 'Password updated successfully.');
    }

    return redirect()->back()->with('error', 'An error occurred while updating the password.');
}

}

<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UserModel;
use CodeIgniter\I18n\Time;

class AuthController extends BaseController
{
    protected UserModel $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
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

    $user = $this->userModel
        ->where('email', $email)
        ->where('active', 1)
        ->first();

    if (! $user || ! password_verify($password, $user['password'])) {
        return redirect()->back()
            ->withInput()
            ->with('error', 'Invalid email or password.');
    }

    try {
        $this->userModel->update($user['id'], [
            'last_login' => date('Y-m-d H:i:s')
        ]);
    } catch (\Exception $e) {
        log_message('error', 'Failed to update last_login for user ID ' . $user['id'] . ' — ' . $e->getMessage());
    }

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

    // 🔹 Handle intended redirect
    $redirectTo = session()->get('intended_url');

    // Remove it so it doesn’t persist
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

    
    $validation->setRules([
        'first_name'        => 'required|min_length[2]|max_length[50]',
        'last_name'         => 'required|min_length[2]|max_length[50]',
        'email'             => 'required|valid_email|is_unique[users.email]',
        'password'          => 'required|min_length[6]',
        'confirm_password'  => 'required|matches[password]',
        'role'              => 'permit_empty|in_list[admin,applicant]',
    ]);

    if (!$validation->withRequest($this->request)->run()) {
        return redirect()->back()
            ->withInput()
            ->with('error', $validation->getErrors());
    }

   
    $role = $this->request->getPost('role') ?: 'applicant';

    
    $userData = [
        'first_name'       => $this->request->getPost('first_name'),
        'last_name'        => $this->request->getPost('last_name'),
        'email'            => $this->request->getPost('email'),
        'password'         => $this->request->getPost('password'), // hashed automatically by model
        'role'             => $role,
        'access_level'     => 1,
        'password_changed' => 0,
        'active'           => 1,
    ];

    
    $insertedId = $this->userModel->insert($userData);

    if (!$insertedId) {
        return redirect()->back()
            ->withInput()
            ->with('error', 'Unable to create account. Please try again.');
    }

    // Prepare and send welcome email
    $message = view('emails/welcome', [
        'first_name' => $this->request->getPost('first_name'),
        'appName'    => 'CRVWWDA RECRUITMENT PORTAL', 
    ]);

    send_email(
        $this->request->getPost('email'),
        'Welcome to CRVWWDA RECRUITMENT PORTAL',
        $message
    );

    // Success: redirect back to login without old input
    return redirect()->to('/login')->with(
        'success',
        'Account created successfully. You can now log in.'
    );
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
    $email = $this->request->getPost('email');

    $userModel = new \App\Models\UserModel();
    $user = $userModel->where('email', $email)->first();

    if (!$user) {
        return redirect()->back()->with('error', 'No user found with that email address.');
    }

    
    helper('text');
    $newPassword = random_string('alnum', 10);

    
    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

    
    $userModel->update($user['id'], [
        'password' => $hashedPassword,
        'change_pass' => 1, 
        'updated_at' => date('Y-m-d H:i:s'),
    ]);

    
    $loginUrl = base_url('/login');

    
    $subject = 'Your New Password - CRVWWDA VMS';
    $message = "
        <p>Hello <strong>{$user['first_name']}</strong>,</p>
        <p>Your password has been reset. Here is your new password:</p>
        <p style='font-size:16px;'><strong>{$newPassword}</strong></p>
        <p>Please log in and change it immediately for your security.</p>
        <p>
            <a href='{$loginUrl}' 
               style='display:inline-block;padding:10px 18px;background:#1a73e8;color:#fff;
                      text-decoration:none;border-radius:5px;margin-top:10px;'>Login Now</a>
        </p>
        <br>
        <p>Regards,<br><strong>CRVWWDA-VMS Team</strong></p>
    ";

    
    $emailService = \Config\Services::email();
    $emailService->setTo($email);
    $emailService->setSubject($subject);
    $emailService->setMessage($message);

    if ($emailService->send()) {
        return redirect()->back()->with('success', 'Password reset successfully. A new password has been sent to the user\'s email.');
    } else {
        log_message('error', 'Password reset email failed: ' . $emailService->printDebugger(['headers']));
        return redirect()->back()->with('error', 'Password updated, but failed to send the email.');
    }
}




    
public function changePassword()
{
    // Ensure the user is logged in
    if (!session()->get('logged_in')) {
        return redirect()->to('/login')->with('error', 'Please log in first.');
    }

    // Load the view
    return view('auth/change_password', [
        'title' => 'Change Password'
    ]);
}


    
    public function updatePassword()
{
    if ($this->request->getMethod() !== 'POST') {
        return redirect()->back()->with('error', 'Invalid request method.');
    }

    $validation = \Config\Services::validation();
    $validation->setRules([
        'current_password'      => 'required',
        'new_password'          => 'required|min_length[8]',
        'confirm_new_password'  => 'required|matches[new_password]'
    ]);

    if (!$validation->withRequest($this->request)->run()) {
        return redirect()->back()->withInput()->with('error', $validation->getErrors());
    }

    $userId = session('user_id');
    $user = $this->userModel->find($userId);

    if (!$user) {
        return redirect()->back()->with('error', 'User not found.');
    }

    
    if (!password_verify($this->request->getPost('current_password'), $user['password'])) {
        return redirect()->back()->with('error', 'Current password is incorrect.');
    }

    $newPassword = $this->request->getPost('new_password');
    $hashed = password_hash($newPassword, PASSWORD_DEFAULT);

    
    $updateData = [
        'password'     => $hashed,
        'change_pass'  => 0, 
        'updated_at'   => date('Y-m-d H:i:s')
    ];

    if ($this->userModel->update($userId, $updateData)) {
        return redirect()->to('/index')->with('success', 'Password updated successfully.');
    }

    return redirect()->back()->with('error', 'Failed to update password.');
}

}

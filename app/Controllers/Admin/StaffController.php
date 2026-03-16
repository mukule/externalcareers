<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\UserModel;

class StaffController extends BaseController
{
    protected $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    /**
     * List all staff (admins)
     */
    public function index()
    {
        $staff = $this->userModel
            ->where('role', 'admin')
           // ->where('active', 1)
            ->orderBy('id', 'DESC')
            ->findAll();

        $applicants = $this->userModel
            ->where('role', 'applicant')
           // ->where('active', 1)
            ->orderBy('first_name', 'ASC')
            ->findAll();

        return view('admin/staffs', [
            'title'      => 'Staff Management',
            'staff'      => $staff,
            'applicants' => $applicants, // for selecting an existing user
        ]);
    }

    /**
     * Show form for creating or editing staff
     */
    public function form($uuid = null)
    {
        $staff = null;

        if ($uuid) {
            $staff = $this->userModel->where('uuid', $uuid)->first();
            if (!$staff) {
                return redirect()->back()->with('error', 'Staff not found.');
            }
        }

        return view('admin/staff_form', [
            'title'  => $uuid ? 'Edit Staff' : 'Add Staff',
            'staff'  => $staff,
            'applicants' => $this->userModel->where('role', 'applicant')->where('active', 1)->findAll(),
        ]);
    }


    public function save()
{
    $data = $this->request->getPost();
    $validation = \Config\Services::validation();

    // -----------------------
    // Determine validation rules
    // -----------------------
    if (!empty($data['existing_user'])) {
        $rules = [
            'existing_user' => 'required|integer',
        ];
    } else {
        $rules = [
            'first_name' => 'required|string|max_length[100]',
            'last_name'  => 'required|string|max_length[100]',
            'email'      => 'required|valid_email|max_length[150]',
        ];
    }

    if (!$validation->setRules($rules)->withRequest($this->request)->run()) {
        return redirect()->back()->withInput()->with('error', $validation->getErrors());
    }

    // -----------------------
    // Promote or update existing user
    // -----------------------
    if (!empty($data['existing_user'])) {
        $user = $this->userModel->find($data['existing_user']);
        if (!$user) {
            return redirect()->back()->with('error', 'Selected user not found.');
        }

        // Prepare update data
        $updateData = [];

        // Only update first_name, last_name, email if explicitly provided
        if (!empty($data['first_name'])) $updateData['first_name'] = $data['first_name'];
        if (!empty($data['last_name']))  $updateData['last_name']  = $data['last_name'];
        if (!empty($data['email']))      $updateData['email']      = $data['email'];

        // Only update role if user is not already admin
        if ($user['role'] !== 'admin') {
            $updateData['role'] = 'admin';
        }

        // Only update password if provided
        if (!empty($data['password'])) {
            $updateData['password'] = $data['password']; // model will hash automatically
        }

        // Email uniqueness check (ignoring current user)
        if (!empty($updateData['email'])) {
            $existingEmailUser = $this->userModel
                ->where('email', $updateData['email'])
                ->where('id !=', $user['id'])
                ->first();
            if ($existingEmailUser) {
                return redirect()->back()->withInput()->with('error', 'Email already exists.');
            }
        }

        $this->userModel->update($user['id'], $updateData);

        // Send promotion notification if role changed to admin
        if (!empty($updateData['role']) && $updateData['role'] === 'admin' && $user['role'] !== 'admin') {
            $emailData = [
                'first_name'   => $updateData['first_name'] ?? $user['first_name'],
                'email'        => $updateData['email'] ?? $user['email'],
                'is_new_admin' => false,
            ];
            $message = view('emails/admin_notification', $emailData);
            send_email($emailData['email'], 'Your Account Has Been Promoted to Admin', $message);
        }

        return redirect()->to(base_url('admin/staffs'))->with('success', 'User updated successfully.');
    }

    // -----------------------
    // Creating a new admin
    // -----------------------
    $existingUser = $this->userModel->where('email', $data['email'])->first();
    if ($existingUser) {
        return redirect()->back()->withInput()->with('error', 'Email already exists.');
    }

    // Generate a secure random password if not provided
    $password = !empty($data['password']) ? $data['password'] : bin2hex(random_bytes(5));

    $userData = [
        'first_name' => $data['first_name'],
        'last_name'  => $data['last_name'],
        'email'      => $data['email'],
        'role'       => 'admin',
        'active'     => 1,
        'password'   => $password, // model will hash automatically
    ];

    $this->userModel->insert($userData);

    // Send email notification with login credentials
    $emailData = [
        'first_name'   => $data['first_name'],
        'email'        => $data['email'],
        'password'     => $password,
        'is_new_admin' => true,
    ];
    $message = view('emails/admin_notification', $emailData);
    send_email($data['email'], 'Your Admin Account Has Been Created', $message);

    return redirect()->to(base_url('admin/staffs'))->with('success', 'Admin created successfully and notification sent.');
}


public function toggleActive($userId)
{
    $user = $this->userModel->find($userId);

    if (!$user) {
        return redirect()->back()->with('error', 'User not found.');
    }

    // Invert active status
    $newStatus = $user['active'] == 1 ? 0 : 1;

    // Update explicitly
    $this->userModel->update($userId, ['active' => $newStatus]);

    return redirect()->back()->with('success', 'User status updated.');
}



}

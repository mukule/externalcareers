<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Models\RoleModel;

class StaffController extends BaseController
{
    protected $userModel;
    protected $roleModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->roleModel = new Rolemodel();
    }

   
    public function index()
{
    $userRoleModel = new \App\Models\UserRoleModel();
    $roleModel     = new \App\Models\RoleModel();

    // Staff roles we care about
    $staffRoleIds = $roleModel
        ->whereIn('name', ['super_admin', 'hr_general', 'hr_interns', 'hr_attachments', 'ict'])
        ->findColumn('id');

    // Fetch roles for filter dropdown
    $roles = $roleModel
        ->whereIn('id', $staffRoleIds)
        ->findAll();

    // Get filters from GET request
    $search     = $this->request->getGet('search');  // e.g., name or email
    $roleFilter = $this->request->getGet('role');    // role id to filter

    // Base query: join users and roles
    $builder = $userRoleModel
        ->select('users.*, roles.id AS role_id, roles.name AS role_name')
        ->join('users', 'users.id = user_roles.user_id')
        ->join('roles', 'roles.id = user_roles.role_id')
        ->whereIn('role_id', $staffRoleIds)
        ->orderBy('users.id', 'DESC');

    // Apply search filter
    if (!empty($search)) {
        $builder->groupStart()
                ->like('users.first_name', $search)
                ->orLike('users.last_name', $search)
                ->orLike('users.email', $search)
                ->groupEnd();
    }

    // Apply role filter
    if (!empty($roleFilter)) {
        $builder->where('roles.id', $roleFilter);
    }

    // Paginate: 20 per page
    $perPage = 20;
    $page    = $this->request->getGet('page') ?? 1;

    $staffRows = $builder->paginate($perPage, 'group1', $page);
    $pager     = $userRoleModel->pager;

    // Group roles per user
    $staff = [];
    foreach ($staffRows as $row) {
        $uid = $row['id'];
        if (!isset($staff[$uid])) {
            $staff[$uid] = $row;
            $staff[$uid]['roles'] = [];
        }
        $staff[$uid]['roles'][] = [
            'id'   => $row['role_id'],
            'name' => $row['role_name']
        ];
    }
    $staff = array_values($staff);

    return view('admin/staffs', [
        'title'      => 'Staff Access',
        'staff'      => $staff,
        'pager'      => $pager,
        'perPage'    => $perPage,
        'search'     => $search,
        'roleFilter' => $roleFilter,
        'roles'      => $roles,       // <-- for dropdown filter
    ]);
}

   
public function form($uuid = null)
{
    $staff = null;
    $assignedRoles = [];
    $nationalId = null;

    // Fetch staff if editing
    if ($uuid) {
        $staff = $this->userModel->where('uuid', $uuid)->first();
        if (!$staff) {
            return redirect()->back()->with('error', 'Staff not found.');
        }

        // Get assigned roles for this staff
        $userRoleModel = new \App\Models\UserRoleModel();
        $assignedRoles = array_column(
            $userRoleModel->getRolesForUser($staff['id']),
            'id'
        );

        // Fetch staff national_id from user_details if exists
        $userDetailsModel = new \App\Models\UserDetailsModel();
        $details = $userDetailsModel->where('user_id', $staff['id'])->first();
        if ($details && !empty($details['national_id'])) {
            $nationalId = $details['national_id'];
        }
    }

    // Fetch all roles for checkboxes, excluding role named "applicant"
    $roleModel = new \App\Models\RoleModel();
    $allRoles = array_filter($roleModel->findAll(), function($role) {
        return strtolower($role['name']) !== 'applicant';
    });

    return view('admin/staff_form', [
        'title'          => $uuid ? 'Edit Staff' : 'Add Staff',
        'staff'          => $staff,
        'assignedRoles'  => $assignedRoles,   
        'allRoles'       => $allRoles,
        'nationalId'     => $nationalId,       // pass to view
    ]);
}


public function save()
{
    $data = $this->request->getPost();
    $validation = \Config\Services::validation();

    // -----------------------
    // Validation rules
    // -----------------------
    if (!empty($data['existing_user'])) {
        $rules = [
            'existing_user' => 'required|integer'
            // roles can be empty to allow removing admin rights
        ];
    } else {
        $rules = [
            'first_name'   => 'required|string|max_length[100]',
            'last_name'    => 'required|string|max_length[100]',
            'email'        => 'required|valid_email|max_length[150]|is_unique[users.email]',
            'national_id'  => 'required|string|max_length[50]',
        ];
    }

    if (!$validation->setRules($rules)->withRequest($this->request)->run()) {
        return redirect()->back()->withInput()->with('error', $validation->getErrors());
    }

    $userRoleModel = new \App\Models\UserRoleModel();
    $userDetailsModel = new \App\Models\UserDetailsModel(); // Ensure you have this model

    // -----------------------
    // Updating an existing user
    // -----------------------
    if (!empty($data['existing_user'])) {
        $user = $this->userModel->find($data['existing_user']);
        if (!$user) {
            return redirect()->back()->with('error', 'Selected user not found.');
        }

        // Remove old roles
        $userRoleModel->where('user_id', $user['id'])->delete();

        // Prepare roles
        $rolesToAssign = [];
        if (!empty($data['roles'])) {
            foreach ($data['roles'] as $roleId) {
                if ($roleId === 'applicant') continue;
                $rolesToAssign[] = intval($roleId);
            }
        }

        // Assign new roles
        foreach ($rolesToAssign as $roleId) {
            $userRoleModel->insert([
                'user_id' => $user['id'],
                'role_id' => $roleId
            ]);
        }

        // Update main role
        $mainRole = (!empty($data['roles']) && in_array('applicant', $data['roles'])) ? 'applicant' : (!empty($rolesToAssign) ? 'admin' : 'applicant');
        $this->userModel->update($user['id'], ['role' => $mainRole]);

        // -----------------------
        // Update or create user_details
        // -----------------------
        $details = $userDetailsModel->where('user_id', $user['id'])->first();
        $detailsData = [
            'national_id' => $data['national_id'] ?? null,
        ];
        if ($details) {
            $userDetailsModel->update($details['id'], $detailsData);
        } else {
            $detailsData['user_id'] = $user['id'];
            $userDetailsModel->insert($detailsData);
        }

        return redirect()->to(base_url('admin/staffs'))->with('success', 'User roles and details updated successfully.');
    }

    // -----------------------
    // Creating a new user
    // -----------------------
    $rolesToAssign = [];
    if (!empty($data['roles'])) {
        foreach ($data['roles'] as $roleId) {
            if ($roleId === 'applicant') continue;
            $rolesToAssign[] = intval($roleId);
        }
    }

    $password = bin2hex(random_bytes(5));

    $userData = [
        'first_name' => $data['first_name'],
        'last_name'  => $data['last_name'],
        'email'      => $data['email'],
        'role'       => (!empty($data['roles']) && in_array('applicant', $data['roles'])) ? 'applicant' : (!empty($rolesToAssign) ? 'admin' : 'applicant'),
        'active'     => 1,
        'password'   => $password, 
    ];

    $userId = $this->userModel->insert($userData);

    // Insert roles
    foreach ($rolesToAssign as $roleId) {
        $userRoleModel->insert([
            'user_id' => $userId,
            'role_id' => $roleId
        ]);
    }

    // -----------------------
    // Create user_details
    // -----------------------
    $detailsData = [
        'user_id'     => $userId,
        'national_id' => $data['national_id'] ?? null,
        'active'      => 1,
    ];
    $userDetailsModel->insert($detailsData);

    // Send email notification
    $emailData = [
        'first_name'   => $data['first_name'],
        'email'        => $data['email'],
        'password'     => $password,
        'is_new_admin' => (!empty($rolesToAssign) && !in_array('applicant', $data['roles'])),
    ];
    $message = view('emails/admin_notification', $emailData);
    send_email($data['email'], 'Your Account Has Been Created', $message);

    return redirect()->to(base_url('admin/staffs'))->with('success', 'User created successfully and notification sent.');
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



public function getUserByEmail()
{
    $email = $this->request->getGet('email');
    if (!$email || strlen($email) < 3) {
        return $this->response->setJSON([]);
    }

    $users = $this->userModel
                  ->select('id, uuid, first_name, last_name, email, active')
                  ->where('role', 'applicant')
                  ->like('email', $email)
                  ->findAll(10); // limit results

    return $this->response->setJSON($users ?: []);
}


}

<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UserDetailsModel;

class UserDetailsController extends BaseController
{
    protected $userDetailsModel;

    public function __construct()
    {
        $this->userDetailsModel = new UserDetailsModel();
    }

   
    public function index()
    {
        $userId = session()->get('user_id'); 
        $details = $this->userDetailsModel->where('user_id', $userId)->first();

        return view('user/user_details', [
            'title'   => 'My Profile',
            'details' => $details
        ]);
    }

   
    public function create()
    {
        $userId = session()->get('user_id');
        $details = $this->userDetailsModel->where('user_id', $userId)->first();

        if ($details) {
            return redirect()->to('/user-details/edit')->with('info', 'You already have details. You can edit them.');
        }

        return view('user/create_user_detail', [
            'title'  => 'Complete Your Profile',
            'action' => base_url('user-details/store')
        ]);
    }

   

    public function store()
{
    $userId = session()->get('user_id');

    if (!$userId) {
        return redirect()->to('/login')->with('error', 'Please log in first.');
    }

    $post = $this->request->getPost();
    $detailsId = $post['id'] ?? null; 

    $userModel        = new \App\Models\UserModel();
    $userDetailsModel = new \App\Models\UserDetailsModel();

    // ----- VALIDATION RULES -----
    $rules = [
        'first_name'            => 'required|min_length[2]',
        'last_name'             => 'required|min_length[2]',
        'gender'                => 'required|in_list[male,female,other]',
        'dob'                   => 'required|valid_date[Y-m-d]',
        'phone'                 => 'required',
        'ethnicity_id'          => 'required|integer',
        'national_id'           => 'required',
        'county_of_origin_id'   => 'required|integer',
        'county_of_residence_id'=> 'required|integer',
    ];

    if (! $this->validate($rules)) {
        return redirect()->back()->withInput()->with('error', $this->validator->getErrors());
    }

    // ----- MANUAL NATIONAL ID CHECK -----
    if ($detailsId) {
        $current = $userDetailsModel->find($detailsId);

        if ($current && $current['national_id'] !== $post['national_id']) {
            $existing = $userDetailsModel->where('national_id', $post['national_id'])->first();
            if ($existing) {
                return redirect()->back()->withInput()->with('error', ['national_id' => 'The National ID must be unique.']);
            }
        }
    } else {
        $existing = $userDetailsModel->where('national_id', $post['national_id'])->first();
        if ($existing) {
            return redirect()->back()->withInput()->with('error', ['national_id' => 'The National ID must be unique.']);
        }
    }

    // ----- DATA PREP -----
    $detailsData = [
        'user_id'                => $userId,
        'national_id'            => $post['national_id'],
        'gender'                 => $post['gender'],
        'dob'                    => $post['dob'],
        'phone'                  => $post['phone'],
        'ethnicity_id'           => $post['ethnicity_id'],
        'county_of_origin_id'    => $post['county_of_origin_id'],
        'county_of_residence_id' => $post['county_of_residence_id'],
        'disability_status'      => isset($post['disability_status']) ? 1 : 0,
        'disability_type'        => $post['disability_type'] ?? null,
        'disability_number'      => $post['disability_number'] ?? null,
        'completed'              => 1,
        'active'                 => 1,
    ];

    // ----- UPDATE USER FIRST/LAST NAME -----
    $userModel->update($userId, [
        'first_name' => $post['first_name'],
        'last_name'  => $post['last_name'],
    ]);

    // ----- UPSERT -----
    if ($detailsId) {
        $userDetailsModel->update($detailsId, $detailsData);
    } else {
        $userDetailsModel->insert($detailsData);
    }

    return redirect()->back()->with('success', 'Profile saved successfully.');
}



  
    public function edit()
    {
        $userId = session()->get('user_id');
        $details = $this->userDetailsModel->where('user_id', $userId)->first();

        if (!$details) {
            return redirect()->to('/user-details/create')->with('info', 'Please complete your profile first.');
        }

        return view('user/edit_user_detail', [
            'title'   => 'Edit My Profile',
            'action'  => base_url('user-details/update'),
            'details' => $details
        ]);
    }

    /**
     * Update user details
     */
    public function update()
    {
        $userId = session()->get('user_id');
        $details = $this->userDetailsModel->where('user_id', $userId)->first();

        if (!$details) {
            return redirect()->to('/user-details/create')->with('info', 'Please complete your profile first.');
        }

        $validation = \Config\Services::validation();
        $validation->setRules([
            'national_id'  => "required|is_unique[user_details.national_id,id,{$details['id']}]",
            'gender'       => 'required|in_list[male,female,other]',
            'dob'          => 'required|valid_date[Y-m-d]',
            'phone'        => 'required',
            'ethnicity_id' => 'required|integer',
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('error', $validation->getErrors());
        }

        $updateData = [
            'national_id'       => $this->request->getPost('national_id'),
            'gender'            => $this->request->getPost('gender'),
            'dob'               => $this->request->getPost('dob'),
            'phone'             => $this->request->getPost('phone'),
            'ethnicity_id'      => $this->request->getPost('ethnicity_id'),
            'disability_status' => $this->request->getPost('disability_status') ? 1 : 0,
            'disability_number' => $this->request->getPost('disability_number') ?? null,
            'completed'         => 1,
            'active'            => 1,
        ];

        $this->userDetailsModel->update($details['id'], $updateData);

        return redirect()->back()->with('success', 'Profile updated successfully.');
    }
}

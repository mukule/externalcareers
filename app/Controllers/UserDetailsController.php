<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UserDetailsModel;
use App\Models\UserModel;
use App\Models\GenderModel;
use App\Models\EthnicityModel;
use App\Models\CountyModel;
use App\Models\CountryModel;
use App\Models\FieldOfStudyModel;
use App\Models\EducationLevelModel;
use App\Models\MaritalStatusModel;

class UserDetailsController extends BaseController
{
    protected $userDetailsModel;
    protected $userModel;

    public function __construct()
    {
        $this->userDetailsModel = new UserDetailsModel();
        $this->userModel        = new UserModel();
    }

    /**
     * Show user details page
     */
    public function index()
    {
        $userId  = session()->get('user_id');
        $details = $this->userDetailsModel->where('user_id', $userId)->first();

        return view('user/user_details', [
            'title'   => 'My Profile',
            'details' => $details,
        ]);
    }

    /**
     * Create user details form
     */
    public function create()
    {
        $userId  = session()->get('user_id');
        $details = $this->userDetailsModel->where('user_id', $userId)->first();

        if ($details) {
            return redirect()->to('/user-details/edit')->with('info', 'You already have details.');
        }

        return view('user/create_user_detail', [
            'title'            => 'Complete Your Profile',
            'action'           => base_url('user-details/store'),
            'genders'          => (new GenderModel())->where('active', 1)->orderBy('title', 'ASC')->findAll(),
            'ethnicities'      => (new EthnicityModel())->where('active', 1)->orderBy('name', 'ASC')->findAll(),
            'counties'         => (new CountyModel())->where('active', 1)->orderBy('title', 'ASC')->findAll(),
            'countries'        => (new CountryModel())->where('active', 1)->orderBy('title', 'ASC')->findAll(),
            'maritalStatuses'  => (new MaritalStatusModel())->where('active', 1)->orderBy('title', 'ASC')->findAll(),
            'fieldsOfStudy'    => (new FieldOfStudyModel())->where('active', 1)->orderBy('name', 'ASC')->findAll(),
            'levelsOfStudy'    => (new EducationLevelModel())->where('active', 1)->orderBy('index', 'ASC')->findAll(),
        ]);
    }

    /**
     * Store new user details
     */
    public function store()
    {
        $userId = session()->get('user_id');

        if (!$userId) {
            return redirect()->to('/login')->with('error', 'Please log in first.');
        }

        $post      = $this->request->getPost();
        $detailsId = $post['id'] ?? null;

        // ✅ VALIDATION
        $rules = [
            'first_name'                => 'required|min_length[2]',
            'last_name'                 => 'required|min_length[2]',
            'gender_id'                 => 'required|integer',
            'dob'                       => 'required|valid_date[Y-m-d]',
            'phone'                     => 'required',
            'ethnicity_id'              => 'required|integer',
            'national_id'               => 'required',
            'county_of_origin_id'       => 'required|integer',
            'county_of_residence_id'    => 'required|integer',
            'country_of_birth_id'       => 'required|integer',
            'country_of_residence_id'   => 'required|integer',
            'marital_status_id'         => 'permit_empty|integer',
            'field_of_study_id'         => 'permit_empty|integer',
            'highest_level_of_study_id' => 'permit_empty|integer',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', $this->validator->getErrors());
        }

        // ✅ NATIONAL ID UNIQUE CHECK
        $existing = $this->userDetailsModel
            ->where('national_id', $post['national_id'])
            ->where('id !=', $detailsId ?? 0)
            ->first();

        if ($existing) {
            return redirect()->back()->withInput()->with('error', [
                'national_id' => 'The National ID must be unique.'
            ]);
        }

        // ✅ PREP DATA
        $detailsData = [
            'user_id'                   => $userId,
            'national_id'               => $post['national_id'],
            'gender_id'                 => $post['gender_id'],
            'dob'                       => $post['dob'],
            'phone'                     => $post['phone'],
            'ethnicity_id'              => $post['ethnicity_id'],
            'county_of_origin_id'       => $post['county_of_origin_id'],
            'county_of_residence_id'    => $post['county_of_residence_id'],
            'country_of_birth_id'       => $post['country_of_birth_id'],
            'country_of_residence_id'   => $post['country_of_residence_id'],
            'marital_status_id'         => $post['marital_status_id'] ?? null,
            'field_of_study_id'         => $post['field_of_study_id'] ?? null,
            'highest_level_of_study_id' => $post['highest_level_of_study_id'] ?? null,
            'disability_status'         => isset($post['disability_status']) ? 1 : 0,
            'disability_type'           => $post['disability_type'] ?? null,
            'disability_number'         => $post['disability_number'] ?? null,
            'completed'                 => 1,
            'active'                    => 1,
        ];

        // ✅ UPDATE USER NAME
        $this->userModel->update($userId, [
            'first_name' => $post['first_name'],
            'last_name'  => $post['last_name'],
        ]);

        // ✅ UPSERT
        if ($detailsId) {
            $this->userDetailsModel->update($detailsId, $detailsData);
        } else {
            $this->userDetailsModel->insert($detailsData);
        }

        return redirect()->back()->with('success', 'Basic information updated successfully.');
    }

    /**
     * Edit user details
     */
    public function edit()
    {
        $userId  = session()->get('user_id');
        $details = $this->userDetailsModel->where('user_id', $userId)->first();

        if (!$details) {
            return redirect()->to('/user-details/create')->with('info', 'Please complete your profile first.');
        }

        return view('user/edit_user_detail', [
            'title'            => 'Edit My Profile',
            'action'           => base_url('user-details/update'),
            'details'          => $details,
            'genders'          => (new GenderModel())->where('active', 1)->orderBy('title', 'ASC')->findAll(),
            'ethnicities'      => (new EthnicityModel())->where('active', 1)->orderBy('name', 'ASC')->findAll(),
            'counties'         => (new CountyModel())->where('active', 1)->orderBy('title', 'ASC')->findAll(),
            'countries'        => (new CountryModel())->where('active', 1)->orderBy('title', 'ASC')->findAll(),
            'maritalStatuses'  => (new MaritalStatusModel())->where('active', 1)->orderBy('title', 'ASC')->findAll(),
            'fieldsOfStudy'    => (new FieldOfStudyModel())->where('active', 1)->orderBy('name', 'ASC')->findAll(),
            'levelsOfStudy'    => (new EducationLevelModel())->where('active', 1)->orderBy('index', 'ASC')->findAll(),
        ]);
    }

    /**
     * Update existing user details
     */
    public function update()
    {
        $userId  = session()->get('user_id');
        $details = $this->userDetailsModel->where('user_id', $userId)->first();

        if (!$details) {
            return redirect()->to('/user-details/create')->with('info', 'Please complete your profile first.');
        }

        $validation = \Config\Services::validation();

        $validation->setRules([
            'national_id'             => "required|is_unique[user_details.national_id,id,{$details['id']}]",
            'gender_id'               => 'required|integer',
            'dob'                     => 'required|valid_date[Y-m-d]',
            'phone'                   => 'required',
            'ethnicity_id'            => 'required|integer',
            'county_of_origin_id'     => 'required|integer',
            'county_of_residence_id'  => 'required|integer',
            'country_of_birth_id'     => 'required|integer',
            'country_of_residence_id' => 'required|integer',
            'marital_status_id'       => 'permit_empty|integer',
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('error', $validation->getErrors());
        }

        $updateData = [
            'national_id'               => $this->request->getPost('national_id'),
            'gender_id'                 => $this->request->getPost('gender_id'),
            'dob'                       => $this->request->getPost('dob'),
            'phone'                     => $this->request->getPost('phone'),
            'ethnicity_id'              => $this->request->getPost('ethnicity_id'),
            'county_of_origin_id'       => $this->request->getPost('county_of_origin_id'),
            'county_of_residence_id'    => $this->request->getPost('county_of_residence_id'),
            'country_of_birth_id'       => $this->request->getPost('country_of_birth_id'),
            'country_of_residence_id'   => $this->request->getPost('country_of_residence_id'),
            'marital_status_id'         => $this->request->getPost('marital_status_id') ?? null,
            'field_of_study_id'         => $this->request->getPost('field_of_study_id') ?? null,
            'highest_level_of_study_id' => $this->request->getPost('highest_level_of_study_id') ?? null,
            'disability_status'         => $this->request->getPost('disability_status') ? 1 : 0,
            'disability_type'           => $this->request->getPost('disability_type') ?? null,
            'disability_number'         => $this->request->getPost('disability_number') ?? null,
            'completed'                 => 1,
            'active'                    => 1,
        ];

        $this->userDetailsModel->update($details['id'], $updateData);

        return redirect()->back()->with('success', 'Basic information updated successfully.');
    }
}
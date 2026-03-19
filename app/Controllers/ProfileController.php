<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Models\UserDetailsModel;
use App\Models\EthnicityModel;
use App\Models\ProfessionalStatementModel;
use App\Models\UserEducationModel;
use App\Models\UserWorkExperienceModel;
use App\Models\UserMembershipModel;
use App\Models\UserCertificationModel;
use App\Models\UserRefereesModel;
use App\Models\CountyModel;
use App\Models\FieldOfStudyModel;
use App\Models\EducationLevelModel;
use App\Models\UserBasicEducationModel; 
use App\Models\UserHigherEducationModel; 

class ProfileController extends BaseController
{
    protected $userModel;
    protected $userDetailsModel;
    protected $ethnicityModel;
    protected $professionalStatementModel;
    protected $educationModel;
    protected $workExperienceModel;
    protected $membershipModel;
    protected $certificationModel;
    protected $refereesModel;
    protected $userCertificationModel;
    protected $countyModel;
    protected $fieldOfStudyModel;
    protected $educationLevelModel;
    protected $userBasicEducationModel;
    protected $userHigherEducationModel; 

    public function __construct()
    {
        $this->userModel                 = new UserModel();
        $this->userDetailsModel          = new UserDetailsModel();
        $this->ethnicityModel            = new EthnicityModel();
        $this->professionalStatementModel = new ProfessionalStatementModel();
        $this->educationModel            = new UserEducationModel();
        $this->workExperienceModel       = new UserWorkExperienceModel();
        $this->membershipModel           = new UserMembershipModel();
        $this->certificationModel        = new UserCertificationModel();
        $this->refereesModel             = new UserRefereesModel();
        $this->userCertificationModel   = new UserCertificationModel();
        $this->countyModel              = new CountyModel();
        $this->fieldOfStudyModel        = new FieldOfStudyModel();
        $this->educationLevelModel     = new EducationLevelModel();
        $this->userBasicEducationModel  = new UserBasicEducationModel(); 
        $this->userHigherEducationModel = new UserHigherEducationModel ();
    }

    private function requireLogin()
    {
        if (!session()->get('user_id')) {
            return redirect()->to('/login')->with('error', 'Please log in first.');
        }
        return null;
    }

   
    public function index()
{
    $userId = session()->get('user_id');

    // Fetch user details
    $details = $this->userDetailsModel->where('user_id', $userId)->first();

    // Fetch reference data
    $ethnicities = $this->ethnicityModel
        ->where('active', 1)
        ->orderBy('name', 'ASC')
        ->findAll();

    $counties = $this->countyModel
        ->where('active', 1)
        ->orderBy('title', 'ASC')
        ->findAll();

    $genders = (new \App\Models\GenderModel())
        ->where('active', 1)
        ->orderBy('title', 'ASC')
        ->findAll();

    $countries = (new \App\Models\CountryModel())
        ->where('active', 1)
        ->orderBy('title', 'ASC')
        ->findAll();

    // ✅ NEW: marital status
    $maritalStatuses = (new \App\Models\MaritalStatusModel())
        ->where('active', 1)
        ->orderBy('title', 'ASC')
        ->findAll();

    // Fetch fields & levels
    $fieldsOfStudy = (new \App\Models\FieldOfStudyModel())
        ->where('active', 1)
        ->orderBy('name', 'ASC')
        ->findAll();

    $levelsOfStudy = (new \App\Models\EducationLevelModel())
        ->where('active', 1)
        ->orderBy('index', 'ASC')
        ->findAll();

    return view('applicant/profile', [
        'title'            => 'Bio Information',
        'details'          => $details,

        'ethnicities'      => $ethnicities,
        'counties'         => $counties,
        'countries'        => $countries,
        'genders'          => $genders,
        'maritalStatuses'  => $maritalStatuses, // ✅ added

        'fieldsOfStudy'    => $fieldsOfStudy,
        'levelsOfStudy'    => $levelsOfStudy,
    ]);
}


    public function professionalStatement()
    {
        if ($redirect = $this->requireLogin()) return $redirect;

        $userId = session()->get('user_id');

        $statement = $this->professionalStatementModel
                          ->where('user_id', $userId)
                          ->first();

        return view('applicant/professional_statement', [
            'title'       => 'Professional Statement',
            'statement'   => $statement,
            'currentStep' => 2
        ]);
    }

    public function saveProfessionalStatement()
    {
        if ($redirect = $this->requireLogin()) return $redirect;

        $userId = session()->get('user_id');
        $statementText = $this->request->getPost('statement');

        if (empty($statementText)) {
            return redirect()->back()
                             ->withInput()
                             ->with('error', 'Professional statement cannot be empty.');
        }

        $existing = $this->professionalStatementModel
                         ->where('user_id', $userId)
                         ->first();

        if ($existing) {
            $this->professionalStatementModel->update($existing['id'], [
                'statement' => $statementText,
                'completed' => 1
            ]);
        } else {
            $this->professionalStatementModel->insert([
                'user_id'   => $userId,
                'statement' => $statementText,
                'completed' => 1
            ]);
        }

        return redirect()->to('/applicant/professional-statement')
                         ->with('success', 'Professional statement saved successfully.');
    }




    public function resume(?string $userUuid = null)
{
    helper(['job_eligibility', 'url', 'filesystem']); 

    $isAdminView = false;

    // 1. Determine User Context
    if ($userUuid) {
        $user = $this->userModel->where('uuid', $userUuid)->first();

        if (!$user) {
            return redirect()->back()->with('error', 'User not found.');
        }

        $userId = $user['id'];
        $isAdminView = true;

    } else {
        if ($redirect = $this->requireLogin()) {
            return $redirect;
        }

        $userId = session()->get('user_id');
        $user   = $this->userModel->find($userId);

        if (!$user) {
            return redirect()->to('/login')->with('error', 'You must be logged in.');
        }
    }

    // 2. Page Title
    $fullName  = trim(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? ''));
    $pageTitle = 'Resume - ' . ($fullName ?: 'User');

    // 3. Load User Details (COUNTIES + MARITAL STATUS FIXED)
    $details = $this->userDetailsModel
        ->select('
            user_details.*,
            genders.title AS gender_name,
            ethnicities.name AS ethnicity_name,
            fields_of_study.name AS study_field,
            edu_levels.name AS highest_edu_level,
            edu_levels.index AS edu_index,

            cbirth.title AS country_of_birth_name,
            cres.title AS country_of_residence_name,

            coo.title AS county_of_origin_name,
            cor.title AS county_of_residence_name,

            ms.title AS marital_status_name
        ')
        ->join('gender genders', 'genders.id = user_details.gender_id', 'left')
        ->join('ethnicities', 'ethnicities.id = user_details.ethnicity_id', 'left')
        ->join('fields_of_study fields_of_study', 'fields_of_study.id = user_details.field_of_study_id', 'left')
        ->join('education_levels edu_levels', 'edu_levels.id = user_details.highest_level_of_study_id', 'left')

        // Countries
        ->join('country cbirth', 'cbirth.id = user_details.country_of_birth_id', 'left')
        ->join('country cres', 'cres.id = user_details.country_of_residence_id', 'left')

        // Counties
        ->join('county coo', 'coo.id = user_details.county_of_origin_id', 'left')
        ->join('county cor', 'cor.id = user_details.county_of_residence_id', 'left')

        // ✅ Marital Status FIX: use marital_status_id
        ->join('marital_status ms', 'ms.id = user_details.marital_status_id', 'left')

        ->where('user_details.user_id', $userId)
        ->where('user_details.active', 1)
        ->first();

    if (!$details) {
        $details = [
            'national_id'               => '',
            'gender_name'               => '',
            'dob'                       => '',
            'phone'                     => '',
            'ethnicity_name'            => '',
            'country_of_birth_name'     => '',
            'country_of_residence_name' => '',
            'county_of_origin_name'     => '',
            'county_of_residence_name'  => '',
            'marital_status_name'       => '',
            'disability_status'         => 0,
            'disability_number'         => '',
            'highest_edu_level'         => '',
            'edu_index'                 => 0,
            'study_field'               => '',
        ];
    }

    // 4. Basic Education
    $basicEducation = $this->userBasicEducationModel
        ->where('user_id', $userId)
        ->where('active', 1)
        ->orderBy('date_ended', 'DESC')
        ->findAll();

    foreach ($basicEducation as &$edu) {
        $edu['certificate_url'] = !empty($edu['certificate'])
            ? base_url('uploads/certificates/' . $edu['certificate'])
            : null;
    }

    // 5. Higher Education
    $higherEducation = $this->userHigherEducationModel->getByUserId($userId);

    // 6. Certifications
    $certifications = $this->userCertificationModel
        ->select('user_certifications.*, certifications.name AS cert_name, certifying_bodies.name AS issuing_body')
        ->join('certifications', 'certifications.id = user_certifications.certification_id', 'left')
        ->join('certifying_bodies', 'certifying_bodies.id = certifications.certifying_body_id', 'left')
        ->where('user_certifications.user_id', $userId)
        ->orderBy('user_certifications.attained_date', 'DESC')
        ->findAll();

    foreach ($certifications as &$cert) {
        $cert['certificate_url'] = !empty($cert['certificate_file'])
            ? base_url('uploads/certifications/' . $cert['certificate_file'])
            : null;
    }

    // 7. Work Experience
    $workExperience = $this->workExperienceModel
        ->where('user_id', $userId)
        ->orderBy('start_date', 'DESC')
        ->findAll() ?? [];

    foreach ($workExperience as &$work) {
        $work['reference_url'] = !empty($work['reference_file'])
            ? base_url('uploads/work_experience/' . $work['reference_file'])
            : null;
    }

    // 8. Memberships
    $memberships = $this->membershipModel
        ->where('user_id', $userId)
        ->findAll() ?? [];

    foreach ($memberships as &$mem) {
        $mem['certificate_url'] = !empty($mem['certificate'])
            ? base_url('uploads/memberships/' . $mem['certificate'])
            : null;
    }

    // 9. Referees
    $referees = $this->refereesModel
        ->where('user_id', $userId)
        ->findAll() ?? [];

    // 10. Professional Statement
    $statement = $this->professionalStatementModel
        ->where('user_id', $userId)
        ->first() ?? [];

    // 11. Total Experience
    $totalExperience = calculate_user_work_experience($userId);

    // 12. Final Data
    $data = [
        'title'           => $pageTitle,
        'user'            => $user,
        'details'         => $details,
        'isAdminView'     => $isAdminView,
        'currentStep'     => 9,

        'statement'       => $statement,

        'basicEducation'  => $basicEducation,
        'higherEducation' => $higherEducation,

        'workExperience'  => $workExperience,
        'memberships'     => $memberships,
        'certifications'  => $certifications,
        'referees'        => $referees,

        'totalExperience' => $totalExperience
    ];

    $viewPath = $isAdminView ? 'admin/user_resume' : 'applicant/resume';

    return view($viewPath, $data);
}
}

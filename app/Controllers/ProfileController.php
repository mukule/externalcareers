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

    $details = $this->userDetailsModel->where('user_id', $userId)->first();
    $ethnicities = $this->ethnicityModel->where('active', 1)->findAll();
    $counties = $this->countyModel->where('active', 1)->orderBy('name', 'ASC')->findAll();

    // Static fallback list of countries
    $countries = [
        'Kenya', 'Uganda', 'Tanzania', 'Rwanda', 'Burundi', 'South Sudan', 'Ethiopia', 'Somalia', 'Other'
    ];

    // Optional: try fetching from RestCountries (fail silently)
    if (function_exists('file_get_contents')) {
        try {
            $response = @file_get_contents('https://restcountries.com/v3.1/all');
            if ($response) {
                $json = json_decode($response, true);
                if (is_array($json)) {
                    $countries = [];
                    foreach ($json as $country) {
                        if (isset($country['name']['common'])) {
                            $countries[] = $country['name']['common'];
                        }
                    }
                    sort($countries);
                }
            }
        } catch (\Exception $e) {
            // fallback is already set
        }
    }

    return view('applicant/profile', [
        'title'       => 'My Profile',
        'details'     => $details,
        'ethnicities' => $ethnicities,
        'counties'    => $counties,
        'countries'   => $countries
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
    helper('job_eligibility'); 

    $isAdminView = false;

    // Determine user
    if ($userUuid) {
        // Admin viewing a user by UUID
        $user = $this->userModel->where('uuid', $userUuid)->first();
        if (!$user) {
            return redirect()->back()->with('error', 'User not found.');
        }
        $userId = $user['id'];
        $isAdminView = true; // mark that this is admin view
    } else {
        // Regular logged-in user
        if ($redirect = $this->requireLogin()) return $redirect;
        $userId = session()->get('user_id');
        $user   = $this->userModel->find($userId);
        if (!$user) {
            return redirect()->to('/login')->with('error', 'You must be logged in.');
        }
    }

    $fullName  = trim(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? ''));
    $pageTitle = 'Resume - ' . ($fullName ?: 'User');

    // --- Load all resume data as before ---
    $details = $this->userDetailsModel
        ->select('user_details.*, ethnicities.name AS ethnicity_name')
        ->join('ethnicities', 'ethnicities.id = user_details.ethnicity_id', 'left')
        ->where('user_details.user_id', $userId)
        ->first() ?? [];

    $education = $this->educationModel
        ->select('user_education.*, education_levels.name AS level_name')
        ->join('education_levels', 'education_levels.id = user_education.level_id', 'left')
        ->where('user_education.user_id', $userId)
        ->orderBy('user_education.end_year', 'DESC')
        ->findAll();

    foreach ($education as $key => $edu) {
        $education[$key]['certificate_url'] = !empty($edu['certificate'])
            ? base_url('uploads/certificates/' . $edu['certificate'])
            : null;
    }

    $certifications = $this->userCertificationModel
        ->select('user_certifications.*, certifications.name AS cert_name, certifying_bodies.name AS issuing_body')
        ->join('certifications', 'certifications.id = user_certifications.certification_id', 'left')
        ->join('certifying_bodies', 'certifying_bodies.id = certifications.certifying_body_id', 'left')
        ->where('user_certifications.user_id', $userId)
        ->orderBy('user_certifications.attained_date', 'DESC')
        ->findAll();

    foreach ($certifications as $key => $cert) {
        $filePath = FCPATH . 'uploads/certifications/' . ($cert['certificate_file'] ?? '');
        $certifications[$key]['certificate_url'] = file_exists($filePath)
            ? base_url('uploads/certifications/' . $cert['certificate_file'])
            : null;
    }

    $totalExperience = calculate_user_work_experience($userId);

    $data = [
        'title'          => $pageTitle,
        'user'           => $user,
        'details'        => $details,
        'statement'      => $this->professionalStatementModel->where('user_id', $userId)->first() ?? [],
        'education'      => $education,
        'workExperience' => $this->workExperienceModel->where('user_id', $userId)->orderBy('start_date', 'DESC')->findAll() ?? [],
        'memberships'    => $this->membershipModel->where('user_id', $userId)->findAll() ?? [],
        'certifications' => $certifications,
        'referees'       => $this->refereesModel->where('user_id', $userId)->findAll() ?? [],
        'totalExperience'=> $totalExperience
    ];

    // --- Decide which view to load ---
    if ($isAdminView) {
        return view('admin/user_resume', $data);
    } else {
        return view('applicant/resume', $data);
    }
}



}

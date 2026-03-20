<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\JobModel;
use App\Models\JobTypeModel;
use App\Models\JobApplicationModel;
use App\Models\FieldOfStudyModel;
use App\Models\JobDisciplineModel;

class Home extends BaseController
{
    protected $userModel;
    protected $jobModel;
    protected $jobTypeModel;
    protected $jobApplicationModel;
    protected $fieldOfStudyModel;
    protected $jobDisciplineModel;


    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->jobModel = new \App\Models\JobModel();
        $this->jobTypeModel = new JobTypeModel();
        $this->jobApplicationModel = new JobApplicationModel();
         $this->fieldOfStudyModel   = new FieldOfStudyModel();
        $this->jobDisciplineModel   = new JobDisciplineModel();
    }

   
    public function index()
{
    $role = strtolower(session()->get('role'));

    if ($role === 'admin') {
        return redirect()->to('/admin');
    }

    if ($role === 'applicant') {
        return redirect()->to('/'); 
    }

    return redirect()->to('/login')->with('error', 'Access denied.');
}



public function home()
{
    $jobTypeModel = model(\App\Models\JobTypeModel::class);

    $jobTypes = $jobTypeModel
        ->select('uuid, name, display_name, banner, icon, description')
        ->where('active', 1)
        ->findAll();

    return view('pages/index', [
        'title'    => $this->data['app_name'],
        'jobTypes' => $jobTypes
    ]);
}



public function jobDetail($uuid = null)
{
    if (!$uuid) {
        return redirect()->to('/')->with('error', 'Job not specified.');
    }

    // Fetch job with related info
    $job = $this->jobModel
        ->select('jobs.*, 
                  job_types.display_name AS job_type_name, 
                  job_types.uuid AS job_type_uuid,
                  education_levels.name AS education_name,
                  job_disciplines.name AS discipline_name')
        ->join('job_types', 'job_types.id = jobs.job_type_id', 'left')
        ->join('education_levels', 'education_levels.id = jobs.min_education_level_id', 'left')
        ->join('job_disciplines', 'job_disciplines.id = jobs.discipline_id', 'left')
        ->where('jobs.uuid', $uuid)
        ->where('jobs.active', 1)
        ->first();

    if (!$job) {
        return redirect()->to('/')->with('error', 'Job not found or inactive.');
    }

    // Determine job status
    $today = date('Y-m-d');
    if ($today < $job['date_open']) {
        $job['status'] = 'Upcoming';
    } elseif ($today > $job['date_close']) {
        $job['status'] = 'Closed';
    } else {
        $job['status'] = 'Open';
    }

    // Fetch specialities / fields for this job
    $specialityModel = new \App\Models\JobSpecialityModel();
    $job['specialities'] = $specialityModel->getByJob($job['id']); // returns array of field objects

    // Fetch application requirements
    $requirements = get_job_application_requirements($job['id']);

    // Pass all data to the view
    return view('pages/job_detail', [
        'title'        => $job['name'] . ' - Job Details',
        'job'          => $job,
        'requirements' => $requirements['requirements'] ?? [],
    ]);
}


public function jobsByType($jobTypeUuid = null)
{
    if (!$jobTypeUuid) {
        return redirect()->to('/')->with('error', 'Job type not specified.');
    }

    // Get job type
    $jobType = $this->jobTypeModel
                    ->where('uuid', $jobTypeUuid)
                    ->where('active', 1)
                    ->first();

    if (!$jobType) {
        return redirect()->to('/')->with('error', 'Job type not found.');
    }

    
    $filters = [
        'name'          => $this->request->getGet('name'),
        'reference_no'  => $this->request->getGet('reference_no'),
        'discipline_id' => $this->request->getGet('discipline_id'),
        'field_id'      => $this->request->getGet('field_id'),
    ];

    // Fetch filtered jobs
    $openJobs = $this->jobModel->getOpenJobsByType($jobType['id'], $filters);

   
    $disciplines = $this->jobDisciplineModel
                        ->where('active', 1)
                        ->findAll();

    $fieldsOfStudy = $this->fieldOfStudyModel
                          ->where('active', 1)
                          ->findAll();

    return view('pages/job_types', [
        'title'        => $jobType['display_name'] . ' Jobs',
        'jobType'      => $jobType,
        'jobs'         => $openJobs,
        'disciplines'  => $disciplines,
        'fieldsOfStudy'=> $fieldsOfStudy,
        'filters'      => $filters, 
    ]);
}



public function adminDashboard()
{
    $totalApplicants       = $this->userModel->countApplicants();
    $totalJobs             = $this->jobModel->countTotalJobs();
    $openJobs              = $this->jobModel->countOpenJobs();
    $totalApplications     = $this->jobApplicationModel->countApplications();
    $qualifiedApplications = $this->jobApplicationModel->countQualifiedApplications();

    
    $filters = [
        'name'        => $this->request->getGet('name'),
        'email'       => $this->request->getGet('email'),
        'national_id' => $this->request->getGet('national_id'),
    ];

   
    $perPage = 10;
    $page    = (int) $this->request->getGet('page') ?: 1;

   
    $applicants = $this->userModel->getApplicantsPaginated($perPage, $page, $filters);

    
    $jobsWithCounts = $this->jobApplicationModel->getJobsWithApplicationCounts();

    return view('dashboard/admin', [
        'title'                  => 'Admin Dashboard',
        'totalApplicants'        => $totalApplicants,
        'totalJobs'              => $totalJobs,
        'openJobs'               => $openJobs,
        'totalApplications'      => $totalApplications,
        'qualifiedApplications'  => $qualifiedApplications,
        'applicants'             => $applicants['data'], 
        'applicantsTotal'        => $applicants['total'],
        'applicantsPage'         => $applicants['page'],
        'applicantsPerPage'      => $applicants['perPage'],
        'filters'                => $filters,             
        'jobsWithCounts'         => $jobsWithCounts,
    ]);
}

   
    private function currentUser(): array
    {
        return [
            'id'           => session()->get('user_id'),
            'first_name'   => session()->get('first_name'),
            'role'         => session()->get('role'),
            'access_level' => session()->get('access_level'),
        ];
    }

   
    private function isAdmin(): bool
    {
        return strtolower(session()->get('role')) === 'admin';
    }


    public function applicants()
{
    if (!$this->isAdmin()) {
        return redirect()->to('/login')->with('error', 'Unauthorized access.');
    }

    $applicants = $this->userModel
                       ->where('role', 'applicant')
                       ->orderBy('created_at', 'DESC')
                       ->findAll();

    return view('admin/applicants', [
        'title'      => 'All Applicants',
        'applicants' => $applicants,
    ]);
}


public function toggleUserStatus(int $userId, string $action)
{
   
    if (!$this->isAdmin()) {
        return redirect()->back()->with('error', 'Unauthorized action.');
    }

    $currentUserId = session()->get('user_id');

   
    if ($userId === $currentUserId && $action === 'deactivate') {
        return redirect()->back()->with('error', 'Self Deactivation Prohibited');
    }

    $active = $action === 'activate' ? true : false;

    $success = $this->userModel->setUserStatus($userId, $active);

    if ($success) {
        $statusText = $active ? 'activated' : 'deactivated';
        return redirect()->back()->with('success', "User successfully $statusText.");
    }

    return redirect()->back()->with('error', 'User not found or could not update status.');
}



public function userDetail(string $uuid)
{
    if (!$this->isAdmin()) {
        return redirect()->to('/login')->with('error', 'Unauthorized access.');
    }

    $userDetailsModel = new \App\Models\UserDetailsModel();
    $countyModel      = new \App\Models\CountyModel();

    // Get user
    $user = $this->userModel->where('uuid', $uuid)->first();
    if (!$user) {
        return redirect()->back()->with('error', 'User not found.');
    }

    // Get details
    $details = $userDetailsModel->getByUserId((int)$user['id']);
    if (!$details) {
        return redirect()->back()->with('error', 'User details not found.');
    }

    // Merge
    $userFull = array_merge($user, $details);

    // Profile completion
    $userFull['profile_completed'] = $userDetailsModel->isComplete((int)$user['id']);

    // ✅ Resolve county names
    $userFull['county_of_origin_name'] = !empty($userFull['county_of_origin_id'])
        ? $countyModel->getCountyNameById((int)$userFull['county_of_origin_id'])
        : null;

    $userFull['county_of_residence_name'] = !empty($userFull['county_of_residence_id'])
        ? $countyModel->getCountyNameById((int)$userFull['county_of_residence_id'])
        : null;

    return view('admin/registrant_details', [
        'title' => 'User Details - ' . trim($user['first_name'] . ' ' . $user['last_name']),
        'applicant'  => $userFull
    ]);
}



public function deleteUser(int $userId)
{
    if (!$this->isAdmin()) {
        return redirect()->to('/login')->with('error', 'Unauthorized access.');
    }

    $userModel = new \App\Models\UserModel();

    if ($userModel->deleteUser($userId)) {
        return redirect()->back()->with('success', 'User deleted successfully.');
    } else {
        return redirect()->back()->with('error', 'Failed to delete user.');
    }
}

}

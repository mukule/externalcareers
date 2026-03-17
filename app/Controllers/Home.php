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
    $totalApplicants        = $this->userModel->countApplicants();
    $totalJobs              = $this->jobModel->countTotalJobs();
    $openJobs               = $this->jobModel->countOpenJobs();
    $totalApplications      = $this->jobApplicationModel->countApplications();
    $qualifiedApplications  = $this->jobApplicationModel->countQualifiedApplications();

    // Latest 5 applicants only (important for performance)
    $latestApplicants = $this->userModel
        ->where('role', 'applicant')
        ->where('active', 1)
        ->orderBy('created_at', 'DESC')
        ->findAll();

    // Jobs with application counts
    $jobsWithCounts = $this->jobApplicationModel
        ->getJobsWithApplicationCounts();

    return view('dashboard/admin', [
        'title'                  => 'Admin Dashboard',
        'totalApplicants'        => $totalApplicants,
        'totalJobs'              => $totalJobs,
        'openJobs'               => $openJobs,
        'totalApplications'      => $totalApplications,
        'qualifiedApplications'  => $qualifiedApplications,
        'latestApplicants'       => $latestApplicants,
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
}

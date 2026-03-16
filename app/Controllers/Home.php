<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\JobModel;
use App\Models\JobTypeModel;
use App\Models\JobApplicationModel;


class Home extends BaseController
{
    protected $userModel;
    protected $jobModel;
    protected $jobTypeModel;
    protected $jobApplicationModel;
    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->jobModel = new \App\Models\JobModel();
        $this->jobTypeModel = new JobTypeModel();
        $this->jobApplicationModel = new JobApplicationModel();
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
    $jobModel = $this->jobModel;
    $jobTypeId = $this->request->getGet('type');
    $today = date('Y-m-d');

    $jobsQuery = $jobModel
        ->select('jobs.*, job_types.display_name AS job_type_name, education_levels.name AS education_name')
        ->join('job_types', 'job_types.id = jobs.job_type_id', 'left')
        ->join('education_levels', 'education_levels.id = jobs.min_education_level_id', 'left')
        ->where('jobs.active', 1);

    if ($jobTypeId) {
        $jobsQuery->where('jobs.job_type_id', $jobTypeId);
    }

    $jobs = $jobsQuery->orderBy('jobs.date_open', 'ASC')->findAll();

    // Filter only currently open jobs
    foreach ($jobs as $key => &$job) {
        if ($today < $job['date_open'] || $today > $job['date_close']) {
            unset($jobs[$key]);
        } else {
            $job['status'] = 'Open';
        }
    }

    $jobTypes = model(\App\Models\JobTypeModel::class)
        ->where('active', 1)
        ->findAll();

    return view('pages/index', [
        'title' => $this->data['app_name'],
        'jobs' => $jobs,
        'jobTypes' => $jobTypes
    ]);
}



public function jobDetail($uuid = null)
{
    if (!$uuid) {
        return redirect()->to('/')->with('error', 'Job not specified.');
    }

    $job = $this->jobModel
        ->select('jobs.*, job_types.display_name AS job_type_name, education_levels.name AS education_name')
        ->join('job_types', 'job_types.id = jobs.job_type_id', 'left')
        ->join('education_levels', 'education_levels.id = jobs.min_education_level_id', 'left')
        ->where('jobs.uuid', $uuid)
        ->where('jobs.active', 1)
        ->first();

    if (!$job) {
        return redirect()->to('/')->with('error', 'Job not found or inactive.');
    }

    
    $today = date('Y-m-d');
    if ($today < $job['date_open']) {
        $job['status'] = 'Upcoming';
    } elseif ($today > $job['date_close']) {
        $job['status'] = 'Closed';
    } else {
        $job['status'] = 'Open';
    }

    
    $requirements = get_job_application_requirements($job['id']);

    return view('pages/job_detail', [
        'title'        => $job['name'] . ' - Job Details',
        'job'          => $job,
        'requirements' => $requirements['requirements'] ?? []  
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

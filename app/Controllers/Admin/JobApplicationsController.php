<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\JobModel;
use App\Models\JobApplicationModel;

class JobApplicationsController extends BaseController
{
    protected $jobModel;
    protected $jobApplicationModel;

    public function __construct()
    {
        $this->jobModel            = new JobModel();
        $this->jobApplicationModel = new JobApplicationModel();
    }

   

    public function index()
    {
        $filters = [
            'name' => $this->request->getGet('name'),
            'ref_no' => $this->request->getGet('ref_no'),
            'job_type_id' => $this->request->getGet('job_type_id'),
            'discipline_id' => $this->request->getGet('discipline_id')
        ];

        
        $filters = array_filter($filters, fn($value) => $value !== null && $value !== '');

       
        $perPage = 20;
        $currentPage = (int) $this->request->getGet('page') ?: 1;
        $offset = ($currentPage - 1) * $perPage;

        
        $jobsData = $this->jobApplicationModel->getJobsWithApplicationCountsOnly($filters, $perPage, $offset);

       
        $totalJobs = $this->jobApplicationModel->countJobsWithFilters($filters);
        $totalPages = ceil($totalJobs / $perPage);

        
        $jobTypes = model(\App\Models\JobTypeModel::class)->findAll();
        $disciplines = model(\App\Models\JobDisciplineModel::class)->findAll();

        return view('admin/jobs_applications', [
            'title'        => 'Job Applications',
            'jobs'         => $jobsData,
            'filters'      => $filters,
            'currentPage'  => $currentPage,
            'perPage'      => $perPage,
            'totalPages'   => $totalPages,
            'jobTypes'     => $jobTypes,
            'disciplines'  => $disciplines
        ]);
    }


    
    public function show($uuid)
{
    $job = $this->jobModel->where('uuid', $uuid)->first();

    if (!$job) {
        return redirect()->back()->with('error', 'Job not found.');
    }

    // ✅ Load Gender Model
    $genderModel = new \App\Models\GenderModel();

    // Get filters from GET query parameters
    $filters = [
        'job_id'        => $job['id'],
        'user_name'     => $this->request->getGet('user_name'),
        'email'         => $this->request->getGet('email'),
        'job_ref'       => $this->request->getGet('job_ref'),
        'qualification' => $this->request->getGet('qualification'), 
        'national_id'   => $this->request->getGet('national_id'),
        'gender'        => $this->request->getGet('gender'),
        'status'        => $this->request->getGet('status'),
    ];

    // Fetch applications
    $applications = $this->jobApplicationModel
        ->getApplicationsByJobWithUsers($filters);

    // Export if requested
    if ($this->request->getGet('export') === 'csv') {
        return $this->exportApplicationsToCSV($job['name'], $applications);
    }

    return view('admin/job_applications', [
        'title'        => $job['name'] . ' - Applications',
        'job'          => $job,
        'applications' => $applications,
        'filters'      => $filters,
        'genders'      => $genderModel->getActive(), // ✅ PASS THIS
    ]);
}



   
public function updateStatus($applicationId)
{
    $status = $this->request->getPost('status');

    // Mapping DB value => readable label
    $statusLabels = [
        'pending'     => 'Pending',
        'reviewed'    => 'Reviewed',
        'shortlisted' => 'Shortlisted'
    ];

    $statusLower = strtolower($status);

    // Validate status
    if (!array_key_exists($statusLower, $statusLabels)) {
        return redirect()->back()->with('error', 'Invalid status.');
    }

    // Save to DB
    $this->jobApplicationModel->updateStatus($applicationId, $statusLower);

    // Optional: return the readable name
    $readableStatus = $statusLabels[$statusLower];

    return redirect()->back()->with('success', "Application {$readableStatus}.");
}


protected function exportApplicationsToCSV(string $jobName, array $applications)
{
    $filename = 'applications_' . strtolower(str_replace(' ', '_', $jobName)) . '_' . date('Ymd_His') . '.csv';

    // Set headers for CSV download
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Pragma: no-cache');
    header('Expires: 0');

    $output = fopen('php://output', 'w');

    // CSV header row
    fputcsv($output, ['#', 'Name', 'Email', 'Reference No', 'Qualification', 'Status', 'Date Applied']);

    foreach ($applications as $index => $app) {
        fputcsv($output, [
            $index + 1,
            ($app['first_name'] ?? '-') . ' ' . ($app['last_name'] ?? '-'),
            $app['email'] ?? '-',
            $app['ref_no'] ?? '-',
            ucfirst($app['qualification'] ?? '-'),
            ucfirst($app['status'] ?? '-'),
            date('d M, Y', strtotime($app['created_at'] ?? ''))
        ]);
    }

    fclose($output);
    exit; // Stop further execution
}


}

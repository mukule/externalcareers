<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\JobModel;
use App\Models\JobApplicationModel;

class JobApplicationController extends BaseController
{
    protected $jobModel;
    protected $applicationModel;

    public function __construct()
    {
        $this->jobModel = new JobModel();
        $this->applicationModel = new JobApplicationModel();
    }

  
    public function apply($jobUuid)
{
    $userId = session()->get('user_id');

    if (!$userId) {
        return redirect()->to('/login')->with('error', 'You must be logged in to apply.');
    }

    $job = $this->jobModel->where('uuid', $jobUuid)->first();

    if (!$job) {
        return redirect()->back()->with('error', 'Job not found.');
    }

    $today = date('Y-m-d');
    if ($today < $job['date_open'] || $today > $job['date_close']) {
        return redirect()->back()->with('error', 'The job is not currently open.');
    }

    // Check if user already applied
    $existing = $this->applicationModel
        ->where('user_id', $userId)
        ->where('job_id', $job['id'])
        ->first();

    // Determine qualification and reason
    $qualifyResult = $this->jobModel->doesUserQualifyWithReason($userId, $job);

    $data = [
        'user_id'               => $userId,
        'job_id'                => $job['id'],
        'qualification'         => $qualifyResult['qualifies'] ? 'qualify' : 'not qualify',
        'disqualification_reason' => $qualifyResult['reason'] ?: null,
        'status'                => 'pending',
    ];

    if ($existing) {
        // Update existing application
        $this->applicationModel->update($existing['id'], $data);
        $this->applicationModel->setRefNo($existing['id']);
        $applicationRef = $this->applicationModel->find($existing['id'])['ref_no'];

        $message = view('emails/application_confirmation', [
            'first_name' => session()->get('first_name'),
            'job_name'   => $job['name'],
            'ref_no'     => $applicationRef
        ]);

        send_email(session()->get('email'), 'Application Updated', $message);

        return redirect()->back()->with('success', 'Your application has been updated.');
    } else {
        // Insert new application
        $insertId = $this->applicationModel->insert($data);
        $this->applicationModel->setRefNo($insertId);
        $applicationRef = $this->applicationModel->find($insertId)['ref_no'];

        $message = view('emails/application_confirmation', [
            'first_name' => session()->get('first_name'),
            'job_name'   => $job['name'],
            'ref_no'     => $applicationRef
        ]);

        send_email(session()->get('email'), 'Application Submitted', $message);

        return redirect()->back()->with('success', 'Application submitted successfully.');
    }
}




    /**
     * View user's applications
     */
    public function myApplications()
    {
        $userId = session()->get('user_id');

        if (!$userId) {
            return redirect()->to('/login')->with('error', 'You must be logged in to view your applications.');
        }

        $applications = $this->applicationModel
            ->select('job_applications.*, jobs.name AS job_name, jobs.reference_no, jobs.date_open, jobs.date_close, job_types.display_name AS job_type')
            ->join('jobs', 'jobs.id = job_applications.job_id', 'left')
            ->join('job_types', 'job_types.id = jobs.job_type_id', 'left')
            ->where('job_applications.user_id', $userId)
            ->orderBy('job_applications.created_at', 'DESC')
            ->findAll();

        return view('applications/my_applications', [
            'title'        => 'My Applications',
            'applications' => $applications
        ]);
    }

  
    public function detail($uuid)
    {
        $application = $this->applicationModel
            ->select('job_applications.*, jobs.name AS job_name, jobs.reference_no, jobs.date_open, jobs.date_close, job_types.display_name AS job_type')
            ->join('jobs', 'jobs.id = job_applications.job_id', 'left')
            ->join('job_types', 'job_types.id = jobs.job_type_id', 'left')
            ->where('job_applications.uuid', $uuid)
            ->first();

        if (!$application) {
            return redirect()->back()->with('error', 'Application not found.');
        }

        return view('applications/detail', [
            'title'       => 'Application Detail',
            'application' => $application
        ]);
    }
}

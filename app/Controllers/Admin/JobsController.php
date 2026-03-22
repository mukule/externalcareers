<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\JobModel;
use App\Models\JobTypeModel;
use App\Models\EducationLevelModel;
use App\Models\JobSpecialityModel;
use App\Models\FieldOfStudyModel;
use App\Models\JobDisciplineModel;

class JobsController extends BaseController
{
    protected $jobModel;
    protected $jobType;
    protected $educationLevel;
    protected $jobSpeciality;
    protected $fieldsOfStudy;
    protected $jobDiscipline;

    protected $validationRules = [
        'name'                      => 'required|string|max_length[255]',
        'reference_no'              => 'required|string|max_length[100]',
        'job_type_id'               => 'required|integer',
        'discipline_id'             => 'required|integer', // job discipline
        'job_summary'               => 'required|string',
        'job_description'           => 'required|string',
        'posts_needed'              => 'required|integer',
        'reports_to'                => 'permit_empty|string|max_length[255]',
        'date_open'                 => 'required|valid_date',
        'date_close'                => 'required|valid_date',
        'min_education_level_id'    => 'required|integer',
        'work_experience_years'     => 'required|decimal',
        'certification_required'    => 'permit_empty|in_list[0,1]',
        'membership_required'       => 'permit_empty|in_list[0,1]',
        'higher_education_required' => 'permit_empty|in_list[0,1]',
        'fields_of_study'           => 'required|is_array'
    ];

    public function __construct()
    {
        $this->jobModel        = new JobModel();
        $this->jobType         = new JobTypeModel();
        $this->educationLevel  = new EducationLevelModel();
        $this->jobSpeciality   = new JobSpecialityModel();
        $this->fieldsOfStudy   = new FieldOfStudyModel();
        $this->jobDiscipline   = new JobDisciplineModel();
    }

    /** List all jobs */
   
public function index()
{
    $perPage = 20; // number of jobs per page
    $page = (int) $this->request->getGet('page') ?: 1;

    // Get filter values from GET parameters
    $filters = [
        'name'          => $this->request->getGet('name'),
        'ref_no'        => $this->request->getGet('ref_no'),
        'job_type_id'   => $this->request->getGet('job_type_id'),
        'discipline_id' => $this->request->getGet('discipline_id')
    ];

    // Remove empty filters
    $filters = array_filter($filters, fn($v) => $v !== null && $v !== '');

    // Base query
    $builder = $this->jobModel
        ->select('jobs.*, job_types.display_name AS job_type_name, education_levels.name AS education_name, jobs.discipline_id')
        ->join('job_types', 'job_types.id = jobs.job_type_id', 'left')
        ->join('education_levels', 'education_levels.id = jobs.min_education_level_id', 'left')
        ->orderBy('jobs.created_at', 'DESC');

    // Apply filters
    if (!empty($filters['name'])) {
        $builder->like('jobs.name', $filters['name']);
    }
    if (!empty($filters['ref_no'])) {
        $builder->like('jobs.reference_no', $filters['ref_no']);
    }
    if (!empty($filters['job_type_id'])) {
        $builder->where('jobs.job_type_id', $filters['job_type_id']);
    }
    if (!empty($filters['discipline_id'])) {
        $builder->where('jobs.discipline_id', $filters['discipline_id']);
    }

    // Paginate after applying filters
    $jobs = $builder->paginate($perPage, 'default', $page);
    $pager = $this->jobModel->pager;

    $today = date('Y-m-d');
    foreach ($jobs as &$job) {
        $job['status'] = ($today < $job['date_open'])
                            ? 'Upcoming'
                            : (($today > $job['date_close']) ? 'Closed' : 'Open');
        $job['fields_of_study'] = $this->jobSpeciality->getByJob($job['id']);
    }

    // Load job types and disciplines for filters
    $jobTypes = $this->jobType->findAll();
    $disciplines = $this->jobDiscipline->findAll();

    return view('admin/jobs', [
        'title'       => 'Job Vacancies',
        'jobs'        => $jobs,
        'pager'       => $pager,
        'currentPage' => $page,
        'perPage'     => $perPage,
        'filters'     => $filters,
        'jobTypes'    => $jobTypes,
        'disciplines' => $disciplines,
    ]);
}
   
    public function toggle($id)
    {
        $job = $this->jobModel->find($id);

        if (!$job) {
            return redirect()->back()->with('error', 'Job not found.');
        }

        // Toggle the active status
        $newStatus = $job['active'] ? 0 : 1;
        $this->jobModel->update($id, ['active' => $newStatus]);

        $message = $newStatus ? 'Job published successfully.' : 'Job unpublished successfully.';
        return redirect()->back()->with('success', $message);
    }

    /** Show form for creating a new job */
    public function create()
    {
        return view('admin/create_job', [
            'title'            => 'Create Job Vacancy',
            'action'           => base_url('admin/jobs/store'),
            'jobTypes'         => $this->jobType->where('active', 1)->findAll(),
            'educationLevels'  => $this->educationLevel->where('active', 1)->findAll(),
            'fieldsOfStudy'    => $this->fieldsOfStudy->where('active', 1)->findAll(),
            'jobDisciplines'   => $this->jobDiscipline->where('active', 1)->findAll(),
        ]);
    }

    /** Store new job */
    public function store()
    {
        $data = $this->request->getPost();
        $data['reference_no'] = trim($data['reference_no']);

        $rules = $this->validationRules;
        $rules['reference_no'] .= '|is_unique[jobs.reference_no]';

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', $this->validator->getErrors());
        }

        $data['certification_required']    = $data['certification_required'] ?? 0;
        $data['membership_required']       = $data['membership_required'] ?? 0;
        $data['higher_education_required'] = $data['higher_education_required'] ?? 0;
        $data['discipline_id']             = $data['discipline_id'] ?? null;
        $data['active']                    = 0;
        $data['created_by']                = session()->get('user_id');

        $jobId = $this->jobModel->insert($data);

        // Assign fields of study
        $fieldIds = $data['fields_of_study'] ?? [];
        if (!empty($fieldIds)) {
            $this->jobSpeciality->assignFields($jobId, $fieldIds);
        }

        return redirect()->to('admin/jobs')->with('success', 'Job created successfully.');
    }

    /** Show edit form */
    public function edit($uuid)
    {
        $job = $this->jobModel->where('uuid', $uuid)->first();
        if (!$job) {
            return redirect()->back()->with('error', 'Job not found.');
        }

        $selectedFields = array_column($this->jobSpeciality->getByJob($job['id']), 'id');

        return view('admin/create_job', [
            'title'            => 'Edit Job Vacancy',
            'action'           => base_url('admin/jobs/update/' . $uuid),
            'job'              => $job,
            'jobTypes'         => $this->jobType->where('active', 1)->findAll(),
            'educationLevels'  => $this->educationLevel->where('active', 1)->findAll(),
            'fieldsOfStudy'    => $this->fieldsOfStudy->where('active', 1)->findAll(),
            'jobDisciplines'   => $this->jobDiscipline->where('active', 1)->findAll(),
            'jobFields'        => $selectedFields
        ]);
    }

    /** Update an existing job */
    public function update($uuid)
    {
        $job = $this->jobModel->where('uuid', $uuid)->first();
        if (!$job) {
            return redirect()->back()->with('error', 'Job not found.');
        }

        $data = $this->request->getPost();
        $data['reference_no'] = trim($data['reference_no']);

        $rules = $this->validationRules;
        $rules['reference_no'] = 'required|string|max_length[100]|is_unique[jobs.reference_no,id,' . $job['id'] . ']';

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', $this->validator->getErrors());
        }

        $data['certification_required']    = $data['certification_required'] ?? 0;
        $data['membership_required']       = $data['membership_required'] ?? 0;
        $data['higher_education_required'] = $data['higher_education_required'] ?? 0;
        $data['discipline_id']             = $data['discipline_id'] ?? null;

        $this->jobModel->update($job['id'], $data);

        // Update fields of study
        $fieldIds = $data['fields_of_study'] ?? [];
        $this->jobSpeciality->assignFields($job['id'], $fieldIds);

        return redirect()->to('admin/jobs')->with('success', 'Job updated successfully.');
    }

    /** Show detailed view of a job */
    public function show($uuid)
    {
        $job = $this->jobModel
            ->select('jobs.*, job_types.display_name AS job_type_name, education_levels.name AS education_name, jobs.discipline_id')
            ->join('job_types', 'job_types.id = jobs.job_type_id', 'left')
            ->join('education_levels', 'education_levels.id = jobs.min_education_level_id', 'left')
            ->where('jobs.uuid', $uuid)
            ->first();

        if (!$job) {
            return redirect()->back()->with('error', 'Job not found.');
        }

        $today = date('Y-m-d');
        $job['status'] = ($today < $job['date_open']) ? 'Upcoming' : (($today > $job['date_close']) ? 'Closed' : 'Open');

        $job['fields_of_study'] = $this->jobSpeciality->getByJob($job['id']);

        return view('admin/job_detail', [
            'title' => 'Job Details: ' . $job['name'],
            'job'   => $job
        ]);
    }
}
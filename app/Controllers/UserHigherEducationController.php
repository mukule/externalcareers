<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UserHigherEducationModel;
use App\Models\EducationLevelModel;

class UserHigherEducationController extends BaseController
{
    protected $educationModel;
    protected $levelModel;

    public function __construct()
    {
        $this->educationModel = new UserHigherEducationModel();
        $this->levelModel     = new EducationLevelModel();
    }

    public function index()
    {
        $userId = session()->get('user_id');

        return view('applicant/higher_education', [
            'title'       => 'Higher Education',
            'currentStep' => 4,
            'educations'  => $this->educationModel->getByUserId($userId),
            'levels'      => $this->levelModel->where('active', 1)
                                ->orderBy('index', 'ASC')->findAll(),
        ]);
    }

    public function create()
    {
        return view('applicant/higher_education_form', [
            'title'       => 'Add Higher Education',
            'currentStep' => 4,
            'action'      => base_url('applicant/higher-education/store'),
            'levels'      => $this->levelModel->where('active', 1)
                                ->orderBy('index', 'ASC')->findAll(),
            'classes' => [
                'First Class',
                'Second Class Upper',
                'Second Class Lower',
                'Pass',
                'Distinction',
                'Credit'
            ],
        ]);
    }

    /**
     * STORE
     */
    public function store()
    {
        $userId = session()->get('user_id');
        $data   = $this->request->getPost();

        $validation = \Config\Services::validation();
        $validation->setRules([
            'institution_name'   => 'required|string|max_length[255]',
            'course_name'        => 'required|string|max_length[255]',
            'education_level_id' => 'required|integer',
            'class_attained'     => 'permit_empty|string|max_length[50]',
            'date_started'       => 'permit_empty|numeric|exact_length[4]',
            'date_ended'         => 'permit_empty|string|max_length[10]',
            'certificate'        => 'permit_empty|max_size[certificate,1024]|ext_in[certificate,pdf]',
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('error', $validation->getErrors());
        }

        $start = $data['date_started'] ?? null;
        $end   = $data['date_ended'] ?? null;
        $currentYear = (int) date('Y');

        $errors = [];

        if ($start && (int)$start > $currentYear) {
            $errors['date_started'] = 'Start year cannot be in the future.';
        }

        if ($end && $end !== 'present' && (int)$end > $currentYear) {
            $errors['date_ended'] = 'End year cannot be in the future.';
        }

        if ($start && $end && $end !== 'present' && (int)$end < (int)$start) {
            $errors['date_ended'] = 'End year cannot be earlier than start year.';
        }

        if ($errors) {
            return redirect()->back()->withInput()->with('error', $errors);
        }

        // FILE (1MB MAX)
        $certificatePath = null;
        $file = $this->request->getFile('certificate');

        if ($file && $file->getError() !== 4) {

            if (!$file->isValid()) {
                return redirect()->back()->withInput()->with('error', [
                    'certificate' => 'Invalid file upload.'
                ]);
            }

            if ($file->getSize() > (1 * 1024 * 1024)) {
                return redirect()->back()->withInput()->with('error', [
                    'certificate' => 'File must not exceed 1MB.'
                ]);
            }

            if ($file->getMimeType() !== 'application/pdf') {
                return redirect()->back()->withInput()->with('error', [
                    'certificate' => 'Only PDF files allowed.'
                ]);
            }

            $path = ROOTPATH . 'public/uploads/certs/';
            if (!is_dir($path)) mkdir($path, 0777, true);

            $certificatePath = $file->getRandomName();
            $file->move($path, $certificatePath);
        }

        $this->educationModel->insert([
            'user_id'            => $userId,
            'institution_name'   => $data['institution_name'],
            'course_name'        => $data['course_name'],
            'education_level_id' => $data['education_level_id'],
            'class_attained'     => $data['class_attained'] ?? null,

            // YEARS → FULL DATE
            'date_started'       => $start ? $start . '-01-01' : null,
            'date_ended'         => ($end && $end !== 'present') ? $end . '-12-31' : null,

            'certificate'        => $certificatePath,
            'active'             => 1,
        ]);

        return redirect()->to('/applicant/higher-education')
            ->with('success', 'Higher education added successfully.');
    }

    /**
     * EDIT
     */
    public function edit($uuid)
    {
        $userId = session()->get('user_id');

        $record = $this->educationModel
            ->where(['uuid' => $uuid, 'user_id' => $userId])
            ->first();

        if (!$record) {
            return redirect()->back()->with('error', 'Record not found.');
        }

        // CONVERT DATE → YEAR FOR UI
        if (!empty($record['date_started'])) {
            $record['date_started'] = date('Y', strtotime($record['date_started']));
        }

        if (!empty($record['date_ended'])) {
            $record['date_ended'] = date('Y', strtotime($record['date_ended']));
        } else {
            $record['date_ended'] = 'present';
        }

        return view('applicant/higher_education_form', [
            'title'       => 'Edit Higher Education',
            'currentStep' => 4,
            'action'      => base_url('applicant/higher-education/update'),
            'edu'         => $record,
            'levels'      => $this->levelModel->where('active', 1)
                                ->orderBy('index', 'ASC')->findAll(),
            'classes'     => [
                'First Class',
                'Second Class Upper',
                'Second Class Lower',
                'Pass',
                'Distinction',
                'Credit'
            ],
        ]);
    }

    /**
     * UPDATE
     */
    public function update()
    {
        $userId = session()->get('user_id');
        $data   = $this->request->getPost();

        $record = $this->educationModel
            ->where(['id' => $data['id'], 'user_id' => $userId])
            ->first();

        if (!$record) {
            return redirect()->back()->withInput()->with('error', 'Record not found.');
        }

        $start = $data['date_started'] ?? null;
        $end   = $data['date_ended'] ?? null;
        $currentYear = (int) date('Y');

        $errors = [];

        if ($start && (int)$start > $currentYear) {
            $errors['date_started'] = 'Start year cannot be in future.';
        }

        if ($end && $end !== 'present' && (int)$end > $currentYear) {
            $errors['date_ended'] = 'End year cannot be in future.';
        }

        if ($start && $end && $end !== 'present' && (int)$end < (int)$start) {
            $errors['date_ended'] = 'End year invalid.';
        }

        if ($errors) {
            return redirect()->back()->withInput()->with('error', $errors);
        }

        $file = $this->request->getFile('certificate');
        $certificateName = $record['certificate'];

        if ($file && $file->getError() !== 4) {

            if (!$file->isValid()) {
                return redirect()->back()->withInput()->with('error', [
                    'certificate' => 'Invalid file upload.'
                ]);
            }

            if ($file->getSize() > (1 * 1024 * 1024)) {
                return redirect()->back()->withInput()->with('error', [
                    'certificate' => 'File must not exceed 1MB.'
                ]);
            }

            if ($file->getMimeType() !== 'application/pdf') {
                return redirect()->back()->withInput()->with('error', [
                    'certificate' => 'Only PDF files allowed.'
                ]);
            }

            $path = ROOTPATH . 'public/uploads/certs/';
            if (!is_dir($path)) mkdir($path, 0777, true);

            if (!empty($record['certificate']) && file_exists($path . $record['certificate'])) {
                unlink($path . $record['certificate']);
            }

            $certificateName = $file->getRandomName();
            $file->move($path, $certificateName);
        }

        $this->educationModel->update($data['id'], [
            'institution_name'   => $data['institution_name'],
            'course_name'        => $data['course_name'],
            'education_level_id' => $data['education_level_id'],
            'class_attained'     => $data['class_attained'] ?? null,

            'date_started'       => $start ? $start . '-01-01' : null,
            'date_ended'         => ($end && $end !== 'present') ? $end . '-12-31' : null,

            'certificate'        => $certificateName,
        ]);

        return redirect()->to('/applicant/higher-education')
            ->with('success', 'Updated successfully.');
    }
}
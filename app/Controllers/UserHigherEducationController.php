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

    /**
     * List all higher education records
     */
    public function index()
    {
        $userId = session()->get('user_id');

        return view('applicant/higher_education', [
            'title'       => 'Higher Education',
            'currentStep' => 4,
            'educations'  => $this->educationModel->getByUserId($userId),
            'levels'      => $this->levelModel
                ->where('active', 1)
                ->orderBy('index', 'ASC')
                ->findAll(),
        ]);
    }

    /**
     * Show create form
     */
    public function create()
    {
        return view('applicant/higher_education_form', [
            'title'       => 'Add Higher Education',
            'currentStep' => 4,
            'action'      => base_url('applicant/higher-education/store'),
            'levels'      => $this->levelModel
                ->where('active', 1)
                ->orderBy('index', 'ASC')
                ->findAll(),
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
     * Store record
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
            'date_started'       => 'permit_empty|valid_date[Y-m-d]',
            'date_ended'         => 'permit_empty|valid_date[Y-m-d]',
            'certificate'        => 'permit_empty|max_size[certificate,2048]|ext_in[certificate,pdf]',
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('error', $validation->getErrors());
        }

        // =========================
        // DATE VALIDATION
        // =========================
        $start = $data['date_started'] ?? null;
        $end   = $data['date_ended'] ?? null;
        $today = date('Y-m-d');

        $errors = [];

        if ($start && $end && strtotime($end) < strtotime($start)) {
            $errors['date_ended'] = 'End date cannot be earlier than start date.';
        }

        if ($end && strtotime($end) > strtotime($today)) {
            $errors['date_ended'] = 'End date cannot be in the future.';
        }

        if (!empty($errors)) {
            return redirect()->back()->withInput()->with('error', $errors);
        }

        // =========================
        // FILE UPLOAD (CERTS)
        // =========================
        $certificatePath = null;

        $file = $this->request->getFile('certificate');

        if ($file && $file->getError() !== 4) {
            if ($file->isValid() && !$file->hasMoved()) {

                // extra safety (2MB)
                if ($file->getSize() > (2 * 1024 * 1024)) {
                    return redirect()
                        ->back()
                        ->withInput()
                        ->with('error', ['certificate' => 'File must not exceed 2MB.']);
                }

                $certificatePath = $file->getRandomName();
                $file->move(ROOTPATH . 'public/uploads/certs', $certificatePath);
            }
        }

        $this->educationModel->insert([
            'user_id'            => $userId,
            'institution_name'   => $data['institution_name'],
            'course_name'        => $data['course_name'],
            'education_level_id' => $data['education_level_id'],
            'class_attained'     => $data['class_attained'] ?? null,
            'date_started'       => $data['date_started'] ?? null,
            'date_ended'         => $data['date_ended'] ?? null,
            'certificate'        => $certificatePath,
            'active'             => 1,
        ]);

        return redirect()->to('/applicant/higher-education')
            ->with('success', 'Higher education added successfully.');
    }

    /**
     * Show edit form
     */
    public function edit($uuid)
    {
        $userId = session()->get('user_id');

        $record = $this->educationModel
            ->where(['uuid' => $uuid, 'user_id' => $userId])
            ->first();

        if (!$record) {
            return redirect()->back()->with('error', 'Education record not found.');
        }

        return view('applicant/higher_education_form', [
            'title'       => 'Edit Higher Education',
            'currentStep' => 4,
            'action'      => base_url('applicant/higher-education/update'),
            'edu'         => $record,
            'levels'      => $this->levelModel
                ->where('active', 1)
                ->orderBy('index', 'ASC')
                ->findAll(),
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
     * Update record
     */
    public function update()
    {
        $userId = session()->get('user_id');
        $data   = $this->request->getPost();

        $record = $this->educationModel
            ->where(['id' => $data['id'], 'user_id' => $userId])
            ->first();

        if (!$record) {
            return redirect()->back()->withInput()->with('error', 'Education record not found.');
        }

        $validation = \Config\Services::validation();
        $validation->setRules([
            'institution_name'   => 'required|string|max_length[255]',
            'course_name'        => 'required|string|max_length[255]',
            'education_level_id' => 'required|integer',
            'class_attained'     => 'permit_empty|string|max_length[50]',
            'date_started'       => 'permit_empty|valid_date[Y-m-d]',
            'date_ended'         => 'permit_empty|valid_date[Y-m-d]',
            'certificate'        => 'permit_empty|max_size[certificate,2048]|ext_in[certificate,pdf]',
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('error', $validation->getErrors());
        }

        // =========================
        // DATE VALIDATION
        // =========================
        $start = $data['date_started'] ?? null;
        $end   = $data['date_ended'] ?? null;
        $today = date('Y-m-d');

        $errors = [];

        if ($start && $end && strtotime($end) < strtotime($start)) {
            $errors['date_ended'] = 'End date cannot be earlier than start date.';
        }

        if ($end && strtotime($end) > strtotime($today)) {
            $errors['date_ended'] = 'End date cannot be in the future.';
        }

        if (!empty($errors)) {
            return redirect()->back()->withInput()->with('error', $errors);
        }

        try {
            $file = $this->request->getFile('certificate');
            $certificateName = $record['certificate'];

            if ($file && $file->getError() !== 4) {
                if ($file->isValid() && !$file->hasMoved()) {

                    if ($file->getSize() > (2 * 1024 * 1024)) {
                        return redirect()
                            ->back()
                            ->withInput()
                            ->with('error', ['certificate' => 'File must not exceed 2MB.']);
                    }

                    // delete old
                    if (!empty($record['certificate']) &&
                        file_exists(ROOTPATH . 'public/uploads/certs/' . $record['certificate'])) {
                        unlink(ROOTPATH . 'public/uploads/certs/' . $record['certificate']);
                    }

                    $certificateName = $file->getRandomName();
                    $file->move(ROOTPATH . 'public/uploads/certs', $certificateName);
                }
            }

            $this->educationModel->update($data['id'], [
                'institution_name'   => $data['institution_name'],
                'course_name'        => $data['course_name'],
                'education_level_id' => $data['education_level_id'],
                'class_attained'     => $data['class_attained'] ?? null,
                'date_started'       => $data['date_started'] ?? null,
                'date_ended'         => $data['date_ended'] ?? null,
                'certificate'        => $certificateName,
            ]);

        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }

        return redirect()->to('/applicant/higher-education')
            ->with('success', 'Education updated successfully.');
    }

    /**
     * Delete record
     */
    public function delete($uuid)
    {
        $userId = session()->get('user_id');

        $record = $this->educationModel
            ->where(['uuid' => $uuid, 'user_id' => $userId])
            ->first();

        if (!$record) {
            return redirect()->back()->with('error', 'Education record not found.');
        }

        try {
            if (!empty($record['certificate']) &&
                file_exists(ROOTPATH . 'public/uploads/certs/' . $record['certificate'])) {
                unlink(ROOTPATH . 'public/uploads/certs/' . $record['certificate']);
            }

            $this->educationModel->delete($record['id']);

        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }

        return redirect()->to('/applicant/higher-education')
            ->with('success', 'Education record deleted successfully.');
    }
}
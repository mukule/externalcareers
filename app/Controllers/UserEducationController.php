<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UserEducationModel;
use App\Models\EducationLevelModel;
use App\Models\FieldOfStudyModel;

class UserEducationController extends BaseController
{
    protected $educationModel;
    protected $levelModel;
    protected $fieldOfStudyModel;

    public function __construct()
    {
        $this->educationModel    = new UserEducationModel();
        $this->levelModel        = new EducationLevelModel();
        $this->fieldOfStudyModel = new FieldOfStudyModel();
    }

    /**
     * LIST
     */
    public function index()
    {
        $userId = session()->get('user_id');

        return view('applicant/education', [
            'title'       => 'My Education',
            'educations'  => $this->educationModel->getByUser($userId),
            'currentStep' => 3
        ]);
    }

    /**
     * CREATE
     */
    public function create()
    {
        return view('applicant/education_form', [
            'title'           => 'Add New Education',
            'action'          => base_url('applicant/education/store'),
            'levels'          => $this->levelModel->where('active', 1)->orderBy('index', 'ASC')->findAll(),
            'certifications'  => [
                '8-4-4' => ['KCPE', 'KCSE'],
                'CBC'   => ['CBC Primary', 'CBC Junior', 'CBC Senior']
            ],
            'fieldsOfStudy'   => $this->fieldOfStudyModel->where('active', 1)->orderBy('name', 'ASC')->findAll(),
            'currentStep'     => 3
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
            'level_id'    => 'required|integer',
            'institution' => 'required|string|max_length[255]',
            'course'      => 'required|string|max_length[255]',
            'grade'       => 'permit_empty|string|max_length[50]',
            'start_year'  => 'permit_empty|integer|exact_length[4]',
            'end_year'    => 'permit_empty|integer|exact_length[4]',
            'certificate' => 'permit_empty|max_size[certificate,1024]|ext_in[certificate,pdf]',
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('error', $validation->getErrors());
        }

        $startYear   = (int) ($data['start_year'] ?? 0);
        $endYear     = (int) ($data['end_year'] ?? 0);
        $currentYear = (int) date('Y');

        $errors = [];

        if ($startYear && $endYear && $endYear < $startYear) {
            $errors['end_year'] = 'End year cannot be earlier than start year.';
        }

        if ($startYear && $startYear > $currentYear) {
            $errors['start_year'] = 'Start year cannot be in the future.';
        }

        if ($endYear && $endYear > $currentYear) {
            $errors['end_year'] = 'End year cannot be in the future.';
        }

        if (!empty($errors)) {
            return redirect()->back()->withInput()->with('error', $errors);
        }

        // FILE UPLOAD (1MB max)
        $certificatePath = null;
        $file = $this->request->getFile('certificate');

        if ($file && $file->getError() !== 4) {

            if (!$file->isValid()) {
                return redirect()->back()->withInput()->with('error', [
                    'certificate' => 'Invalid file upload.'
                ]);
            }

            if ($file->getSize() > (1024 * 1024)) {
                return redirect()->back()->withInput()->with('error', [
                    'certificate' => 'File must not exceed 1MB.'
                ]);
            }

            $certificatePath = $file->getRandomName();
            $file->move(ROOTPATH . 'public/uploads/certs', $certificatePath);
        }

        $this->educationModel->insert([
            'user_id'     => $userId,
            'level_id'    => $data['level_id'],
            'institution' => $data['institution'],
            'course'      => $data['course'],
            'field_id'    => $data['field_id'] ?? null,
            'grade'       => $data['grade'] ?? null,
            'start_year'  => $data['start_year'] ?? null,
            'end_year'    => $data['end_year'] ?? null,
            'certificate' => $certificatePath,
            'active'      => 1,
        ]);

        return redirect()->to('/applicant/education')
            ->with('success', 'Education added successfully.');
    }

    /**
     * EDIT
     */
    public function edit($uuid)
    {
        $userId = session()->get('user_id');

        $education = $this->educationModel
            ->where(['uuid' => $uuid, 'user_id' => $userId])
            ->first();

        if (!$education) {
            return redirect()->back()->with('error', 'Education record not found.');
        }

        return view('applicant/education_form', [
            'title'          => 'Edit Education',
            'action'         => base_url('applicant/education/update'),
            'edu'            => $education,
            'levels'         => $this->levelModel->where('active', 1)->orderBy('index', 'ASC')->findAll(),
            'certifications' => [
                '8-4-4' => ['KCPE', 'KCSE'],
                'CBC'   => ['CBC Primary', 'CBC Junior', 'CBC Senior']
            ],
            'fieldsOfStudy'  => $this->fieldOfStudyModel->where('active', 1)->orderBy('name', 'ASC')->findAll(),
            'currentStep'    => 3
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
            return redirect()->back()->with('error', 'Education record not found.');
        }

        $startYear   = (int) ($data['start_year'] ?? 0);
        $endYear     = (int) ($data['end_year'] ?? 0);
        $currentYear = (int) date('Y');

        if ($startYear && $endYear && $endYear < $startYear) {
            return redirect()->back()->withInput()->with('error', [
                'end_year' => 'End year cannot be earlier than start year.'
            ]);
        }

        if ($endYear && $endYear > $currentYear) {
            return redirect()->back()->withInput()->with('error', [
                'end_year' => 'End year cannot be in the future.'
            ]);
        }

        // FILE (1MB max)
        $file = $this->request->getFile('certificate');
        $certificateName = $record['certificate'];

        if ($file && $file->getError() !== 4) {

            if ($file->getSize() > (1024 * 1024)) {
                return redirect()->back()->withInput()->with('error', [
                    'certificate' => 'File must not exceed 1MB.'
                ]);
            }

            if (!empty($record['certificate']) &&
                file_exists(ROOTPATH . 'public/uploads/certs/' . $record['certificate'])) {
                unlink(ROOTPATH . 'public/uploads/certs/' . $record['certificate']);
            }

            $certificateName = $file->getRandomName();
            $file->move(ROOTPATH . 'public/uploads/certs', $certificateName);
        }

        $this->educationModel->update($data['id'], [
            'level_id'    => $data['level_id'],
            'institution' => $data['institution'],
            'course'      => $data['course'],
            'field_id'    => $data['field_id'] ?? null,
            'grade'       => $data['grade'] ?? null,
            'start_year'  => $data['start_year'] ?? null,
            'end_year'    => $data['end_year'] ?? null,
            'certificate' => $certificateName,
        ]);

        return redirect()->to('/applicant/education')
            ->with('success', 'Education updated successfully.');
    }

    /**
     * DELETE
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

        if (!empty($record['certificate']) &&
            file_exists(ROOTPATH . 'public/uploads/certs/' . $record['certificate'])) {
            unlink(ROOTPATH . 'public/uploads/certs/' . $record['certificate']);
        }

        $this->educationModel->delete($record['id']);

        return redirect()->to('/applicant/education')
            ->with('success', 'Education deleted successfully.');
    }
}
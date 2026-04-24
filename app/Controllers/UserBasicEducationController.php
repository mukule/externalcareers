<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UserBasicEducationModel;
use App\Models\EducationLevelModel;

class UserBasicEducationController extends BaseController
{
    protected $educationModel;
    protected $levelModel;

    public function __construct()
    {
        $this->educationModel = new UserBasicEducationModel();
        $this->levelModel     = new EducationLevelModel();
    }

    /**
     * List all basic education records
     */
    public function index()
    {
        $userId = session()->get('user_id');

        return view('applicant/basic_education', [
            'title'      => 'Basic Education',
            'currentStep' => 3,
            'educations' => $this->educationModel->getByUserId($userId),
            'levels'     => $this->levelModel->where('active', 1)->orderBy('index', 'ASC')->findAll(),
        ]);
    }

    /**
     * Show create form
     */
    public function create()
    {
        return view('applicant/basic_education_form', [
            'title'          => 'Add Basic Education',
             'currentStep' => 3,
            'action'         => base_url('applicant/basic-education/store'),
            'levels'         => $this->levelModel->where('active', 1)->orderBy('index', 'ASC')->findAll(),
            'certifications' => [
                'KCPE' => 'Kenya Certificate of Primary Education (KCPE)',
                'KCSE' => 'Kenya Certificate of Secondary Education (KCSE)',
            ],
            'grades' => ['A','A-','B+','B','B-','C+','C','C-','D+','D','D-','E'],
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
        'school_name'    => 'required|string|max_length[255]',
        'certification'  => 'required|string|max_length[100]',
        'grade_attained' => 'permit_empty|string|max_length[50]',
        'date_started'   => 'permit_empty|numeric',
        'date_ended'     => 'permit_empty|string|max_length[10]',
        'certificate'    => 'permit_empty|max_size[certificate,1024]|ext_in[certificate,pdf]',
    ]);

    if (!$validation->withRequest($this->request)->run()) {
        return redirect()->back()->withInput()->with('error', $validation->getErrors());
    }

    // =========================
    // YEAR VALIDATION
    // =========================
    $start = $data['date_started'] ?? null;
    $end   = $data['date_ended'] ?? null;
    $currentYear = (int) date('Y');

    $errors = [];

    if ($start && $end && $end !== 'present') {
        if ((int)$end < (int)$start) {
            $errors['date_ended'] = 'End year cannot be earlier than start year.';
        }
    }

    if ($start && (int)$start > $currentYear) {
        $errors['date_started'] = 'Start year cannot be in the future.';
    }

    if ($end && $end !== 'present' && (int)$end > $currentYear) {
        $errors['date_ended'] = 'End year cannot be in the future.';
    }

    if (!empty($errors)) {
        return redirect()->back()->withInput()->with('error', $errors);
    }

    // =========================
    // FILE HANDLING
    // =========================
    $certificatePath = null;

    try {
        $file = $this->request->getFile('certificate');

        if ($file && $file->getError() !== 4) {

            if (!$file->isValid()) {
                return redirect()->back()->withInput()->with('error', [
                    'certificate' => 'Invalid file upload.'
                ]);
            }

            if ($file->getSize() > (1 * 1024 * 1024)) {
                return redirect()->back()->withInput()->with('error', [
                    'certificate' => 'File size must not exceed 1MB.'
                ]);
            }

            if ($file->getMimeType() !== 'application/pdf') {
                return redirect()->back()->withInput()->with('error', [
                    'certificate' => 'Only PDF files are allowed.'
                ]);
            }

            $uploadPath = ROOTPATH . 'public/uploads/certs/';
            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0777, true);
            }

            $certificatePath = $file->getRandomName();
            $file->move($uploadPath, $certificatePath);
        }

        // =========================
        // SAVE (YEAR → DATE conversion)
        // =========================
        $this->educationModel->insert([
            'user_id'        => $userId,
            'school_name'    => $data['school_name'],
            'certification'  => $data['certification'],
            'grade_attained' => $data['grade_attained'] ?? null,
            'date_started'   => $start ? $start . '-01-01' : null,
            'date_ended'     => ($end && $end !== 'present') ? $end . '-12-31' : null,
            'certificate'    => $certificatePath,
            'active'         => 1,
        ]);

    } catch (\Exception $e) {
        return redirect()->back()->withInput()->with('error', $e->getMessage());
    }

    return redirect()->to('/applicant/basic-education')
        ->with('success', 'Basic education added successfully.');
}



    /**
     * Edit form
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

    // =========================
    // EXTRACT YEAR FROM DATE
    // =========================
    if (!empty($record['date_started'])) {
        $record['date_started'] = date('Y', strtotime($record['date_started']));
    }

    if (!empty($record['date_ended'])) {
        $record['date_ended'] = date('Y', strtotime($record['date_ended']));
    }

    // Optional: if null or future logic like "present"
    if (empty($record['date_ended'])) {
        $record['date_ended'] = 'present';
    }

    return view('applicant/basic_education_form', [
        'title'          => 'Edit Basic Education',
        'action'         => base_url('applicant/basic-education/update'),
        'edu'            => $record,
        'currentStep'    => 3,
        'levels'         => $this->levelModel->where('active', 1)
                              ->orderBy('index', 'ASC')
                              ->findAll(),
        'certifications' => [
            'KCPE' => 'Kenya Certificate of Primary Education (KCPE)',
            'KCSE' => 'Kenya Certificate of Secondary Education (KCSE)',
        ],
        'grades' => ['A','A-','B+','B','B-','C+','C','C-','D+','D','D-','E'],
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
        'school_name'    => 'required|string|max_length[255]',
        'certification'  => 'required|string|max_length[100]',
        'grade_attained' => 'permit_empty|string|max_length[50]',
        'date_started'   => 'permit_empty|numeric',
        'date_ended'     => 'permit_empty|string|max_length[10]',
        'certificate'    => 'permit_empty|max_size[certificate,1024]|ext_in[certificate,pdf]',
    ]);

    if (!$validation->withRequest($this->request)->run()) {
        return redirect()->back()->withInput()->with('error', $validation->getErrors());
    }

    // =========================
    // YEAR VALIDATION
    // =========================
    $start = $data['date_started'] ?? null;
    $end   = $data['date_ended'] ?? null;
    $currentYear = (int) date('Y');

    $errors = [];

    if ($start && $end && $end !== 'present') {
        if ((int)$end < (int)$start) {
            $errors['date_ended'] = 'End year cannot be earlier than start year.';
        }
    }

    if ($start && (int)$start > $currentYear) {
        $errors['date_started'] = 'Start year cannot be in the future.';
    }

    if ($end && $end !== 'present' && (int)$end > $currentYear) {
        $errors['date_ended'] = 'End year cannot be in the future.';
    }

    if (!empty($errors)) {
        return redirect()->back()->withInput()->with('error', $errors);
    }

    try {
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
                    'certificate' => 'File size must not exceed 1MB.'
                ]);
            }

            if ($file->getMimeType() !== 'application/pdf') {
                return redirect()->back()->withInput()->with('error', [
                    'certificate' => 'Only PDF files are allowed.'
                ]);
            }

            $uploadPath = ROOTPATH . 'public/uploads/certs/';

            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0777, true);
            }

            if (!empty($record['certificate'])) {
                $oldPath = $uploadPath . $record['certificate'];
                if (is_file($oldPath)) {
                    unlink($oldPath);
                }
            }

            $certificateName = $file->getRandomName();
            $file->move($uploadPath, $certificateName);
        }

        $this->educationModel->update($data['id'], [
            'school_name'    => $data['school_name'],
            'certification'  => $data['certification'],
            'grade_attained' => $data['grade_attained'] ?? null,
            'date_started'   => $start ? $start . '-01-01' : null,
            'date_ended'     => ($end && $end !== 'present') ? $end . '-12-31' : null,
            'certificate'    => $certificateName,
        ]);

    } catch (\Exception $e) {
        return redirect()->back()->withInput()->with('error', $e->getMessage());
    }

    return redirect()->to('/applicant/basic-education')
        ->with('success', 'Education updated successfully.');
}




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
        $uploadPath = ROOTPATH . 'public/uploads/certs/';

        if (!empty($record['certificate'])) {
            $filePath = $uploadPath . $record['certificate'];

            if (is_file($filePath)) {
                unlink($filePath);
            }
        }

        $this->educationModel->delete($record['id']);

    } catch (\Exception $e) {
        return redirect()->back()->with('error', $e->getMessage());
    }

    return redirect()->to('/applicant/basic-education')
        ->with('success', 'Education record deleted successfully.');
}


}
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
            'date_started'   => 'permit_empty|valid_date[Y-m-d]',
            'date_ended'     => 'permit_empty|valid_date[Y-m-d]',
            // ✅ FIXED: removed uploaded[]
            'certificate'    => 'permit_empty|max_size[certificate,2048]|ext_in[certificate,pdf]',
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('error', $validation->getErrors());
        }

        $certificatePath = null;

        try {
            $file = $this->request->getFile('certificate');

            // ✅ Only process if file exists
            if ($file && $file->getError() !== 4) {
                if ($file->isValid() && !$file->hasMoved()) {
                    $certificatePath = $file->getRandomName();
                    $file->move(ROOTPATH . 'public/uploads/certificates', $certificatePath);
                }
            }

            $this->educationModel->insert([
                'user_id'        => $userId,
                'school_name'    => $data['school_name'],
                'certification'  => $data['certification'],
                'grade_attained' => $data['grade_attained'] ?? null,
                'date_started'   => $data['date_started'] ?? null,
                'date_ended'     => $data['date_ended'] ?? null,
                'certificate'    => $certificatePath,
                'active'         => 1,
            ]);

        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }

        return redirect()->to('/applicant/basic-education')->with('success', 'Basic education added successfully.');
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

        return view('applicant/basic_education_form', [
            'title'          => 'Edit Basic Education',
            'action'         => base_url('applicant/basic-education/update'),
            'edu'            => $record,
             'currentStep' => 3,
            'levels'         => $this->levelModel->where('active', 1)->orderBy('index', 'ASC')->findAll(),
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
            'date_started'   => 'permit_empty|valid_date[Y-m-d]',
            'date_ended'     => 'permit_empty|valid_date[Y-m-d]',
            // ✅ FIXED
            'certificate'    => 'permit_empty|max_size[certificate,2048]|ext_in[certificate,pdf]',
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('error', $validation->getErrors());
        }

        try {
            $file = $this->request->getFile('certificate');
            $certificateName = $record['certificate'];

            if ($file && $file->getError() !== 4) {
                if ($file->isValid() && !$file->hasMoved()) {

                    // delete old file
                    if (!empty($record['certificate']) &&
                        file_exists(ROOTPATH . 'public/uploads/certificates/' . $record['certificate'])) {
                        unlink(ROOTPATH . 'public/uploads/certificates/' . $record['certificate']);
                    }

                    $certificateName = $file->getRandomName();
                    $file->move(ROOTPATH . 'public/uploads/certificates', $certificateName);
                }
            }

            $this->educationModel->update($data['id'], [
                'school_name'    => $data['school_name'],
                'certification'  => $data['certification'],
                'grade_attained' => $data['grade_attained'] ?? null,
                'date_started'   => $data['date_started'] ?? null,
                'date_ended'     => $data['date_ended'] ?? null,
                'certificate'    => $certificateName,
            ]);

        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }

        return redirect()->to('/applicant/basic-education')->with('success', 'Education updated successfully.');
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
                file_exists(ROOTPATH . 'public/uploads/certificates/' . $record['certificate'])) {
                unlink(ROOTPATH . 'public/uploads/certificates/' . $record['certificate']);
            }

            $this->educationModel->delete($record['id']);

        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }

        return redirect()->to('/applicant/basic-education')->with('success', 'Education record deleted successfully.');
    }
}
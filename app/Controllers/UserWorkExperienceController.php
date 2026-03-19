<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UserWorkExperienceModel;

class UserWorkExperienceController extends BaseController
{
    protected $workModel;

    public function __construct()
    {
        $this->workModel = new UserWorkExperienceModel();
    }

    // List all experiences
    public function index()
    {
        $userId = session()->get('user_id');
        $experiences = $this->workModel->getByUser($userId);

        return view('applicant/work_experience', [
            'title'       => 'My Work Experience',
            'experiences' => $experiences,
            'currentStep' => 7
        ]);
    }

    // Show create form
    public function create()
    {
        return view('applicant/work_experience_form', [
            'title'      => 'Add Work Experience',
            'action'     => base_url('applicant/work-experience/store'),
            'experience' => null,
            'currentStep' => 7
        ]);
    }

    // Store new experience
    public function store()
    {
        $userId = session()->get('user_id');
        $data = $this->request->getPost();

        $validation = \Config\Services::validation();
        $validation->setRules([
            'company_name'      => 'required|string|max_length[255]',
            'company_address'   => 'permit_empty|string|max_length[255]',
            'company_phone'     => 'permit_empty|string|max_length[50]',
            'position'          => 'required|string|max_length[255]',
            'start_date'        => 'required|valid_date',
            'end_date'          => 'permit_empty|valid_date',
            'currently_working' => 'permit_empty|in_list[0,1]',
            'responsibilities'  => 'permit_empty|string',
            'reference_letter'  => 'permit_empty|uploaded[reference_letter]|max_size[reference_letter,2048]|ext_in[reference_letter,pdf]',
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('error', $validation->getErrors());
        }

        $currentlyWorking = !empty($data['currently_working']) ? 1 : 0;
        $endDate = $currentlyWorking ? null : ($data['end_date'] ?? null);

        // Handle reference letter upload
        $referenceFile = null;
        if ($file = $this->request->getFile('reference_letter')) {
            if ($file->isValid() && !$file->hasMoved()) {
                $referenceFile = $file->getRandomName();
                $file->move(ROOTPATH . 'public/uploads/work_experience', $referenceFile);
            }
        }

        $this->workModel->insert([
            'uuid'              => uniqid(),
            'user_id'           => $userId,
            'company_name'      => $data['company_name'],
            'company_address'   => $data['company_address'] ?? null,
            'company_phone'     => $data['company_phone'] ?? null,
            'position'          => $data['position'],
            'start_date'        => $data['start_date'],
            'end_date'          => $endDate,
            'currently_working' => $currentlyWorking,
            'responsibilities'  => $data['responsibilities'] ?? null,
            'reference_file'    => $referenceFile,
            'active'            => 1
        ]);

        return redirect()->to('/applicant/work-experience')->with('success', 'Work experience added successfully.');
    }

    // Show edit form
    public function edit($uuid)
    {
        $userId = session()->get('user_id');
        $experience = $this->workModel->where(['uuid' => $uuid, 'user_id' => $userId])->first();

        if (!$experience) {
            return redirect()->back()->withInput()->with('error', 'Work experience not found.');
        }

        return view('applicant/work_experience_form', [
            'title'      => 'Edit Work Experience',
            'action'     => base_url('applicant/work-experience/update'),
            'experience' => $experience,
            'currentStep' => 7
        ]);
    }

    // Update experience
    public function update()
    {
        $userId = session()->get('user_id');
        $data = $this->request->getPost();

        $experience = $this->workModel->where(['id' => $data['id'], 'user_id' => $userId])->first();
        if (!$experience) {
            return redirect()->back()->withInput()->with('error', 'Work experience not found.');
        }

        $validation = \Config\Services::validation();
        $validation->setRules([
            'company_name'      => 'required|string|max_length[255]',
            'company_address'   => 'permit_empty|string|max_length[255]',
            'company_phone'     => 'permit_empty|string|max_length[50]',
            'position'          => 'required|string|max_length[255]',
            'start_date'        => 'required|valid_date',
            'end_date'          => 'permit_empty|valid_date',
            'currently_working' => 'permit_empty|in_list[0,1]',
            'responsibilities'  => 'permit_empty|string',
            'reference_letter'  => 'permit_empty|uploaded[reference_letter]|max_size[reference_letter,2048]|ext_in[reference_letter,pdf]',
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('error', $validation->getErrors());
        }

        $currentlyWorking = !empty($data['currently_working']) ? 1 : 0;
        $endDate = $currentlyWorking ? null : ($data['end_date'] ?? null);

        // Handle reference letter upload
        $referenceFile = $experience['reference_file'] ?? null;
        if ($file = $this->request->getFile('reference_letter')) {
            if ($file->isValid() && !$file->hasMoved()) {
                if ($referenceFile && file_exists(ROOTPATH . 'public/uploads/work_experience/' . $referenceFile)) {
                    unlink(ROOTPATH . 'public/uploads/work_experience/' . $referenceFile);
                }
                $referenceFile = $file->getRandomName();
                $file->move(ROOTPATH . 'public/uploads/work_experience', $referenceFile);
            }
        }

        $this->workModel->update($data['id'], [
            'company_name'      => $data['company_name'],
            'company_address'   => $data['company_address'] ?? null,
            'company_phone'     => $data['company_phone'] ?? null,
            'position'          => $data['position'],
            'start_date'        => $data['start_date'],
            'end_date'          => $endDate,
            'currently_working' => $currentlyWorking,
            'responsibilities'  => $data['responsibilities'] ?? null,
            'reference_file'    => $referenceFile,
        ]);

        return redirect()->to('/applicant/work-experience')->with('success', 'Work experience updated successfully.');
    }

    // Delete experience
    public function delete($uuid)
    {
        $userId = session()->get('user_id');
        $experience = $this->workModel->where(['uuid' => $uuid, 'user_id' => $userId])->first();

        if ($experience) {
            if (!empty($experience['reference_file']) && file_exists(ROOTPATH . 'public/uploads/work_experience/' . $experience['reference_file'])) {
                unlink(ROOTPATH . 'public/uploads/work_experience/' . $experience['reference_file']);
            }
            $this->workModel->delete($experience['id']);
        }

        return redirect()->to('/applicant/work-experience')->with('success', 'Work experience deleted successfully.');
    }
}
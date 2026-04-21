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

    public function create()
    {
        return view('applicant/work_experience_form', [
            'title'      => 'Add Work Experience',
            'action'     => base_url('applicant/work-experience/store'),
            'experience' => null,
            'currentStep' => 7
        ]);
    }

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
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('error', $validation->getErrors());
        }

        $currentlyWorking = !empty($data['currently_working']) ? 1 : 0;

        $startDate = $data['start_date'];
        $endDate = $data['end_date'] ?? null;

        // DATE LOGIC VALIDATION
        if (!$currentlyWorking) {
            if (empty($endDate)) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', ['end_date' => 'End date is required when not currently working.']);
            }

            if (strtotime($endDate) < strtotime($startDate)) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', ['end_date' => 'End date cannot be earlier than start date.']);
            }
        } else {
            $endDate = null;
        }

        // FILE UPLOAD (CERTS)
        $referenceFile = null;
        $file = $this->request->getFile('reference_letter');

        if ($file && $file->isValid() && !$file->hasMoved()) {
            if ($file->getSize() > 2048 * 1024) {
                return redirect()->back()->withInput()
                    ->with('error', ['reference_letter' => 'File size must not exceed 2MB']);
            }

            $referenceFile = $file->getRandomName();
            $file->move(ROOTPATH . 'public/uploads/certs', $referenceFile);
        }

        $this->workModel->insert([
            'uuid'              => uniqid(),
            'user_id'           => $userId,
            'company_name'      => $data['company_name'],
            'company_address'   => $data['company_address'] ?? null,
            'company_phone'     => $data['company_phone'] ?? null,
            'position'          => $data['position'],
            'start_date'        => $startDate,
            'end_date'          => $endDate,
            'currently_working' => $currentlyWorking,
            'responsibilities'  => $data['responsibilities'] ?? null,
            'reference_file'    => $referenceFile,
            'active'            => 1
        ]);

        return redirect()->to('/applicant/work-experience')
            ->with('success', 'Work experience added successfully.');
    }

    public function edit($uuid)
    {
        $userId = session()->get('user_id');
        $experience = $this->workModel->where([
            'uuid' => $uuid,
            'user_id' => $userId
        ])->first();

        if (!$experience) {
            return redirect()->back()->with('error', 'Work experience not found.');
        }

        return view('applicant/work_experience_form', [
            'title'      => 'Edit Work Experience',
            'action'     => base_url('applicant/work-experience/update'),
            'experience' => $experience,
            'currentStep' => 7
        ]);
    }

    public function update()
    {
        $userId = session()->get('user_id');
        $data = $this->request->getPost();

        $experience = $this->workModel->where([
            'id' => $data['id'],
            'user_id' => $userId
        ])->first();

        if (!$experience) {
            return redirect()->back()->with('error', 'Work experience not found.');
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
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('error', $validation->getErrors());
        }

        $currentlyWorking = !empty($data['currently_working']) ? 1 : 0;

        $startDate = $data['start_date'];
        $endDate = $data['end_date'] ?? null;

        if (!$currentlyWorking) {
            if (empty($endDate)) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', ['end_date' => 'End date is required when not currently working.']);
            }

            if (strtotime($endDate) < strtotime($startDate)) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', ['end_date' => 'End date cannot be earlier than start date.']);
            }
        } else {
            $endDate = null;
        }

        // FILE UPDATE (CERTS)
        $referenceFile = $experience['reference_file'] ?? null;
        $file = $this->request->getFile('reference_letter');

        if ($file && $file->isValid() && !$file->hasMoved()) {
            if ($file->getSize() > 2048 * 1024) {
                return redirect()->back()->withInput()
                    ->with('error', ['reference_letter' => 'File size must not exceed 2MB']);
            }

            if ($referenceFile && file_exists(ROOTPATH . 'public/uploads/certs/' . $referenceFile)) {
                unlink(ROOTPATH . 'public/uploads/certs/' . $referenceFile);
            }

            $referenceFile = $file->getRandomName();
            $file->move(ROOTPATH . 'public/uploads/certs', $referenceFile);
        }

        $this->workModel->update($data['id'], [
            'company_name'      => $data['company_name'],
            'company_address'   => $data['company_address'] ?? null,
            'company_phone'     => $data['company_phone'] ?? null,
            'position'          => $data['position'],
            'start_date'        => $startDate,
            'end_date'          => $endDate,
            'currently_working' => $currentlyWorking,
            'responsibilities'  => $data['responsibilities'] ?? null,
            'reference_file'    => $referenceFile,
        ]);

        return redirect()->to('/applicant/work-experience')
            ->with('success', 'Work experience updated successfully.');
    }

    public function delete($uuid)
    {
        $userId = session()->get('user_id');

        $experience = $this->workModel->where([
            'uuid' => $uuid,
            'user_id' => $userId
        ])->first();

        if ($experience) {
            if (!empty($experience['reference_file'])) {
                $path = ROOTPATH . 'public/uploads/certs/' . $experience['reference_file'];
                if (file_exists($path)) {
                    unlink($path);
                }
            }

            $this->workModel->delete($experience['id']);
        }

        return redirect()->to('/applicant/work-experience')
            ->with('success', 'Work experience deleted successfully.');
    }
}
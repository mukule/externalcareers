<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\JobTypeModel;

class JobTypeController extends BaseController
{
    protected $jobTypeModel;

    public function __construct()
    {
        $this->jobTypeModel = new JobTypeModel();
    }

    public function index()
    {
        return view('admin/job_types', [
            'title'    => 'Job Types',
            'jobTypes' => $this->jobTypeModel->findAll()
        ]);
    }

    public function create()
    {
        return view('admin/create_job_type', [
            'title'  => 'Add Job Type',
            'action' => base_url('admin/job-types/store')
        ]);
    }

    public function store()
    {
        $data = $this->request->getPost();

        $validation = \Config\Services::validation();
        $validation->setRules([
            'name' => 'required|is_unique[job_types.name]',
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('error', $validation->getErrors());
        }

        $insertData = [
            'name'         => $data['name'],
            'display_name' => $data['display_name'] ?? $data['name'],
            'active'       => isset($data['active']) ? 1 : 0, // handle active status
        ];

        $this->jobTypeModel->insert($insertData);

        return redirect()->to('/admin/job-types')->with('success', 'Job Type added successfully.');
    }

    public function edit($uuid)
    {
        $jobType = $this->jobTypeModel->where('uuid', $uuid)->first();
        if (!$jobType) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Job Type not found.');
        }

        return view('admin/create_job_type', [
            'title'    => 'Edit Job Type',
            'action'   => base_url("admin/job-types/update"),
            'jobType'  => $jobType
        ]);
    }

    public function update()
    {
        $id = $this->request->getPost('id');
        $jobType = $this->jobTypeModel->find($id);

        if (!$jobType) {
            return redirect()->back()->with('error', 'Job Type not found.');
        }

        $data = $this->request->getPost();

        $validation = \Config\Services::validation();
        $validation->setRules([
            'name' => "required|is_unique[job_types.name,id,{$id}]",
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('error', $validation->getErrors());
        }

        $updateData = [
            'name'         => $data['name'],
            'display_name' => $data['display_name'] ?? $data['name'],
            'active'       => isset($data['active']) ? 1 : 0, // handle active status
        ];

        $this->jobTypeModel->update($id, $updateData);

        return redirect()->to('/admin/job-types')->with('success', 'Job Type updated successfully.');
    }

    public function delete($uuid)
    {
        $jobType = $this->jobTypeModel->where('uuid', $uuid)->first();
        if ($jobType) {
            $this->jobTypeModel->delete($jobType['id']);
        }

        return redirect()->to('/admin/job-types')->with('success', 'Job Type deleted successfully.');
    }
}

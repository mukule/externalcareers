<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\JobDisciplineModel;

class JobDisciplineController extends BaseController
{
    protected $jobDisciplineModel;

    public function __construct()
    {
        $this->jobDisciplineModel = new JobDisciplineModel();
    }

    public function index()
    {
        return view('admin/job_disciplines', [
            'title'          => 'Job Disciplines',
            'jobDisciplines' => $this->jobDisciplineModel->findAll()
        ]);
    }

    public function create()
    {
        return view('admin/create_job_discipline', [
            'title'  => 'Add Job Discipline',
            'action' => base_url('admin/job-disciplines/store')
        ]);
    }

    public function store()
    {
        $data = $this->request->getPost();

        $validation = \Config\Services::validation();
        $validation->setRules([
            'name' => 'required|is_unique[job_disciplines.name]',
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('error', $validation->getErrors());
        }

        $insertData = [
            'name'         => $data['name'],
            'display_name' => $data['display_name'] ?? $data['name'],
            'active'       => isset($data['active']) ? 1 : 0, // default active if not specified
        ];

        $this->jobDisciplineModel->insert($insertData);

        return redirect()->to('/admin/job-disciplines')->with('success', 'Job Discipline added successfully.');
    }

    public function edit($uuid)
    {
        $discipline = $this->jobDisciplineModel->where('uuid', $uuid)->first();
        if (!$discipline) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Job Discipline not found.');
        }

        return view('admin/create_job_discipline', [
            'title'      => 'Edit Job Discipline',
            'action'     => base_url("admin/job-disciplines/update"),
            'discipline' => $discipline
        ]);
    }

    public function update()
    {
        $id = $this->request->getPost('id');
        $discipline = $this->jobDisciplineModel->find($id);

        if (!$discipline) {
            return redirect()->back()->with('error', 'Job Discipline not found.');
        }

        $data = $this->request->getPost();

        $validation = \Config\Services::validation();
        $validation->setRules([
            'name' => "required|is_unique[job_disciplines.name,id,{$id}]",
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('error', $validation->getErrors());
        }

        $updateData = [
            'name'         => $data['name'],
            'display_name' => $data['display_name'] ?? $data['name'],
            'active'       => isset($data['active']) ? 1 : 0,
        ];

        $this->jobDisciplineModel->update($id, $updateData);

        return redirect()->to('/admin/job-disciplines')->with('success', 'Job Discipline updated successfully.');
    }

    public function delete($uuid)
    {
        $discipline = $this->jobDisciplineModel->where('uuid', $uuid)->first();
        if ($discipline) {
            $this->jobDisciplineModel->delete($discipline['id']);
        }

        return redirect()->to('/admin/job-disciplines')->with('success', 'Job Discipline deleted successfully.');
    }
}

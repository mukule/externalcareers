<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\JobTypeModel;

class JobTypeController extends BaseController
{
    protected $jobTypeModel;
    protected $uploadPath;

    public function __construct()
    {
        $this->jobTypeModel = new JobTypeModel();
        // Upload path in public folder
        $this->uploadPath = WRITEPATH . '../public/uploads/job_types/';
        // Ensure directory exists
        if (!is_dir($this->uploadPath)) {
            mkdir($this->uploadPath, 0755, true);
        }
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
            'banner' => 'uploaded[banner]|max_size[banner,2048]|is_image[banner]',
            'icon'   => 'uploaded[icon]|max_size[icon,1024]|is_image[icon]',
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('error', $validation->getErrors());
        }

        // Handle Banner Upload
        $bannerFile = $this->request->getFile('banner');
        $bannerName = $bannerFile ? $bannerFile->getRandomName() : null;
        if ($bannerFile) $bannerFile->move($this->uploadPath, $bannerName);

        // Handle Icon Upload
        $iconFile = $this->request->getFile('icon');
        $iconName = $iconFile ? $iconFile->getRandomName() : null;
        if ($iconFile) $iconFile->move($this->uploadPath, $iconName);

        $insertData = [
            'name'         => $data['name'],
            'display_name' => $data['display_name'] ?? $data['name'],
            'banner'       => $bannerName,
            'icon'         => $iconName,
            'description'  => $data['description'] ?? null,
            'active'       => isset($data['active']) ? 1 : 0,
        ];

        $this->jobTypeModel->insert($insertData);

        return redirect()->to('/admin/job-types')->with('success', 'Job Type added successfully.');
    }

    public function edit($uuid)
    {
        $jobType = $this->jobTypeModel->where('uuid', $uuid)->first();
         if (!$jobType) {
            return redirect()->back()->with('error', 'Job Type not found.');
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
            'banner' => 'max_size[banner,2048]|is_image[banner]',
            'icon'   => 'max_size[icon,1024]|is_image[icon]',
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('error', $validation->getErrors());
        }

        $updateData = [
            'name'         => $data['name'],
            'display_name' => $data['display_name'] ?? $data['name'],
            'description'  => $data['description'] ?? null,
            'active'       => isset($data['active']) ? 1 : 0,
        ];

        // Handle Banner Upload
        $bannerFile = $this->request->getFile('banner');
        if ($bannerFile && $bannerFile->isValid()) {
            $bannerName = $bannerFile->getRandomName();
            $bannerFile->move($this->uploadPath, $bannerName);
            $updateData['banner'] = $bannerName;

            // Delete old banner if exists
            if (!empty($jobType['banner']) && file_exists($this->uploadPath . $jobType['banner'])) {
                unlink($this->uploadPath . $jobType['banner']);
            }
        }

        // Handle Icon Upload
        $iconFile = $this->request->getFile('icon');
        if ($iconFile && $iconFile->isValid()) {
            $iconName = $iconFile->getRandomName();
            $iconFile->move($this->uploadPath, $iconName);
            $updateData['icon'] = $iconName;

            // Delete old icon if exists
            if (!empty($jobType['icon']) && file_exists($this->uploadPath . $jobType['icon'])) {
                unlink($this->uploadPath . $jobType['icon']);
            }
        }

        $this->jobTypeModel->update($id, $updateData);

        return redirect()->to('/admin/job-types')->with('success', 'Job Type updated successfully.');
    }

    public function delete($uuid)
    {
        $jobType = $this->jobTypeModel->where('uuid', $uuid)->first();
        if ($jobType) {
            // Delete images if they exist
            if (!empty($jobType['banner']) && file_exists($this->uploadPath . $jobType['banner'])) {
                unlink($this->uploadPath . $jobType['banner']);
            }
            if (!empty($jobType['icon']) && file_exists($this->uploadPath . $jobType['icon'])) {
                unlink($this->uploadPath . $jobType['icon']);
            }

            $this->jobTypeModel->delete($jobType['id']);
        }

        return redirect()->to('/admin/job-types')->with('success', 'Job Type deleted successfully.');
    }
}
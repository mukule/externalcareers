<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\FieldOfStudyModel;

class FieldOfStudyController extends BaseController
{
    protected $fieldOfStudyModel;

    public function __construct()
    {
        $this->fieldOfStudyModel = new FieldOfStudyModel();
    }

    public function index()
    {
        return view('admin/fields_of_study', [
            'title'           => 'Fields of Study',
            'fieldsOfStudy'   => $this->fieldOfStudyModel->findAll()
        ]);
    }

    public function create()
    {
        return view('admin/create_field_of_study', [
            'title'  => 'Add Field of Study',
            'action' => base_url('admin/fields-of-study/store')
        ]);
    }

    public function store()
    {
        $data = $this->request->getPost();

        $validation = \Config\Services::validation();
        $validation->setRules([
            'name' => 'required|is_unique[fields_of_study.name]',
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('error', $validation->getErrors());
        }

        $insertData = [
            'name'   => $data['name'],
            'active' => isset($data['active']) ? 1 : 1, // default active
        ];

        $this->fieldOfStudyModel->insert($insertData);

        return redirect()->to('/admin/fields-of-study')->with('success', 'Field of Study added successfully.');
    }

    public function edit($uuid)
    {
        $field = $this->fieldOfStudyModel->where('uuid', $uuid)->first();
        if (!$field) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Field of Study not found.');
        }

        return view('admin/create_field_of_study', [
            'title'  => 'Edit Field of Study',
            'action' => base_url("admin/fields-of-study/update"),
            'field'  => $field
        ]);
    }

    public function update()
    {
        $id = $this->request->getPost('id');
        $field = $this->fieldOfStudyModel->find($id);

        if (!$field) {
            return redirect()->back()->with('error', 'Field of Study not found.');
        }

        $data = $this->request->getPost();

        $validation = \Config\Services::validation();
        $validation->setRules([
            'name' => "required|is_unique[fields_of_study.name,id,{$id}]",
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('error', $validation->getErrors());
        }

        $updateData = [
            'name'   => $data['name'],
            'active' => isset($data['active']) ? 1 : 0,
        ];

        $this->fieldOfStudyModel->update($id, $updateData);

        return redirect()->to('/admin/fields-of-study')->with('success', 'Field of Study updated successfully.');
    }

    public function delete($uuid)
    {
        $field = $this->fieldOfStudyModel->where('uuid', $uuid)->first();
        if ($field) {
            $this->fieldOfStudyModel->delete($field['id']);
        }

        return redirect()->to('/admin/fields-of-study')->with('success', 'Field of Study deleted successfully.');
    }
}

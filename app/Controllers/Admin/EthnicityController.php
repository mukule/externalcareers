<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\EthnicityModel;

class EthnicityController extends BaseController
{
    protected $ethnicityModel;

    public function __construct()
    {
        $this->ethnicityModel = new EthnicityModel();
    }

    public function index()
    {
        return view('admin/ethnicities', [
            'title'       => 'Ethnicities',
            'ethnicities' => $this->ethnicityModel->findAll()
        ]);
    }

    public function create()
    {
        return view('admin/create_ethnicity', [
            'title'  => 'Add Ethnicity',
            'action' => base_url('admin/ethnicities/store')
        ]);
    }

    public function store()
    {
        $data = $this->request->getPost();

        $validation = \Config\Services::validation();
        $validation->setRules([
            'name' => 'required|is_unique[ethnicities.name]',
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('error', $validation->getErrors());
        }

        $insertData = [
            'name'   => $data['name'],
            'active' => isset($data['active']) ? 1 : 1,
        ];

        $this->ethnicityModel->insert($insertData);

        return redirect()->to('/admin/ethnicities')->with('success', 'Ethnicity added successfully.');
    }

    public function edit($uuid)
    {
        $ethnicity = $this->ethnicityModel->where('uuid', $uuid)->first();
        if (!$ethnicity) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Ethnicity not found.');
        }

        return view('admin/create_ethnicity', [
            'title'   => 'Edit Ethnicity',
            'action'  => base_url("admin/ethnicities/update"),
            'ethnicity' => $ethnicity
        ]);
    }

    public function update()
    {
        $id = $this->request->getPost('id');
        $ethnicity = $this->ethnicityModel->find($id);

        if (!$ethnicity) {
            return redirect()->back()->with('error', 'Ethnicity not found.');
        }

        $data = $this->request->getPost();

        $validation = \Config\Services::validation();
        $validation->setRules([
            'name' => "required|is_unique[ethnicities.name,id,{$id}]",
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('error', $validation->getErrors());
        }

        $updateData = [
            'name'   => $data['name'],
            'active' => isset($data['active']) ? 1 : 0,
        ];

        $this->ethnicityModel->update($id, $updateData);

        return redirect()->to('/admin/ethnicities')->with('success', 'Ethnicity updated successfully.');
    }

    public function delete($uuid)
    {
        $ethnicity = $this->ethnicityModel->where('uuid', $uuid)->first();
        if ($ethnicity) {
            $this->ethnicityModel->delete($ethnicity['id']);
        }

        return redirect()->to('/admin/ethnicities')->with('success', 'Ethnicity deleted successfully.');
    }
}

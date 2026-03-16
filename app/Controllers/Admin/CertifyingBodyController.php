<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\CertifyingBodyModel;

class CertifyingBodyController extends BaseController
{
    protected $certifyingBodyModel;

    public function __construct()
    {
        $this->certifyingBodyModel = new CertifyingBodyModel();
    }

    public function index()
    {
        return view('admin/certifying_bodies', [
            'title'             => 'Certifying Bodies',
            'certifyingBodies'  => $this->certifyingBodyModel->findAll()
        ]);
    }

    public function create()
    {
        return view('admin/create_certifying_body', [
            'title'  => 'Add Certifying Body',
            'action' => base_url('admin/certifying-bodies/store')
        ]);
    }

    public function store()
    {
        $data = $this->request->getPost();

        $validation = \Config\Services::validation();
        $validation->setRules([
            'name' => 'required|is_unique[certifying_bodies.name]',
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('error', $validation->getErrors());
        }

        $insertData = [
            'name'   => $data['name'],
            'active' => isset($data['active']) ? 1 : 1, // default active
        ];

        $this->certifyingBodyModel->insert($insertData);

        return redirect()->to('/admin/certifying-bodies')->with('success', 'Certifying Body added successfully.');
    }

    public function edit($uuid)
    {
        $body = $this->certifyingBodyModel->where('uuid', $uuid)->first();
        if (!$body) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Certifying Body not found.');
        }

        return view('admin/create_certifying_body', [
            'title' => 'Edit Certifying Body',
            'action' => base_url("admin/certifying-bodies/update"),
            'body'   => $body
        ]);
    }

    public function update()
    {
        $id = $this->request->getPost('id');
        $body = $this->certifyingBodyModel->find($id);

        if (!$body) {
            return redirect()->back()->with('error', 'Certifying Body not found.');
        }

        $data = $this->request->getPost();

        $validation = \Config\Services::validation();
        $validation->setRules([
            'name' => "required|is_unique[certifying_bodies.name,id,{$id}]",
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('error', $validation->getErrors());
        }

        $updateData = [
            'name'   => $data['name'],
            'active' => isset($data['active']) ? 1 : 0,
        ];

        $this->certifyingBodyModel->update($id, $updateData);

        return redirect()->to('/admin/certifying-bodies')->with('success', 'Certifying Body updated successfully.');
    }

    public function delete($uuid)
    {
        $body = $this->certifyingBodyModel->where('uuid', $uuid)->first();
        if ($body) {
            $this->certifyingBodyModel->delete($body['id']);
        }

        return redirect()->to('/admin/certifying-bodies')->with('success', 'Certifying Body deleted successfully.');
    }
}

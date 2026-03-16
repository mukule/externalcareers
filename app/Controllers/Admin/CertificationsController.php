<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\CertificationModel;
use App\Models\CertifyingBodyModel;

class CertificationsController extends BaseController
{
    protected $certificationModel;
    protected $certifyingBodyModel;

    public function __construct()
    {
        $this->certificationModel = new CertificationModel();
        $this->certifyingBodyModel = new CertifyingBodyModel();
    }

    public function index()
    {
        $certifications = $this->certificationModel->findAll();

        // Optionally, fetch certifying body names
        foreach ($certifications as &$cert) {
            $body = $this->certifyingBodyModel->find($cert['certifying_body_id']);
            $cert['certifying_body_name'] = $body['name'] ?? '';
        }

        return view('admin/certifications', [
            'title'          => 'Certifications',
            'certifications' => $certifications,
        ]);
    }

    public function create()
    {
        return view('admin/create_certification', [
            'title'  => 'Add Certification',
            'action' => base_url('admin/certifications/store'),
            'certifyingBodies' => $this->certifyingBodyModel->where('active', 1)->findAll(),
        ]);
    }

    public function store()
    {
        $data = $this->request->getPost();

        $validation = \Config\Services::validation();
        $validation->setRules([
            'name' => 'required|is_unique[certifications.name]',
            'certifying_body_id' => 'required|integer',
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('error', $validation->getErrors());
        }

        $insertData = [
            'name' => $data['name'],
            'certifying_body_id' => $data['certifying_body_id'],
            'active' => isset($data['active']) ? 1 : 0,
        ];

        $this->certificationModel->insert($insertData);

        return redirect()->to('/admin/certifications')->with('success', 'Certification added successfully.');
    }

    public function edit($uuid)
    {
        $cert = $this->certificationModel->where('uuid', $uuid)->first();
        if (!$cert) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Certification not found.');
        }

        return view('admin/create_certification', [
            'title' => 'Edit Certification',
            'action' => base_url('admin/certifications/update'),
            'certification' => $cert,
            'bodies' => $this->certifyingBodyModel->where('active', 1)->findAll(),
        ]);
    }

    public function update()
    {
        $id = $this->request->getPost('id');
        $cert = $this->certificationModel->find($id);

        if (!$cert) {
            return redirect()->back()->with('error', 'Certification not found.');
        }

        $data = $this->request->getPost();

        $validation = \Config\Services::validation();
        $validation->setRules([
            'name' => "required|is_unique[certifications.name,id,{$id}]",
            'certifying_body_id' => 'required|integer',
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('error', $validation->getErrors());
        }

        $updateData = [
            'name' => $data['name'],
            'certifying_body_id' => $data['certifying_body_id'],
            'active' => isset($data['active']) ? 1 : 0,
        ];

        $this->certificationModel->update($id, $updateData);

        return redirect()->to('/admin/certifications')->with('success', 'Certification updated successfully.');
    }

    public function delete($uuid)
    {
        $cert = $this->certificationModel->where('uuid', $uuid)->first();
        if ($cert) {
            $this->certificationModel->delete($cert['id']);
        }

        return redirect()->to('/admin/certifications')->with('success', 'Certification deleted successfully.');
    }
}

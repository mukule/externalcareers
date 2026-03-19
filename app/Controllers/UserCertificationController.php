<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UserCertificationModel;
use App\Models\CertifyingBodyModel;
use App\Models\CertificationModel;

class UserCertificationController extends BaseController
{
    protected $certModel;
    protected $bodyModel;
    protected $certificationModel;

    public function __construct()
    {
        $this->certModel          = new UserCertificationModel();
        $this->bodyModel          = new CertifyingBodyModel();
        $this->certificationModel = new CertificationModel();
    }

    // List all certifications
    public function index()
    {
        $userId = session()->get('user_id');

        // Fetch user certifications with related certification and certifying body
        $certifications = $this->certModel
            ->select('user_certifications.*, certifications.name AS cert_name, certifying_bodies.name AS body_name')
            ->join('certifications', 'certifications.id = user_certifications.certification_id', 'left')
            ->join('certifying_bodies', 'certifying_bodies.id = certifications.certifying_body_id', 'left')
            ->where('user_certifications.user_id', $userId)
            ->orderBy('attained_date', 'DESC')
            ->findAll();

        return view('applicant/certifications', [
            'title'          => 'My Certifications',
            'certifications' => $certifications,
            'currentStep'    => 6
        ]);
    }

    // Show create form
    public function create()
    {
        $bodies = $this->bodyModel->where('active', 1)->findAll();

        return view('applicant/certification_form', [
            'title'         => 'Add Certification',
            'action'        => base_url('applicant/certification/store'),
            'certification' => null,
            'bodies'        => $bodies,
            'currentStep'   => 6
        ]);
    }

    // AJAX: Get certifications for a certifying body
    public function getCertificationsByBody($bodyId)
    {
        $certifications = $this->certificationModel
                               ->where('certifying_body_id', $bodyId)
                               ->where('active', 1)
                               ->findAll();

        return $this->response->setJSON($certifications);
    }

    // Store new certification
    public function store()
    {
        $userId = session()->get('user_id');
        $data   = $this->request->getPost();

        $validation = \Config\Services::validation();
        $validation->setRules([
            'name'             => 'required|string|max_length[255]',
            'certification_id' => 'permit_empty|integer',
            'attained_date'    => 'required|valid_date',
            'certificate_file' => 'permit_empty|uploaded[certificate_file]|max_size[certificate_file,2048]|ext_in[certificate_file,pdf]',
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('error', $validation->getErrors());
        }

        // Handle certificate upload
        $certificatePath = null;
        if ($file = $this->request->getFile('certificate_file')) {
            if ($file->isValid() && !$file->hasMoved()) {
                $certificatePath = $file->getRandomName();
                $file->move(ROOTPATH . 'public/uploads/certifications', $certificatePath);
            }
        }

        $this->certModel->insert([
            'uuid'             => uniqid(),
            'user_id'          => $userId,
            'name'             => $data['name'],
            'certification_id' => $data['certification_id'] ?? null,
            'attained_date'    => $data['attained_date'],
            'certificate_file' => $certificatePath,
            'active'           => 1
        ]);

        return redirect()->to('/applicant/certification')->with('success', 'Certification added successfully.');
    }

   
    // Show edit form
public function edit($uuid)
{
    $userId = session()->get('user_id');

    // Join certifications to get certifying_body_id
    $cert = $this->certModel
                 ->select('user_certifications.*, certifications.certifying_body_id')
                 ->join('certifications', 'certifications.id = user_certifications.certification_id', 'left')
                 ->where(['user_certifications.uuid' => $uuid, 'user_certifications.user_id' => $userId])
                 ->first();

    $bodies = $this->bodyModel->where('active', 1)->findAll();

    if (!$cert) {
        return redirect()->back()->withInput()->with('error', 'Certification not found.');
    }

    return view('applicant/certification_form', [
        'title'         => 'Edit Certification',
        'action'        => base_url('applicant/certification/update'),
        'certification' => $cert,
        'bodies'        => $bodies,
        'currentStep'   => 6
    ]);
}


    // Update certification
    public function update()
    {
        $userId = session()->get('user_id');
        $data   = $this->request->getPost();

        $cert = $this->certModel->where(['id' => $data['id'], 'user_id' => $userId])->first();
        if (!$cert) {
            return redirect()->back()->withInput()->with('error', 'Certification not found.');
        }

        $validation = \Config\Services::validation();
        $validation->setRules([
            'name'             => 'required|string|max_length[255]',
            'certification_id' => 'permit_empty|integer',
            'attained_date'    => 'required|valid_date',
            'certificate_file' => 'permit_empty|uploaded[certificate_file]|max_size[certificate_file,2048]|ext_in[certificate_file,pdf]',
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('error', $validation->getErrors());
        }

        // Handle new certificate upload
        if ($file = $this->request->getFile('certificate_file')) {
            if ($file->isValid() && !$file->hasMoved()) {
                if ($cert['certificate_file'] && file_exists(ROOTPATH . 'public/uploads/certifications/' . $cert['certificate_file'])) {
                    unlink(ROOTPATH . 'public/uploads/certifications/' . $cert['certificate_file']);
                }
                $data['certificate_file'] = $file->getRandomName();
                $file->move(ROOTPATH . 'public/uploads/certifications', $data['certificate_file']);
            }
        }

        $this->certModel->update($data['id'], [
            'name'             => $data['name'],
            'certification_id' => $data['certification_id'] ?? null,
            'attained_date'    => $data['attained_date'],
            'certificate_file' => $data['certificate_file'] ?? $cert['certificate_file'],
        ]);

        return redirect()->to('/applicant/certification')->with('success', 'Certification updated successfully.');
    }

    // Delete certification
    public function delete($uuid)
    {
        $userId = session()->get('user_id');
        $cert   = $this->certModel->where(['uuid' => $uuid, 'user_id' => $userId])->first();

        if ($cert) {
            if ($cert['certificate_file'] && file_exists(ROOTPATH . 'public/uploads/certifications/' . $cert['certificate_file'])) {
                unlink(ROOTPATH . 'public/uploads/certifications/' . $cert['certificate_file']);
            }
            $this->certModel->delete($cert['id']);
        }

        return redirect()->to('/applicant/certification')->with('success', 'Certification deleted successfully.');
    }
}

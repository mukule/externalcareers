<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UserCertificationModel;
use App\Models\CertifyingBodyModel;

class UserCertificationController extends BaseController
{
    protected $certModel;
    protected $bodyModel;

    public function __construct()
    {
        $this->certModel = new UserCertificationModel();
        $this->bodyModel = new CertifyingBodyModel();
    }

    public function index()
    {
        $userId = session()->get('user_id');

        $certifications = $this->certModel
            ->select('user_certifications.*, certifying_bodies.name AS body_name')
            ->join('certifying_bodies', 'certifying_bodies.id = user_certifications.certifying_body_id', 'left')
            ->where('user_certifications.user_id', $userId)
            ->orderBy('attained_date', 'DESC')
            ->findAll();

        return view('applicant/certifications', [
            'title'          => 'My Certifications',
            'certifications' => $certifications,
            'currentStep'    => 6
        ]);
    }

    // =========================
    // CREATE
    // =========================
    public function create()
    {
        return view('applicant/certification_form', [
            'title'         => 'Add Certification',
            'action'        => base_url('applicant/certification/store'),
            'certification' => null,
            'currentStep'   => 6,
            'bodies'        => $this->bodyModel->where('active', 1)->findAll()
        ]);
    }

    // =========================
    // STORE
    // =========================
    public function store()
    {
        $userId = session()->get('user_id');
        $data   = $this->request->getPost();

        $validation = \Config\Services::validation();
        $validation->setRules([
            'name'                => 'required|string|max_length[255]',
            'certifying_body_id'  => 'required|integer',
            'attained_date'       => 'required|valid_date',
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('error', $validation->getErrors());
        }

        // FILE UPLOAD (1MB MAX)
        $file = $this->request->getFile('certificate_file');
        $certificatePath = null;

        if ($file && $file->getError() !== 4) {

            if (!$file->isValid()) {
                return redirect()->back()->withInput()->with('error', [
                    'certificate_file' => 'Invalid file upload.'
                ]);
            }

            if ($file->getSize() > (1 * 1024 * 1024)) {
                return redirect()->back()->withInput()->with('error', [
                    'certificate_file' => 'File must not exceed 1MB.'
                ]);
            }

            if ($file->getMimeType() !== 'application/pdf') {
                return redirect()->back()->withInput()->with('error', [
                    'certificate_file' => 'Only PDF files are allowed.'
                ]);
            }

            $uploadPath = ROOTPATH . 'public/uploads/certs/';
            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0777, true);
            }

            $certificatePath = $file->getRandomName();
            $file->move($uploadPath, $certificatePath);
        }

        $this->certModel->insert([
            'uuid'               => uniqid(),
            'user_id'            => $userId,
            'name'               => $data['name'],
            'certifying_body_id' => $data['certifying_body_id'],
            'attained_date'      => $data['attained_date'],
            'certificate_file'   => $certificatePath,
            'active'             => 1
        ]);

        return redirect()->to('/applicant/certification')
            ->with('success', 'Certification added successfully.');
    }

    // =========================
    // EDIT
    // =========================
    public function edit($uuid)
    {
        $userId = session()->get('user_id');

        $cert = $this->certModel
            ->where(['uuid' => $uuid, 'user_id' => $userId])
            ->first();

        if (!$cert) {
            return redirect()->back()->with('error', 'Certification not found.');
        }

        return view('applicant/certification_form', [
            'title'         => 'Edit Certification',
            'action'        => base_url('applicant/certification/update'),
            'certification' => $cert,
            'currentStep'   => 6,
            'bodies'        => $this->bodyModel->where('active', 1)->findAll()
        ]);
    }

    // =========================
    // UPDATE
    // =========================
    public function update()
    {
        $userId = session()->get('user_id');
        $data   = $this->request->getPost();

        $cert = $this->certModel
            ->where(['id' => $data['id'], 'user_id' => $userId])
            ->first();

        if (!$cert) {
            return redirect()->back()->withInput()->with('error', 'Certification not found.');
        }

        $validation = \Config\Services::validation();
        $validation->setRules([
            'name'                => 'required|string|max_length[255]',
            'certifying_body_id'  => 'required|integer',
            'attained_date'       => 'required|valid_date',
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('error', $validation->getErrors());
        }

        try {
            $file = $this->request->getFile('certificate_file');
            $certificateName = $cert['certificate_file'];

            if ($file && $file->getError() !== 4) {

                if (!$file->isValid()) {
                    return redirect()->back()->withInput()->with('error', [
                        'certificate_file' => 'Invalid file upload.'
                    ]);
                }

                if ($file->getSize() > (1 * 1024 * 1024)) {
                    return redirect()->back()->withInput()->with('error', [
                        'certificate_file' => 'File must not exceed 1MB.'
                    ]);
                }

                if ($file->getMimeType() !== 'application/pdf') {
                    return redirect()->back()->withInput()->with('error', [
                        'certificate_file' => 'Only PDF files are allowed.'
                    ]);
                }

                $uploadPath = ROOTPATH . 'public/uploads/certs/';

                if (!is_dir($uploadPath)) {
                    mkdir($uploadPath, 0777, true);
                }

                if (!empty($cert['certificate_file'])) {
                    $old = $uploadPath . $cert['certificate_file'];
                    if (is_file($old)) {
                        unlink($old);
                    }
                }

                $certificateName = $file->getRandomName();
                $file->move($uploadPath, $certificateName);
            }

            $this->certModel->update($data['id'], [
                'name'               => $data['name'],
                'certifying_body_id' => $data['certifying_body_id'],
                'attained_date'      => $data['attained_date'],
                'certificate_file'   => $certificateName,
            ]);

        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }

        return redirect()->to('/applicant/certification')
            ->with('success', 'Certification updated successfully.');
    }

    // =========================
    // DELETE
    // =========================
    public function delete($uuid)
    {
        $userId = session()->get('user_id');

        $cert = $this->certModel
            ->where(['uuid' => $uuid, 'user_id' => $userId])
            ->first();

        if ($cert) {

            if (!empty($cert['certificate_file'])) {
                $path = ROOTPATH . 'public/uploads/certs/' . $cert['certificate_file'];
                if (is_file($path)) {
                    unlink($path);
                }
            }

            $this->certModel->delete($cert['id']);
        }

        return redirect()->to('/applicant/certification')
            ->with('success', 'Certification deleted successfully.');
    }
}
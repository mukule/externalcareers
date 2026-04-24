<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UserMembershipModel;
use App\Models\CertifyingBodyModel;

class UserMembershipController extends BaseController
{
    protected $membershipModel;
    protected $bodyModel;

    public function __construct()
    {
        $this->membershipModel = new UserMembershipModel();
        $this->bodyModel       = new CertifyingBodyModel();
    }

    // =========================
    // LIST
    // =========================
    public function index()
    {
        $userId = session()->get('user_id');

        $memberships = $this->membershipModel
            ->select('user_memberships.*, certifying_bodies.name AS body_name')
            ->join('certifying_bodies', 'certifying_bodies.id = user_memberships.certifying_body_id', 'left')
            ->where('user_memberships.user_id', $userId)
            ->orderBy('joined_date', 'DESC')
            ->findAll();

        return view('applicant/memberships', [
            'title'       => 'Memberships',
            'memberships' => $memberships,
            'currentStep' => 5
        ]);
    }

    // =========================
    // CREATE
    // =========================
    public function create()
    {
        return view('applicant/membership_form', [
            'title'       => 'Add Membership',
            'action'      => base_url('applicant/membership/store'),
            'membership'  => null,
            'bodies'      => $this->bodyModel->where('active', 1)->findAll(),
            'currentStep' => 5
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
            'name'               => 'required|string|max_length[255]',
            'certifying_body_id' => 'required|integer',
            'membership_no'      => 'permit_empty|string|max_length[100]',
            'joined_date'        => 'required|integer|exact_length[4]',
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('error', $validation->getErrors());
        }

        $joinedYear = (int) $data['joined_date'];

        // =========================
        // FILE UPLOAD (MAX 1MB)
        // =========================
        $certificatePath = null;
        $file = $this->request->getFile('certificate');

        if ($file && $file->getError() !== 4) {

            if (!$file->isValid() || $file->hasMoved()) {
                return redirect()->back()->withInput()->with('error', [
                    'certificate' => 'Invalid file upload.'
                ]);
            }

            if ($file->getSize() > (1 * 1024 * 1024)) {
                return redirect()->back()->withInput()->with('error', [
                    'certificate' => 'File must not exceed 1MB.'
                ]);
            }

            if ($file->getMimeType() !== 'application/pdf') {
                return redirect()->back()->withInput()->with('error', [
                    'certificate' => 'Only PDF files are allowed.'
                ]);
            }

            $certificatePath = $file->getRandomName();
            $file->move(ROOTPATH . 'public/uploads/certs', $certificatePath);
        }

        $this->membershipModel->insert([
            'uuid'               => uniqid(),
            'user_id'            => $userId,
            'name'               => $data['name'],
            'certifying_body_id' => $data['certifying_body_id'],
            'membership_no'      => $data['membership_no'] ?? null,
            'joined_date'        => $joinedYear,
            'certificate'        => $certificatePath,
            'active'             => 1
        ]);

        return redirect()->to('/applicant/membership')
            ->with('success', 'Membership added successfully.');
    }

    // =========================
    // EDIT
    // =========================
    public function edit($uuid)
    {
        $userId = session()->get('user_id');

        $membership = $this->membershipModel
            ->where(['uuid' => $uuid, 'user_id' => $userId])
            ->first();

        if (!$membership) {
            return redirect()->back()->with('error', 'Membership not found.');
        }

        return view('applicant/membership_form', [
            'title'       => 'Edit Membership',
            'action'      => base_url('applicant/membership/update'),
            'membership'  => $membership,
            'bodies'      => $this->bodyModel->where('active', 1)->findAll(),
            'currentStep' => 5
        ]);
    }

    // =========================
    // UPDATE
    // =========================
    public function update()
    {
        $userId = session()->get('user_id');
        $data   = $this->request->getPost();

        $membership = $this->membershipModel
            ->where(['id' => $data['id'], 'user_id' => $userId])
            ->first();

        if (!$membership) {
            return redirect()->back()->withInput()->with('error', 'Membership not found.');
        }

        $validation = \Config\Services::validation();
        $validation->setRules([
            'name'               => 'required|string|max_length[255]',
            'certifying_body_id' => 'required|integer',
            'membership_no'      => 'permit_empty|string|max_length[100]',
            'joined_date'        => 'required|integer|exact_length[4]',
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('error', $validation->getErrors());
        }

        try {
            $file = $this->request->getFile('certificate');
            $certificateName = $membership['certificate'];

            if ($file && $file->getError() !== 4) {

                if (!$file->isValid() || $file->hasMoved()) {
                    return redirect()->back()->withInput()->with('error', [
                        'certificate' => 'Invalid file upload.'
                    ]);
                }

                if ($file->getSize() > (1 * 1024 * 1024)) {
                    return redirect()->back()->withInput()->with('error', [
                        'certificate' => 'File must not exceed 1MB.'
                    ]);
                }

                if ($file->getMimeType() !== 'application/pdf') {
                    return redirect()->back()->withInput()->with('error', [
                        'certificate' => 'Only PDF files are allowed.'
                    ]);
                }

                $oldPath = ROOTPATH . 'public/uploads/certs/' . $membership['certificate'];
                if (!empty($membership['certificate']) && is_file($oldPath)) {
                    unlink($oldPath);
                }

                $certificateName = $file->getRandomName();
                $file->move(ROOTPATH . 'public/uploads/certs', $certificateName);
            }

            $this->membershipModel->update($data['id'], [
                'name'               => $data['name'],
                'certifying_body_id' => $data['certifying_body_id'],
                'membership_no'      => $data['membership_no'] ?? null,
                'joined_date'        => (int) $data['joined_date'],
                'certificate'        => $certificateName,
            ]);

        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }

        return redirect()->to('/applicant/membership')
            ->with('success', 'Membership updated successfully.');
    }

    // =========================
    // DELETE
    // =========================
    public function delete($uuid)
    {
        $userId = session()->get('user_id');

        $membership = $this->membershipModel
            ->where(['uuid' => $uuid, 'user_id' => $userId])
            ->first();

        if ($membership) {

            $path = ROOTPATH . 'public/uploads/certs/' . $membership['certificate'];

            if (!empty($membership['certificate']) && is_file($path)) {
                unlink($path);
            }

            $this->membershipModel->delete($membership['id']);
        }

        return redirect()->to('/applicant/membership')
            ->with('success', 'Membership deleted successfully.');
    }
}
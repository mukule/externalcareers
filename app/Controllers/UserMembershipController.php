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

    // List all memberships
   
    public function index()
{
    $userId = session()->get('user_id');

    // Join user_memberships with certifying_bodies to get body name
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


    // Show create form
    public function create()
    {
        $bodies = $this->bodyModel->where('active', 1)->findAll();

        return view('applicant/membership_form', [
            'title'      => 'Add Membership',
            'action'     => base_url('applicant/membership/store'),
            'membership' => null,
            'bodies'     => $bodies,
            'currentStep' => 5
        ]);
    }

    // Store new membership
    public function store()
    {
        $userId = session()->get('user_id');
        $data   = $this->request->getPost();

        $validation = \Config\Services::validation();
        $validation->setRules([
            'name'               => 'required|string|max_length[255]',
            'certifying_body_id' => 'required|integer',
            'membership_no'      => 'permit_empty|string|max_length[100]',
            'joined_date'        => 'required|valid_date',
            'certificate'        => 'permit_empty|uploaded[certificate]|max_size[certificate,2048]|ext_in[certificate,pdf]',
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('error', $validation->getErrors());
        }

        // Handle certificate upload
        $certificatePath = null;
        if ($file = $this->request->getFile('certificate')) {
            if ($file->isValid() && !$file->hasMoved()) {
                $certificatePath = $file->getRandomName();
                $file->move(ROOTPATH . 'public/uploads/memberships', $certificatePath);
            }
        }

        $this->membershipModel->insert(array_merge($data, [
            'uuid'        => uniqid(),
            'user_id'     => $userId,
            'certificate' => $certificatePath,
            'active'      => 1
        ]));

        return redirect()->to('/applicant/membership')->with('success', 'Membership added successfully.');
    }

    // Show edit form
    public function edit($uuid)
    {
        $userId = session()->get('user_id');
        $membership = $this->membershipModel->where(['uuid' => $uuid, 'user_id' => $userId])->first();

        if (!$membership) {
            return redirect()->back()->withInput()->with('error', 'Membership not found.');
        }

        $bodies = $this->bodyModel->where('active', 1)->findAll();

        return view('applicant/membership_form', [
            'title'      => 'Edit Membership',
            'action'     => base_url('applicant/membership/update'),
            'membership' => $membership,
            'bodies'     => $bodies,
            'currentStep' => 4
        ]);
    }

    // Update membership
    public function update()
    {
        $userId = session()->get('user_id');
        $data   = $this->request->getPost();

        $membership = $this->membershipModel->where(['id' => $data['id'], 'user_id' => $userId])->first();
        if (!$membership) {
            return redirect()->back()->withInput()->with('error', 'Membership not found.');
        }

        $validation = \Config\Services::validation();
        $validation->setRules([
            'name'               => 'required|string|max_length[255]',
            'certifying_body_id' => 'required|integer',
            'membership_no'      => 'permit_empty|string|max_length[100]',
            'joined_date'        => 'required|valid_date',
            'certificate'        => 'permit_empty|uploaded[certificate]|max_size[certificate,2048]|ext_in[certificate,pdf]',
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('error', $validation->getErrors());
        }

        // Handle new certificate upload
        if ($file = $this->request->getFile('certificate')) {
            if ($file->isValid() && !$file->hasMoved()) {
                if ($membership['certificate'] && file_exists(ROOTPATH . 'public/uploads/memberships/' . $membership['certificate'])) {
                    unlink(ROOTPATH . 'public/uploads/memberships/' . $membership['certificate']);
                }
                $data['certificate'] = $file->getRandomName();
                $file->move(ROOTPATH . 'public/uploads/memberships', $data['certificate']);
            }
        }

        $this->membershipModel->update($data['id'], $data);

        return redirect()->to('/applicant/membership')->with('success', 'Membership updated successfully.');
    }

    // Delete membership
    public function delete($uuid)
    {
        $userId = session()->get('user_id');
        $membership = $this->membershipModel->where(['uuid' => $uuid, 'user_id' => $userId])->first();

        if ($membership) {
            if ($membership['certificate'] && file_exists(ROOTPATH . 'public/uploads/memberships/' . $membership['certificate'])) {
                unlink(ROOTPATH . 'public/uploads/memberships/' . $membership['certificate']);
            }
            $this->membershipModel->delete($membership['id']);
        }

        return redirect()->to('/applicant/membership')->with('success', 'Membership deleted successfully.');
    }
}
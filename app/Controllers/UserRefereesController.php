<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UserRefereesModel;

class UserRefereesController extends BaseController
{
    protected $refereeModel;
    protected $validationRules = [
        'name'         => 'required|string|max_length[255]',
        'organization' => 'permit_empty|string|max_length[255]',
        'position'     => 'permit_empty|string|max_length[255]',
        'email'        => 'permit_empty|valid_email|max_length[255]',
        'phone'        => 'permit_empty|max_length[50]',
        'relationship' => 'permit_empty|string|max_length[255]',
    ];

    public function __construct()
    {
        $this->refereeModel = new UserRefereesModel();
    }

    /** Show list of referees */
    public function index()
    {
        $referees = $this->refereeModel
            ->where('user_id', session()->get('user_id'))
            ->where('active', 1)
            ->orderBy('id', 'DESC')
            ->findAll();

        return view('applicant/referees', [
            'title'    => 'My Referees',
            'referees' => $referees,
            'currentStep' => 7
        ]);
    }

    /** Show create form */
    public function create()
    {
        return view('applicant/referee_form', [
            'title'  => 'Add Referee',
            'action' => base_url('applicant/referees/store'),
            'currentStep' => 7
        ]);
    }

    /** Store new or update existing referee */
    public function store()
    {
        $userId = session()->get('user_id');
        $data   = $this->request->getPost();

        // Validate input
        if (!$this->validate($this->validationRules)) {
            return redirect()->back()->withInput()->with('error', $this->validator->getErrors());
        }

        // Update existing referee
        if (!empty($data['id'])) {
            $ref = $this->refereeModel
                        ->where(['id' => $data['id'], 'user_id' => $userId])
                        ->first();

            if (!$ref) {
                return redirect()->back()->with('error', 'Referee not found.');
            }

            $this->refereeModel->update($data['id'], $data);

            return redirect()->to('applicant/referees')->with('success', 'Referee updated successfully.');
        }

        // Insert new referee
        $data['user_id'] = $userId;
        $data['active']  = 1;

        $this->refereeModel->insert($data);

        return redirect()->to('applicant/referees')->with('success', 'Referee added successfully.');
    }

    /** Show edit form */
    public function edit($uuid)
    {
        $referee = $this->refereeModel
                        ->where(['uuid' => $uuid, 'user_id' => session()->get('user_id')])
                        ->first();

        if (!$referee) {
            return redirect()->back()->with('error', 'Referee not found.');
        }

        return view('applicant/referee_form', [
            'title'   => 'Edit Referee',
            'action'  => base_url('applicant/referees/store'),
            'referee' => $referee,
            'currentStep' => 7
        ]);
    }

    /** Delete referee */
    public function delete($uuid)
    {
        $referee = $this->refereeModel
                        ->where(['uuid' => $uuid, 'user_id' => session()->get('user_id')])
                        ->first();

        if ($referee) {
            $this->refereeModel->delete($referee['id']);
        }

        return redirect()->to('applicant/referees')->with('success', 'Referee deleted successfully.');
    }
}

<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UserEducationModel;
use App\Models\EducationLevelModel;
use App\Models\FieldOfStudyModel;

class UserEducationController extends BaseController
{
    protected $educationModel;
    protected $levelModel;
    protected $fieldOfStudyModel;

    public function __construct()
    {
        $this->educationModel = new UserEducationModel();
        $this->levelModel     = new EducationLevelModel();
        $this->fieldOfStudyModel = new FieldOfStudyModel();
    }

    /**
     * List all education records (index page)
     */
    public function index()
    {
        $userId = session()->get('user_id');

        return view('applicant/education', [
            'title'      => 'My Education',
            'educations' => $this->educationModel->getByUser($userId),
            'currentStep' => 3
        ]);
    }

    /**
     * Show form to create new education
     */
   
    public function create()
{
    // Get all active education levels
    $levels = $this->levelModel
                   ->where('active', 1)
                   ->orderBy('index', 'ASC')
                   ->findAll();

    // Define certifications for 8-4-4 and CBC
    $certifications = [
        '8-4-4' => ['KCPE', 'KCSE'],
        'CBC'   => ['CBC Primary Assessment', 'CBC Junior Secondary', 'CBC Senior Secondary']
    ];

    // Get all fields of study
    $fieldsOfStudy = model(\App\Models\FieldOfStudyModel::class)
                       ->where('active', 1)
                       ->orderBy('name', 'ASC')
                       ->findAll();

    return view('applicant/education_form', [
        'title'           => 'Add New Education',
        'action'          => base_url('applicant/education/store'),
        'levels'          => $levels,
        'certifications'  => $certifications,
        'fieldsOfStudy'   => $fieldsOfStudy,
        'currentStep'     => 3
    ]);
}


    /**
     * Save new education record
     */
    public function store()
    {
        $userId = session()->get('user_id');
        $data   = $this->request->getPost();

        $validation = \Config\Services::validation();
        $validation->setRules([
            'level_id'    => 'required|integer',
            'institution' => 'required|string|max_length[255]',
            'course'      => 'required|string|max_length[255]',
            'grade'       => 'permit_empty|string|max_length[50]',
            'start_year'  => 'permit_empty|integer|exact_length[4]',
            'end_year'    => 'permit_empty|integer|exact_length[4]',
            'certificate' => 'permit_empty|uploaded[certificate]|max_size[certificate,2048]|ext_in[certificate,pdf]',
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('error', $validation->getErrors());
        }
        

        // Handle certificate upload
        $certificatePath = null;
        if ($file = $this->request->getFile('certificate')) {
            if ($file->isValid() && !$file->hasMoved()) {
                $certificatePath = $file->getRandomName();
                $file->move(ROOTPATH . 'public/uploads/certificates', $certificatePath);
            }
        }

        $this->educationModel->insert(array_merge($data, [
            'user_id'     => $userId,
            'active'      => 1,
            'certificate' => $certificatePath,
        ]));

        return redirect()->to('/applicant/education')->with('success', 'Education added successfully.');
    }

    /**
     * Show form to edit an existing education record
     */
   public function edit($uuid)
{
    $userId = session()->get('user_id');
    $education = $this->educationModel
                      ->where(['uuid' => $uuid, 'user_id' => $userId])
                      ->first();

    if (!$education) {
        throw new \CodeIgniter\Exceptions\PageNotFoundException('Education record not found.');
    }

    // Define certifications grouped by system
    $certifications = [
        '8-4-4 System' => ['KCPE', 'KCSE'],
        'CBC System'   => ['Grade 6', 'Grade 9', 'Grade 12']
    ];

    // Fetch all fields of study
    $fieldsOfStudyModel = new \App\Models\FieldOfStudyModel();
    $fieldsOfStudy = $fieldsOfStudyModel->where('active', 1)->orderBy('name', 'ASC')->findAll();

    return view('applicant/education_form', [
        'title'          => 'Edit Education',
        'action'         => base_url('applicant/education/update'),
        'levels'         => $this->levelModel->where('active', 1)->orderBy('index', 'ASC')->findAll(),
        'edu'            => $education,
        'certifications' => $certifications,
        'fieldsOfStudy'  => $fieldsOfStudy,
        'currentStep'    => 3
    ]);
}


public function update()
{
    $userId = session()->get('user_id');
    $data   = $this->request->getPost();

    // Fetch existing education record
    $education = $this->educationModel
                      ->where(['id' => $data['id'], 'user_id' => $userId])
                      ->first();
    if (!$education) {
        return redirect()->back()->with('error', 'Education record not found.');
    }

    // Validation rules
    $validation = \Config\Services::validation();
    $validation->setRules([
        'level_id'    => 'required|integer',
        'institution' => 'required|string|max_length[255]',
        'course'      => 'required|string|max_length[255]', // Certification (required)
        'field_id'    => 'permit_empty|integer',            // Field of Study (optional)
        'grade'       => 'permit_empty|string|max_length[50]',
        'start_year'  => 'permit_empty|integer|exact_length[4]',
        'end_year'    => 'permit_empty|integer|exact_length[4]',
        'certificate' => 'permit_empty|uploaded[certificate]|max_size[certificate,2048]|ext_in[certificate,pdf]',
    ]);

    // Run validation
    if (!$validation->withRequest($this->request)->run()) {
        return redirect()->back()->withInput()->with('error', $validation->getErrors());
    }

    // Handle certificate file upload
    if ($file = $this->request->getFile('certificate')) {
        if ($file->isValid() && !$file->hasMoved()) {
            // Delete old file if it exists
            if (!empty($education['certificate']) && file_exists(ROOTPATH . 'public/uploads/certificates/' . $education['certificate'])) {
                unlink(ROOTPATH . 'public/uploads/certificates/' . $education['certificate']);
            }

            $data['certificate'] = $file->getRandomName();
            $file->move(ROOTPATH . 'public/uploads/certificates', $data['certificate']);
        }
    }

    // Prepare only allowed fields for update
    $updateData = [
        'level_id'    => $data['level_id'],
        'institution' => $data['institution'],
        'course'      => $data['course'],           // Certification
        'field_id'    => $data['field_id'] ?? null, // Field of Study (optional)
        'grade'       => $data['grade'] ?? null,
        'start_year'  => $data['start_year'] ?? null,
        'end_year'    => $data['end_year'] ?? null,
    ];

    if (isset($data['certificate'])) {
        $updateData['certificate'] = $data['certificate'];
    }

    // Update the record
    $this->educationModel->update($data['id'], $updateData);

    return redirect()->to('/applicant/education')->with('success', 'Education updated successfully.');
}


  
    public function delete($uuid)
    {
        $userId = session()->get('user_id');
        $education = $this->educationModel->where(['uuid' => $uuid, 'user_id' => $userId])->first();

        if ($education) {
            // Delete certificate file if exists
            if ($education['certificate'] && file_exists(ROOTPATH . 'public/uploads/certificates/' . $education['certificate'])) {
                unlink(ROOTPATH . 'public/uploads/certificates/' . $education['certificate']);
            }

            $this->educationModel->delete($education['id']);
        }

        return redirect()->to('/applicant/education')->with('success', 'Education deleted successfully.');
    }
}

<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\CountyModel;
use CodeIgniter\Controller;

class CountyController extends BaseController
{
    protected $countyModel;

    public function __construct()
    {
        $this->countyModel = new CountyModel();
    }

  
    public function index()
    {
        $counties = $this->countyModel->findAll();
        return view('admin/counties', ['counties' => $counties]);
    }

   
    public function add()
    {
        helper('form');

        if ($this->request->getMethod() === 'POST') {
            $data = [
                'name' => $this->request->getPost('name'),
                'code' => $this->request->getPost('code'),
            ];

            $this->countyModel->save($data);

            return redirect()->to('/admin/counties')->with('success', 'County added successfully.');
        }

        return view('admin/county_form', [
            'action' => 'add',
            'county' => null
        ]);
    }

    /**
     * Edit a county by UUID
     */
    public function edit($uuid)
    {
        helper('form');

        if ($this->request->getMethod() !== 'POST') {
            log_message('debug', 'County Edit UUID received: ' . $uuid);

            $county = $this->countyModel->getCountyByUUID($uuid);

            if (!$county) {
                return redirect()->back()->with('error', 'County not found.');
            }

            return view('admin/county_form', [
                'action' => 'edit',
                'county' => $county
            ]);
        }

        // POST request: save edited data
        $data = [
            'id'   => $this->request->getPost('id'),
            'name' => $this->request->getPost('name'),
            'code' => $this->request->getPost('code'),
        ];

        $this->countyModel->save($data);

        return redirect()->to('/admin/counties')->with('success', 'County updated successfully.');
    }

    /**
     * Delete a county by UUID
     */
    public function delete($uuid)
    {
        $county = $this->countyModel->getCountyByUUID($uuid);
        if ($county) {
            $this->countyModel->delete($county['id']);
        }

        return redirect()->to('/admin/counties')->with('success', 'County deleted successfully.');
    }
}

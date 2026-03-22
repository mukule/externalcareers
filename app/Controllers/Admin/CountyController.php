<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\CountyModel;

class CountyController extends BaseController
{
    protected $countyModel;

    public function __construct()
    {
        $this->countyModel = new CountyModel();
    }

    /**
     * List all counties
     */
    public function index()
    {
        $counties = $this->countyModel->findAll();
        return view('admin/counties', ['counties' => $counties]);
    }

    /**
     * Add a new county
     */
    public function add()
    {
        helper('form');

        if ($this->request->getMethod() === 'POST') {
            $data = [
                'title'        => $this->request->getPost('title'),
                'active'       => 1,
                'date_created' => date('Y-m-d H:i:s'),
            ];

            $this->countyModel->insert($data);

            return redirect()->to('/admin/counties')->with('success', 'County added successfully.');
        }

        return view('admin/county_form', [
            'action' => 'add',
            'county' => null
        ]);
    }

    /**
     * Edit a county by ID
     */
    public function edit($id)
    {
        helper('form');

        $county = $this->countyModel->find($id);
        if (!$county) {
            return redirect()->back()->with('error', 'County not found.');
        }

        if ($this->request->getMethod() === 'POST') {
            $data = [
                'id'    => $id,
                'title' => $this->request->getPost('title'),
                'active'=> $this->request->getPost('active') ? 1 : 0,
            ];

            $this->countyModel->save($data);

            return redirect()->to('/admin/counties')->with('success', 'County updated successfully.');
        }

        return view('admin/county_form', [
            'action' => 'edit',
            'county' => $county
        ]);
    }

    /**
     * Delete a county by ID
     */
    public function delete($id)
    {
        $county = $this->countyModel->find($id);
        if ($county) {
            $this->countyModel->delete($id);
            return redirect()->to('/admin/counties')->with('success', 'County deleted successfully.');
        }

        return redirect()->back()->with('error', 'County not found.');
    }
}
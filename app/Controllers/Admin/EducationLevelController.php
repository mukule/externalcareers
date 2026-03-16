<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\EducationLevelModel;

class EducationLevelController extends BaseController
{
    protected $educationLevelModel;

    public function __construct()
    {
        $this->educationLevelModel = new EducationLevelModel();
    }

    /** List all education levels ordered by index */
    public function index()
    {
        return view('admin/education_levels', [
            'title' => 'Education Levels',
            'educationLevels' => $this->educationLevelModel->orderBy('index', 'ASC')->findAll()
        ]);
    }

    /** Show form to create a new education level */
    public function create()
    {
        return view('admin/create_education_level', [
            'title' => 'Add Education Level',
            'action' => base_url('admin/education-levels/store')
        ]);
    }

    /** Store new education level */
    public function store()
    {
        $data = $this->request->getPost();

        $validation = \Config\Services::validation();
        $validation->setRules([
            'name'  => 'required|is_unique[education_levels.name]',
            'index' => 'required|integer'
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('error', $validation->getErrors());
        }

        $index = (int)$data['index'];

        // Shift existing levels with index >= new index
        $this->educationLevelModel->builder()
            ->where('`index` >=', $index)
            ->set('`index`', '`index` + 1', false)
            ->update();

        $this->educationLevelModel->insert([
            'name'   => $data['name'],
            'index'  => $index,
            'active' => isset($data['active']) ? 1 : 0
        ]);

        return redirect()->to('/admin/education-levels')->with('success', 'Education Level added successfully.');
    }

    /** Show form to edit an existing education level */
    public function edit($uuid)
    {
        $level = $this->educationLevelModel->where('uuid', $uuid)->first();
        if (!$level) throw new \CodeIgniter\Exceptions\PageNotFoundException('Education Level not found.');

        return view('admin/create_education_level', [
            'title'  => 'Edit Education Level',
            'action' => base_url("admin/education-levels/update"),
            'level'  => $level
        ]);
    }

    /** Update an existing education level */
    public function update()
    {
        $data = $this->request->getPost();
        $id = $data['id'];

        $level = $this->educationLevelModel->find($id);
        if (!$level) return redirect()->back()->with('error', 'Education Level not found.');

        $validation = \Config\Services::validation();
        $validation->setRules([
            'name'  => "required|is_unique[education_levels.name,id,{$id}]",
            'index' => 'required|integer'
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('error', $validation->getErrors());
        }

        $newIndex = (int)$data['index'];
        $oldIndex = (int)$level['index'];

        if ($newIndex !== $oldIndex) {
            if ($newIndex > $oldIndex) {
                // Decrement indices in the gap
                $this->educationLevelModel->builder()
                    ->where('`index` >', $oldIndex)
                    ->where('`index` <=', $newIndex)
                    ->set('`index`', '`index` - 1', false)
                    ->update();
            } else {
                // Increment indices in the gap
                $this->educationLevelModel->builder()
                    ->where('`index` >=', $newIndex)
                    ->where('`index` <', $oldIndex)
                    ->set('`index`', '`index` + 1', false)
                    ->update();
            }
        }

        $this->educationLevelModel->update($id, [
            'name'   => $data['name'],
            'index'  => $newIndex,
            'active' => isset($data['active']) ? 1 : 0
        ]);

        return redirect()->to('/admin/education-levels')->with('success', 'Education Level updated successfully.');
    }

   
    public function delete($uuid)
    {
        $level = $this->educationLevelModel->where('uuid', $uuid)->first();
        if ($level) $this->educationLevelModel->delete($level['id']);

        return redirect()->to('/admin/education-levels')->with('success', 'Education Level deleted successfully.');
    }


    public function reorder()
{
    $data = $this->request->getJSON(true); 

    if (!isset($data['order'])) {
        return $this->response->setStatusCode(400)->setJSON(['error' => 'Invalid data']);
    }

    $this->educationLevelModel->db->transStart();

    foreach ($data['order'] as $item) {
        if (!isset($item['id']) || !isset($item['index'])) continue;

        $this->educationLevelModel
             ->where('uuid', $item['id'])
             ->set('index', (int)$item['index'])
             ->update(); 
    }

    $this->educationLevelModel->db->transComplete();

    return $this->response->setJSON(['status' => 'success']);
}


}

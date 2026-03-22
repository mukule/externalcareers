<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\AdminLogModel;

class AdminLogs extends BaseController
{
    protected $adminLogModel;

    public function __construct()
    {
        $this->adminLogModel = new AdminLogModel();
    }

    public function index()
    {
        
        $filters = [
            'user_name' => $this->request->getGet('user_name'),
            'email'     => $this->request->getGet('email'),
            'action'    => $this->request->getGet('action'),
            'date_from' => $this->request->getGet('date_from'),
            'date_to'   => $this->request->getGet('date_to'),
        ];

        
        $query = $this->adminLogModel->getFilteredLogs($filters);

        
        $logs = $query->paginate(20);

        return view('admin/admin_logs', [
            'title'   => 'Admin Logs',
            'logs'    => $logs,
            'pager'   => $this->adminLogModel->pager,
            'filters' => $filters
        ]);
    }
}
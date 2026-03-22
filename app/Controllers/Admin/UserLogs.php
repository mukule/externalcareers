<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\UserLogModel;

class UserLogs extends BaseController
{
    protected $userLogModel;

    public function __construct()
    {
        $this->userLogModel = new UserLogModel();
    }

    public function index()
    {
        // Get filters from GET parameters
        $filters = [
            'user_name' => $this->request->getGet('user_name'),
            'email'     => $this->request->getGet('email'),
            'action'    => $this->request->getGet('action'),
            'date_from' => $this->request->getGet('date_from'),
            'date_to'   => $this->request->getGet('date_to'),
        ];

        // Apply filters using the model
        $query = $this->userLogModel->getFilteredLogs($filters);

        // Paginate results (20 per page)
        $logs = $query->paginate(20);

        // Pass data to view
        return view('admin/user_logs', [
            'title'   => 'User Logs',
            'logs'    => $logs,
            'pager'   => $this->userLogModel->pager,
            'filters' => $filters
        ]);
    }
}
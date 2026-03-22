<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\MailQueueModel;

class MailQueueController extends BaseController
{
    protected $mailQueueModel;

    public function __construct()
    {
        $this->mailQueueModel = new MailQueueModel();
    }

    /**
     * Display mail queue with filters and pagination
     */
    public function index()
    {
        // Get current page from query string, default to 1
        $page = (int) ($this->request->getGet('page') ?? 1);

        // Get filters from query string
        $filters = [
            'status'    => $this->request->getGet('status'),
            'to_email'  => $this->request->getGet('to_email'),
            'subject'   => $this->request->getGet('subject'),
            'date_from' => $this->request->getGet('date_from'),
            'date_to'   => $this->request->getGet('date_to'),
        ];

        // Fetch filtered data with pagination
        $perPage = 20; // items per page
        $result = $this->mailQueueModel->getFiltered($filters, $perPage, $page);

        // Pass to view
        return view('admin/mail_queue', [
            'emails'     => $result['data'],
            'pagination' => $result,
            'filters'    => $filters,
            'title'      => 'Mail Queue'
        ]);
    }
}
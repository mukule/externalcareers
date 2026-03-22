<?php

namespace App\Models;

use CodeIgniter\Model;

class AdminLogModel extends Model
{
    protected $table = 'admin_logs';
    protected $primaryKey = 'id';

    protected $returnType = 'array';

    protected $allowedFields = ['uid', 'action'];

    // Disable automatic timestamps
    protected $useTimestamps = false;

    /**
     * Get logs with filters + pagination
     */
    public function getFilteredLogs($filters = [])
    {
        $builder = $this->select('admin_logs.*, users.first_name, users.last_name, users.email')
                        ->join('users', 'users.id = admin_logs.uid', 'left');

        if (!empty($filters['user_name'])) {
            $builder->groupStart()
                ->like('users.first_name', $filters['user_name'])
                ->orLike('users.last_name', $filters['user_name'])
                ->groupEnd();
        }

        if (!empty($filters['email'])) {
            $builder->like('users.email', $filters['email']);
        }

        if (!empty($filters['action'])) {
            $builder->like('admin_logs.action', $filters['action']);
        }

        if (!empty($filters['date_from'])) {
            $builder->where('admin_logs.date_created >=', $filters['date_from'] . ' 00:00:00');
        }

        if (!empty($filters['date_to'])) {
            $builder->where('admin_logs.date_created <=', $filters['date_to'] . ' 23:59:59');
        }

        return $builder->orderBy('admin_logs.date_created', 'DESC');
    }
}
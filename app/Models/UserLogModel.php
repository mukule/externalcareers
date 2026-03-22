<?php

namespace App\Models;

use CodeIgniter\Model;

class UserLogModel extends Model
{
    protected $table = 'user_logs';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $allowedFields = ['uid', 'action'];

    // Disable timestamps — DB handles date_created
    protected $useTimestamps = false;

    /**
     * Get logs with filters + pagination
     */
    public function getFilteredLogs($filters = [])
    {
        $builder = $this->select('user_logs.*, users.first_name, users.last_name, users.email')
                        ->join('users', 'users.id = user_logs.uid', 'left');

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
            $builder->like('user_logs.action', $filters['action']);
        }

        if (!empty($filters['date_from'])) {
            $builder->where('user_logs.date_created >=', $filters['date_from'] . ' 00:00:00');
        }

        if (!empty($filters['date_to'])) {
            $builder->where('user_logs.date_created <=', $filters['date_to'] . ' 23:59:59');
        }

        return $builder->orderBy('user_logs.date_created', 'DESC');
    }
}
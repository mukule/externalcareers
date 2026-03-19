<?php

namespace App\Models;

use CodeIgniter\Model;

class GenderModel extends Model
{
    protected $table      = 'gender';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;

    protected $allowedFields = [
        'title',
        'active',
        'date_created',
    ];

    // We are using custom timestamp field (date_created only)
    protected $useTimestamps = false;

    /**
     * Get all active genders
     */
    public function getActive()
    {
        return $this->where('active', 1)
                    ->orderBy('title', 'ASC')
                    ->findAll();
    }

    /**
     * Get gender by ID
     */
    public function getById(int $id)
    {
        return $this->where('id', $id)->first();
    }
}
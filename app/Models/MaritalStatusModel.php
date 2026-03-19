<?php

namespace App\Models;

use CodeIgniter\Model;

class MaritalStatusModel extends Model
{
    protected $table      = 'marital_status';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;

    protected $allowedFields = [
        'title',
        'active',
        'date_created',
    ];

    protected $useTimestamps = false; // you're using date_created manually

    /**
     * Get all active marital statuses
     */
    public function getActive(): array
    {
        return $this->where('active', 1)
                    ->orderBy('title', 'ASC')
                    ->findAll();
    }

    /**
     * Get marital status by ID
     */
    public function getById(int $id): ?array
    {
        return $this->where('id', $id)->first();
    }

    /**
     * Get title by ID (useful for quick lookups)
     */
    public function getTitleById(int $id): ?string
    {
        $row = $this->select('title')->where('id', $id)->first();
        return $row['title'] ?? null;
    }
}
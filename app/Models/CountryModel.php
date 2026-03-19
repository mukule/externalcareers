<?php

namespace App\Models;

use CodeIgniter\Model;

class CountryModel extends Model
{
    protected $table      = 'country';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;

    protected $allowedFields = [
        'title',
        'active',
        'date_created',
    ];

    // Using timestamps for creation
    protected $useTimestamps = true;
    protected $createdField  = 'date_created';
    protected $updatedField  = '';

    /**
     * Get all active countries ordered alphabetically
     */
    public function getActiveCountries(): array
    {
        return $this->where('active', 1)
                    ->orderBy('title', 'ASC')
                    ->findAll();
    }

    /**
     * Get country by ID
     */
    public function getById(int $id): ?array
    {
        return $this->find($id);
    }

    /**
     * Get country by title
     */
    public function getByTitle(string $title): ?array
    {
        return $this->where('title', $title)->first();
    }

    
}
<?php

namespace App\Models;

use CodeIgniter\Model;
use Ramsey\Uuid\Uuid;

class UserBasicEducationModel extends Model
{
    protected $table      = 'user_basic_education';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;

    protected $allowedFields = [
        'uuid',
        'user_id',
        'school_name',
        'certification',
        'date_started',
        'date_ended',
        'grade_attained',
        'certificate',
        'active',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $beforeInsert = ['generateUUID'];

    /**
     * Generate a UUID for the record before insert.
     */
    protected function generateUUID(array $data)
    {
        if (!isset($data['data']['uuid'])) {
            $data['data']['uuid'] = Uuid::uuid4()->toString();
        }
        return $data;
    }

    /**
     * Get all education records for a user.
     */
    public function getByUserId(int $userId): array
    {
        return $this->where('user_id', $userId)
                    ->orderBy('date_ended', 'DESC')
                    ->findAll();
    }

    /**
     * Add or update an education record.
     */
    public function saveEducation(array $data): bool
    {
        if (!empty($data['id'])) {
            return (bool) $this->update($data['id'], $data);
        }

        return (bool) $this->insert($data);
    }

    /**
     * Delete an education record by ID.
     */
    public function deleteEducation(int $id): bool
    {
        return (bool) $this->delete($id, true);
    }
}
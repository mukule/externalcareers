<?php

namespace App\Models;

use CodeIgniter\Model;
use Ramsey\Uuid\Uuid;

class UserHigherEducationModel extends Model
{
    protected $table      = 'user_higher_education';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;

    protected $allowedFields = [
        'uuid',
        'user_id',
        'institution_name',
        'course_name',
        'education_level_id',
        'date_started',
        'date_ended',
        'class_attained',
        'certificate',
        'active',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $beforeInsert = ['generateUUID'];

    /**
     * Generate UUID before insert
     */
    protected function generateUUID(array $data)
    {
        if (!isset($data['data']['uuid'])) {
            $data['data']['uuid'] = Uuid::uuid4()->toString();
        }
        return $data;
    }

    /**
     * Get all higher education records for a user
     * Includes education level name (joined)
     */
    public function getByUserId(int $userId): array
    {
        return $this->select('user_higher_education.*, education_levels.name as level_name, education_levels.index as level_index')
                    ->join('education_levels', 'education_levels.id = user_higher_education.education_level_id', 'left')
                    ->where('user_higher_education.user_id', $userId)
                    ->where('user_higher_education.active', 1)
                    ->orderBy('education_levels.index', 'DESC') // highest first
                    ->orderBy('date_ended', 'DESC')
                    ->findAll();
    }

    /**
     * Save (insert or update) education record
     */
    public function saveEducation(array $data): bool
    {
        if (!empty($data['id'])) {
            return (bool) $this->update($data['id'], $data);
        }

        return (bool) $this->insert($data);
    }

    /**
     * Delete education record
     */
    public function deleteEducation(int $id): bool
    {
        return (bool) $this->delete($id, true);
    }
}
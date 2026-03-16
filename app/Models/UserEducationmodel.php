<?php

namespace App\Models;

use CodeIgniter\Model;
use Ramsey\Uuid\Uuid;

class UserEducationModel extends Model
{
    protected $table      = 'user_education';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useSoftDeletes = false; 

    protected $allowedFields = [
        'uuid',
        'user_id',
        'level_id',
        'field_id',       
        'institution',
        'course',
        'grade',
        'start_year',
        'end_year',
        'certificate',
        'active',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $beforeInsert = ['generateUUID'];

    /**
     * Generate UUID automatically before insert
     */
    protected function generateUUID(array $data)
    {
        if (empty($data['data']['uuid'])) {
            $data['data']['uuid'] = Uuid::uuid4()->toString();
        }
        return $data;
    }

    /**
     * Get a single education record by UUID
     */
    public function getByUuid(string $uuid): ?array
    {
        return $this->where('uuid', $uuid)->first();
    }

    /**
     * Get all active education records for a user
     */
    public function getByUser(int $userId): array
    {
        return $this->select('user_education.*, education_levels.name as level_name, fields_of_study.name as field_name')
                    ->join('education_levels', 'education_levels.id = user_education.level_id', 'left')
                    ->join('fields_of_study', 'fields_of_study.id = user_education.field_id', 'left')
                    ->where('user_education.user_id', $userId)
                    ->where('user_education.active', 1)
                    ->orderBy('user_education.start_year', 'DESC')
                    ->findAll();
    }

    /**
     * Get the highest education level for a user
     */
    public function getHighestLevel(int $userId): ?array
    {
        return $this->select('user_education.*, education_levels.name as level_name, education_levels.index, fields_of_study.name as field_name')
                    ->join('education_levels', 'education_levels.id = user_education.level_id', 'left')
                    ->join('fields_of_study', 'fields_of_study.id = user_education.field_id', 'left')
                    ->where('user_education.user_id', $userId)
                    ->where('user_education.active', 1)
                    ->orderBy('education_levels.index', 'DESC') // higher index = higher level
                    ->first();
    }
}

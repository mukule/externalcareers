<?php

namespace App\Models;

use CodeIgniter\Model;
use Ramsey\Uuid\Uuid;

class EducationLevelModel extends Model
{
    protected $table      = 'education_levels';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;

    protected $allowedFields = [
        'uuid', 'name', 'index', 'active'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $beforeInsert = ['generateUUID'];

    protected function generateUUID(array $data)
    {
        if (!isset($data['data']['uuid'])) {
            $data['data']['uuid'] = Uuid::uuid4()->toString();
        }
        return $data;
    }


    public function qualifies(?int $userEducationLevelId, ?int $requiredEducationLevelId): bool
{
    if (!$userEducationLevelId || !$requiredEducationLevelId) {
        return false;
    }

    $userLevel = $this->find($userEducationLevelId);
    $requiredLevel = $this->find($requiredEducationLevelId);

    if (!$userLevel || !$requiredLevel) {
        return false;
    }

    return (int) $userLevel['index'] >= (int) $requiredLevel['index'];
}

}

<?php

namespace App\Models;

use CodeIgniter\Model;
use Ramsey\Uuid\Uuid;

class JobDisciplineModel extends Model
{
    protected $table = 'job_disciplines';
    protected $primaryKey = 'id';
    protected $allowedFields = ['uuid', 'name', 'display_name'];
    protected $useTimestamps = true;

    // Automatically generate UUID on insert
    protected $beforeInsert = ['generateUuid'];

    protected function generateUuid(array $data)
    {
        if (!isset($data['data']['uuid'])) {
            $data['data']['uuid'] = Uuid::uuid4()->toString();
        }
        return $data;
    }
}

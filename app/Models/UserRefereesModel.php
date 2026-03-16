<?php

namespace App\Models;

use CodeIgniter\Model;
use Ramsey\Uuid\Uuid;

class UserRefereesModel extends Model
{
    protected $table      = 'user_referees';
    protected $primaryKey = 'id';
    protected $returnType = 'array';

    protected $allowedFields = [
        'uuid',
        'user_id',
        'name',
        'organization',
        'position',
        'email',
        'phone',
        'relationship',
        'active'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Callbacks
    protected $beforeInsert = ['generateUUID'];

    /**
     * Automatically generate UUID before insert
     */
    protected function generateUUID(array $data)
    {
        if (empty($data['data']['uuid'])) {
            $data['data']['uuid'] = Uuid::uuid4()->toString();
        }

        return $data;
    }
}

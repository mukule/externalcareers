<?php

namespace App\Models;

use CodeIgniter\Model;
use Ramsey\Uuid\Uuid;

class UserDetailsModel extends Model
{
    protected $table      = 'user_details';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;

    protected $allowedFields = [
        'uuid',
        'user_id',
        'national_id',
        'gender',
        'dob',
        'phone',
        'ethnicity_id',
        'disability_status',
        'disability_number',
        'county_of_origin_id',
        'county_of_residence_id',
        'disability_type',
        'nationality',
        'completed',
        'active',
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

   
    public function isComplete(int $userId): bool
    {
        $details = $this->where('user_id', $userId)
                        ->select('completed')
                        ->first();

        return !empty($details) && $details['completed'] == 1;
    }
}

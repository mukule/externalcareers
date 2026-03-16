<?php

namespace App\Models;

use CodeIgniter\Model;

class ProfessionalStatementModel extends Model
{
    protected $table      = 'professional_statements';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;

    protected $allowedFields = [
        'user_id',
        'statement',
        'completed',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

   
    public function isComplete(int $userId): bool
    {
        $statement = $this->where('user_id', $userId)
                          ->select('completed')
                          ->first();

        return !empty($statement) && $statement['completed'] == 1;
    }
}

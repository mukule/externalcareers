<?php

namespace App\Models;

use CodeIgniter\Model;

class UserMembershipModel extends Model
{
    protected $table      = 'user_memberships';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'uuid', 'user_id', 'name', 'membership_no', 
        'joined_date', 'expiry_date', 'certificate', 'active'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public function getByUser($userId, $onlyActive = true)
{
    $builder = $this->where('user_id', $userId)
                    ->orderBy('joined_date', 'DESC');

    if ($onlyActive) {
        $builder->where('active', 1); // Only active memberships
    }

    return $builder->findAll();
}

}

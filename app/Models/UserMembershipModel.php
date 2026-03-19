<?php

namespace App\Models;

use CodeIgniter\Model;

class UserMembershipModel extends Model
{
    protected $table      = 'user_memberships';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'uuid',
        'user_id',
        'name',
        'certifying_body_id', 
        'membership_no',
        'joined_date',
        'expiry_date',
        'certificate',
        'active'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Get all memberships for a user, optionally only active ones.
     * Join with certifying_bodies to get the body name.
     */
    public function getByUser(int $userId, bool $onlyActive = true): array
    {
        $builder = $this->select('user_memberships.*, certifying_bodies.name AS body_name')
                        ->join('certifying_bodies', 'certifying_bodies.id = user_memberships.certifying_body_id', 'left')
                        ->where('user_memberships.user_id', $userId)
                        ->orderBy('joined_date', 'DESC');

        if ($onlyActive) {
            $builder->where('user_memberships.active', 1);
        }

        return $builder->findAll();
    }
}
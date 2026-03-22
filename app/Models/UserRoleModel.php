<?php

namespace App\Models;

use CodeIgniter\Model;

class UserRoleModel extends Model
{
    protected $table      = 'user_roles';
    protected $primaryKey = 'id';
    protected $returnType = 'array';

    protected $useTimestamps = false; // you can add created_at if needed

    protected $allowedFields = [
        'user_id',
        'role_id',
    ];

    /**
     * Get all roles for a user
     */
    public function getRolesForUser(int $userId): array
    {
        return $this->select('r.*')
                    ->join('roles r', 'r.id = user_roles.role_id')
                    ->where('user_id', $userId)
                    ->findAll();
    }

    /**
     * Check if a user has a specific role
     */
    public function userHasRole(int $userId, string $roleName): bool
    {
        return $this->join('roles r', 'r.id = user_roles.role_id')
                    ->where('user_id', $userId)
                    ->where('r.name', $roleName)
                    ->countAllResults() > 0;
    }
}
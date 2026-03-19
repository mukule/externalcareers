<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table            = 'users';
    protected $primaryKey       = 'id';
    protected $returnType       = 'array';

    protected $useTimestamps    = true;
    protected $createdField     = 'created_at';
    protected $updatedField     = 'updated_at';

    protected $skipValidation   = true;

    protected $allowedFields = [
        'uuid',
        'first_name',
        'last_name',
        'email',
        'password',
        'password_changed',
        'role',
        'access_level',
        'active',
        'activation_token',
        'last_login',
        'created_at',
        'updated_at',
    ];

    protected $beforeInsert = ['addUuid', 'applyDefaultRole', 'hashPassword'];
    protected $beforeUpdate = ['hashPassword'];

    protected function addUuid(array $data)
    {
        if (empty($data['data']['uuid'] ?? null)) {
            $data['data']['uuid'] = $this->generateUuid();
        }
        return $data;
    }

    protected function applyDefaultRole(array $data)
    {
        if (empty($data['data']['role'] ?? null)) {
            $data['data']['role'] = 'applicant';
        }
        return $data;
    }

    protected function hashPassword(array $data)
    {
        if (isset($data['data']['password'])) {
            if (!password_get_info($data['data']['password'])['algo']) {
                $data['data']['password'] = password_hash($data['data']['password'], PASSWORD_DEFAULT);
                if (!isset($data['data']['password_changed'])) {
                    $data['data']['password_changed'] = 1;
                }
            }
        }
        return $data;
    }

    protected function generateUuid(): string
    {
        return sprintf(
            '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff), mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff)
        );
    }

    public function getFullName(int $userId): ?string
    {
        $user = $this->find($userId);
        return $user
            ? trim(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? ''))
            : null;
    }

   
    public function verifyLogin(string $email, string $password): array|string|null
    {
        $user = $this->where('email', $email)->first();

        if (!$user) {
            return null;
        }

        if (!password_verify($password, $user['password'])) {
            return null;
        }

        if (isset($user['active']) && $user['active'] != 1) {
            // User is not activated
            return 'inactive';
        }

        // Update login timestamp
        $this->update($user['id'], [
            'last_login'       => date('Y-m-d H:i:s'),
            'password_changed' => 1,
        ]);

        return $user;
    }

    /**
     * Activate user by token
     */
    public function activateByToken(string $token): bool
    {
        $user = $this->where('activation_token', $token)->first();
        if (!$user) return false;

        $this->update($user['id'], [
            'active'           => 1,
            'activation_token' => null,
        ]);

        return true;
    }

    public function hasAccess(int $userId, int $requiredLevel): bool
    {
        $user = $this->find($userId);
        return $user && isset($user['access_level']) && $user['access_level'] >= $requiredLevel;
    }

    public function countApplicants(): int
    {
        return $this->where('role', 'applicant')->countAllResults();
    }



    public function getApplicantsPaginated(int $perPage = 10, int $page = 1, array $filters = []): array
    {
        $builder = $this->db->table($this->table)
            ->select('users.id, users.first_name, users.last_name, users.email, users.uuid, users.active, users.created_at, users.last_login, user_details.national_id')
            ->join('user_details', 'user_details.user_id = users.id', 'left')
            ->where('users.role', 'applicant');

        // Apply filters
        if (!empty($filters['name'])) {
            $builder->groupStart()
                    ->like('users.first_name', $filters['name'])
                    ->orLike('users.last_name', $filters['name'])
                    ->groupEnd();
        }

        if (!empty($filters['email'])) {
            $builder->like('users.email', $filters['email']);
        }

        if (!empty($filters['national_id'])) {
            $builder->like('user_details.national_id', $filters['national_id']);
        }

        // Clone for total count
        $totalBuilder = clone $builder;
        $total = $totalBuilder->countAllResults(false);

        // Pagination
        $offset = ($page - 1) * $perPage;
        $builder->limit($perPage, $offset);

        $data = $builder->get()->getResultArray();

        return [
            'data'    => $data,
            'total'   => $total,
            'perPage' => $perPage,
            'page'    => $page,
        ];
    }



public function setUserStatus(int $userId, bool $active): bool
{
    $user = $this->find($userId);
    if (!$user) {
        return false; 
    }

    return (bool) $this->update($userId, ['active' => $active ? 1 : 0]);
}




public function deleteUser(int $userId): bool
{
    
    $user = $this->find($userId);
    if (!$user) {
        return false; 
    }

    
    if (!empty($user['role']) && $user['role'] === 'admin') {
        return false; 
    }

    
    $userDetailsModel = new \App\Models\UserDetailsModel();
    $userDetailsModel->where('user_id', $userId)->delete();

    
    return (bool) $this->delete($userId, true); 
}



public function findByEmailAndNationalId(string $email, string $nationalId): ?array
    {
        return $this->db->table($this->table)
            ->select('users.*')
            ->join('user_details', 'user_details.user_id = users.id', 'left')
            ->where('users.email', $email)
            ->where('user_details.national_id', $nationalId)
            ->get()
            ->getRowArray();
    }



}
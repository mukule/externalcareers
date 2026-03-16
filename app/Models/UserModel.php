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
        'active',
        'last_name',
        'email',
        'password',
        'password_changed',
        'role',             
        'access_level',
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

            // Only hash if it's not already hashed
            if (!password_get_info($data['data']['password'])['algo']) {

                $data['data']['password'] = password_hash(
                    $data['data']['password'],
                    PASSWORD_DEFAULT
                );

                // Mark password as changed if not provided
                if (!isset($data['data']['password_changed'])) {
                    $data['data']['password_changed'] = 1;
                }
            }
        }

        return $data;
    }


    /**
     * UUID Generator
     */
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


    /**
     * Get full name
     */
    public function getFullName(int $userId): ?string
    {
        $user = $this->find($userId);

        return $user
            ? trim(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? ''))
            : null;
    }

    /**
     * Validate login using email + password
     */
    public function verifyLogin(string $email, string $password): ?array
    {
        $user = $this->where('email', $email)->first();

        if ($user && password_verify($password, $user['password'])) {

            // Update login timestamp & set password as changed
            $this->update($user['id'], [
                'last_login'       => date('Y-m-d H:i:s'),
                'password_changed' => 1,
            ]);

            return $user;
        }

        return null;
    }

    /**
     * Check if user has required access level
     */
    public function hasAccess(int $userId, int $requiredLevel): bool
    {
        $user = $this->find($userId);

        return $user
            && isset($user['access_level'])
            && $user['access_level'] >= $requiredLevel;
    }


    public function countApplicants(): int
{
    return $this->where('role', 'applicant')->countAllResults();
}
}

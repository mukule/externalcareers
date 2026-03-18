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

    /**
     * Generate a UUID for the user_details record before insert.
     */
    protected function generateUUID(array $data)
    {
        if (!isset($data['data']['uuid'])) {
            $data['data']['uuid'] = Uuid::uuid4()->toString();
        }
        return $data;
    }

    /**
     * Check if the profile for a given user ID is complete.
     */
    public function isComplete(int $userId): bool
    {
        $details = $this->where('user_id', $userId)
                        ->select('completed')
                        ->first();

        return !empty($details) && $details['completed'] == 1;
    }

    /**
     * Get full user details by user ID.
     */
    public function getByUserId(int $userId): ?array
    {
        return $this->where('user_id', $userId)->first();
    }

    /**
     * Get full user details joined with the main users table by user ID.
     */
    public function getFullUserByUserId(int $userId): ?array
    {
        $builder = $this->db->table($this->table)
            ->select('users.id AS user_id, users.uuid AS user_uuid, users.first_name, users.last_name, users.email, users.active AS user_active, users.role, users.last_login, user_details.*')
            ->join('users', 'users.id = user_details.user_id', 'left')
            ->where('user_details.user_id', $userId);

        return $builder->get()->getRowArray();
    }

    /**
     * Get all users with their details, optionally paginated.
     */
    public function getAllUsersWithDetails(int $perPage = 0, int $page = 1): array
    {
        $builder = $this->db->table($this->table)
            ->select('users.id AS user_id, users.uuid AS user_uuid, users.first_name, users.last_name, users.email, users.active AS user_active, users.role, users.last_login, user_details.*')
            ->join('users', 'users.id = user_details.user_id', 'left')
            ->orderBy('users.created_at', 'DESC');

        $total = $builder->countAllResults(false);

        if ($perPage > 0) {
            $offset = ($page - 1) * $perPage;
            $builder->limit($perPage, $offset);
        }

        $data = $builder->get()->getResultArray();

        return [
            'data'    => $data,
            'total'   => $total,
            'perPage' => $perPage,
            'page'    => $page,
        ];
    }
}
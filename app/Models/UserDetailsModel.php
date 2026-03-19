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
        'gender_id',
        'dob',
        'phone',
        'ethnicity_id',
        'disability_status',
        'disability_number',
        'disability_type',

        // ✅ Counties
        'county_of_origin_id',
        'county_of_residence_id',

        // ✅ Countries (IDs only)
        'country_of_birth_id',
        'country_of_residence_id',

        // ✅ NEW: marital status FK
        'marital_status_id',

        'nationality',
        'field_of_study_id',
        'highest_level_of_study_id',

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

    public function getByUserId(int $userId): ?array
    {
        return $this->where('user_id', $userId)->first();
    }

    public function getFullUserByUserId(int $userId): ?array
    {
        return $this->db->table($this->table)
            ->select('users.id AS user_id, users.uuid AS user_uuid, users.first_name, users.last_name, users.email, users.active AS user_active, users.role, users.last_login, user_details.*')
            ->join('users', 'users.id = user_details.user_id', 'left')
            ->where('user_details.user_id', $userId)
            ->get()
            ->getRowArray();
    }

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

        return [
            'data'    => $builder->get()->getResultArray(),
            'total'   => $total,
            'perPage' => $perPage,
            'page'    => $page,
        ];
    }

    /**
     * ✅ UPDATED Resume Details (NOW FULLY NORMALIZED)
     */
    public function getResumeDetails(int $userId): array
    {
        $result = $this->select('
                user_details.*,

                genders.title AS gender_name,
                ethnicities.name AS ethnicity_name,

                edu_levels.name AS highest_edu_level,
                edu_levels.index AS edu_index,
                fields.name AS study_field,

                cbirth.title AS country_of_birth_name,
                cres.title AS country_of_residence_name,

                coo.title AS county_of_origin_name,
                cor.title AS county_of_residence_name,

                ms.title AS marital_status_name
            ')
            ->join('gender genders', 'genders.id = user_details.gender_id', 'left')
            ->join('ethnicities', 'ethnicities.id = user_details.ethnicity_id', 'left')
            ->join('education_levels edu_levels', 'edu_levels.id = user_details.highest_level_of_study_id', 'left')
            ->join('fields_of_study fields', 'fields.id = user_details.field_of_study_id', 'left')

            // Countries
            ->join('country cbirth', 'cbirth.id = user_details.country_of_birth_id', 'left')
            ->join('country cres', 'cres.id = user_details.country_of_residence_id', 'left')

            // Counties
            ->join('county coo', 'coo.id = user_details.county_of_origin_id', 'left')
            ->join('county cor', 'cor.id = user_details.county_of_residence_id', 'left')

            // Marital Status
            ->join('marital_status ms', 'ms.id = user_details.marital_status_id', 'left')

            ->where('user_details.user_id', $userId)
            ->where('user_details.active', 1)
            ->first();

        if (!$result) {
            return [];
        }

        return [
            'national_id'               => $result['national_id'] ?? '',
            'gender_name'               => $result['gender_name'] ?? '',
            'dob'                       => $result['dob'] ?? '',
            'phone'                     => $result['phone'] ?? '',
            'ethnicity_name'            => $result['ethnicity_name'] ?? '',

            'country_of_birth_name'     => $result['country_of_birth_name'] ?? '',
            'country_of_residence_name' => $result['country_of_residence_name'] ?? '',

            'county_of_origin_name'     => $result['county_of_origin_name'] ?? '',
            'county_of_residence_name'  => $result['county_of_residence_name'] ?? '',

            'marital_status_name'       => $result['marital_status_name'] ?? '',

            'disability_status'         => $result['disability_status'] ?? 0,
            'disability_number'         => $result['disability_number'] ?? '',

            'highest_edu_level'         => $result['highest_edu_level'] ?? '',
            'edu_index'                 => $result['edu_index'] ?? 0,
            'study_field'               => $result['study_field'] ?? '',
        ];
    }

    public function getAllCountries(): array
    {
        return $this->db->table('country')
                        ->where('active', 1)
                        ->orderBy('title', 'ASC')
                        ->get()
                        ->getResultArray();
    }
}
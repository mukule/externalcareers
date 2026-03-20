<?php

namespace App\Models;

use CodeIgniter\Model;
use Ramsey\Uuid\Uuid;

class UserDetailsModel extends Model
{
    protected $table      = 'user_details';
    protected $primaryKey = 'id';

    protected $returnType = 'array';

    protected $allowedFields = [
        'uuid','user_id','national_id','gender_id','dob','phone',
        'ethnicity_id','disability_status','disability_number','disability_type',
        'county_of_origin_id','county_of_residence_id',
        'country_of_birth_id','country_of_residence_id',
        'marital_status_id','nationality',
        'field_of_study_id','highest_level_of_study_id',
        'completed','active'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $beforeInsert = ['generateUUID'];

    protected function generateUUID(array $data)
    {
        if (empty($data['data']['uuid'])) {
            $data['data']['uuid'] = Uuid::uuid4()->toString();
        }
        return $data;
    }

    /**
     * ⚡ FAST: Check completion using EXISTS (no row fetch)
     */
    public function isComplete(int $userId): bool
    {
        return $this->where('user_id', $userId)
                    ->where('completed', 1)
                    ->countAllResults() > 0;
    }

    /**
     * ⚡ FAST: Get user details only (no joins)
     */
    public function getByUserId(int $userId): ?array
    {
        return $this->select('id,user_id,completed,active')
                    ->where('user_id', $userId)
                    ->first();
    }

    /**
     * 🚀 KEYSET PAGINATION (NO OFFSET)
     * Use lastId instead of page number
     */
    public function getUsersFast(int $limit = 20, ?int $lastId = null): array
    {
        $builder = $this->db->table('users u')
            ->select('
                u.id,
                u.first_name,
                u.last_name,
                u.email,
                u.created_at,
                ud.completed
            ')
            ->join('user_details ud', 'ud.user_id = u.id', 'left')
            ->orderBy('u.id', 'DESC')
            ->limit($limit);

        if ($lastId) {
            $builder->where('u.id <', $lastId); // keyset pagination
        }

        $data = $builder->get()->getResultArray();

        return [
            'data' => $data,
            'next_last_id' => !empty($data) ? end($data)['id'] : null
        ];
    }

    /**
     * ⚡ LIGHTWEIGHT LIST (no joins at all)
     * Use this for admin tables when joins are not needed
     */
    public function getUsersLight(int $limit = 20, int $offset = 0): array
    {
        return $this->db->table('users')
            ->select('id, first_name, last_name, email, created_at')
            ->orderBy('id', 'DESC')
            ->limit($limit, $offset)
            ->get()
            ->getResultArray();
    }

    /**
     * ⚡ OPTIMIZED RESUME (minimal joins)
     */
    public function getResumeDetails(int $userId): array
    {
        $row = $this->db->table('user_details ud')
            ->select('
                ud.national_id,
                ud.dob,
                ud.phone,
                ud.disability_status,
                ud.disability_number,

                g.title AS gender,
                ms.title AS marital_status,
                el.name AS education_level
            ')
            ->join('gender g', 'g.id = ud.gender_id', 'left')
            ->join('marital_status ms', 'ms.id = ud.marital_status_id', 'left')
            ->join('education_levels el', 'el.id = ud.highest_level_of_study_id', 'left')
            ->where('ud.user_id', $userId)
            ->where('ud.active', 1)
            ->get()
            ->getRowArray();

        return $row ?? [];
    }
}
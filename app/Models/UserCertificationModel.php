<?php

namespace App\Models;

use CodeIgniter\Model;

class UserCertificationModel extends Model
{
    protected $table      = 'user_certifications';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'uuid',
        'user_id',
        'certification_id',
        'name',
        'attained_date',
        'certificate_file',
        'active'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Get all user certifications with optional join to official certification and body names
     */
    public function getByUser(int $userId): array
    {
        return $this->select('
                        user_certifications.*, 
                        certifications.name AS cert_name, 
                        certifying_bodies.name AS body_name
                    ')
                    ->join('certifications', 'certifications.id = user_certifications.certification_id', 'left')
                    ->join('certifying_bodies', 'certifying_bodies.id = certifications.certifying_body_id', 'left')
                    ->where('user_certifications.user_id', $userId)
                    ->orderBy('attained_date', 'DESC')
                    ->findAll();
    }
}

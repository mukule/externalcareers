<?php

namespace App\Models;

use CodeIgniter\Model;

class JobSpecialityModel extends Model
{
    protected $table      = 'job_specialities';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $allowedFields = [
        'job_id',
        'field_id',
        'created_at',
        'updated_at',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    
  
    public function getByJob(int $jobId): array
{
    return $this->select('fields_of_study.id, fields_of_study.name')
                ->join('fields_of_study', 'fields_of_study.id = job_specialities.field_id')
                ->where('job_specialities.job_id', $jobId)
                ->groupBy('fields_of_study.id') 
                ->findAll();
}


    public function assignFields(int $jobId, array $fieldIds)
    {
        // Remove existing assignments first
        $this->where('job_id', $jobId)->delete();

        $data = [];
        foreach ($fieldIds as $fieldId) {
            $data[] = [
                'job_id'   => $jobId,
                'field_id' => $fieldId,
            ];
        }

        if (!empty($data)) {
            $this->insertBatch($data);
        }

        return true;
    }

    
    public function removeField(int $jobId, int $fieldId)
    {
        return $this->where('job_id', $jobId)
                    ->where('field_id', $fieldId)
                    ->delete();
    }
}

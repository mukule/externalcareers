<?php

namespace App\Models;

use CodeIgniter\Model;
use Ramsey\Uuid\Uuid;

class JobApplicationModel extends Model
{
    protected $table      = 'job_applications';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = [
        'uuid',
        'user_id',
        'job_id',
        'ref_no',
        'qualification',
        'disqualification_reason', 
        'status',
    ];

    protected $useTimestamps = true;  
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $beforeInsert = ['generateUUID', 'generateRefNo', 'setQualificationAndReason'];

    // ----------------------
    // Generate UUID
    // ----------------------
    protected function generateUUID(array $data)
    {
        if (!isset($data['data']['uuid'])) {
            $data['data']['uuid'] = Uuid::uuid4()->toString();
        }
        return $data;
    }

    // ----------------------
    // Generate temporary ref_no
    // ----------------------
    protected function generateRefNo(array $data)
    {
        if (!isset($data['data']['ref_no']) && isset($data['data']['job_id'])) {
            $jobModel = model(\App\Models\JobModel::class);
            $job = $jobModel->find($data['data']['job_id']);

            if ($job) {
                $data['data']['ref_no'] = $job['reference_no'] . '/TEMP';
            }
        }
        return $data;
    }

    // ----------------------
    // Automatically check qualification and set reason
    // ----------------------
    protected function setQualificationAndReason(array $data)
    {
        if (isset($data['data']['user_id'], $data['data']['job_id'])) {
            $jobModel = model(\App\Models\JobModel::class);
            $job = $jobModel->find($data['data']['job_id']);

            if ($job) {
                $result = $jobModel->doesUserQualifyWithReason($data['data']['user_id'], $job);
                $data['data']['qualification'] = $result['qualifies'] ? 'qualify' : 'not qualify';
                $data['data']['disqualification_reason'] = $result['reason'] ?: null;
            }
        }
        return $data;
    }

    // ----------------------
    // Get applications by user
    // ----------------------
    public function getByUser(int $userId)
    {
        return $this->where('user_id', $userId)->orderBy('created_at', 'DESC')->findAll();
    }

    // ----------------------
    // Get applications by job
    // ----------------------
    public function getByJob(int $jobId)
    {
        return $this->where('job_id', $jobId)->orderBy('created_at', 'DESC')->findAll();
    }

    // ----------------------
    // Update status
    // ----------------------
    public function updateStatus(int $id, string $status)
    {
        return $this->update($id, ['status' => $status]);
    }

    // ----------------------
    // Set final ref_no
    // ----------------------
    public function setRefNo(int $id)
    {
        $application = $this->find($id);
        if (!$application) return false;

        $jobModel = model(\App\Models\JobModel::class);
        $job = $jobModel->find($application['job_id']);
        if (!$job) return false;

        $refNo = $job['reference_no'] . '/' . $application['id'];
        return $this->update($id, ['ref_no' => $refNo]);
    }

    // ----------------------
    // Count applications for a job
    // ----------------------
    public function countByJob(int $jobId): int
    {
        return $this->where('job_id', $jobId)->countAllResults();
    }

    // ----------------------
    // Get applications by job (detailed)
    // ----------------------
    public function getApplicationsByJob(int $jobId): array
    {
        return $this->where('job_id', $jobId)
                    ->orderBy('created_at', 'DESC')
                    ->findAll();
    }

 public function getApplicationsByJobWithUsers(array $filters = []): array
{
    $builder = $this->db->table('job_applications ja')
        ->select('
            ja.*, 
            u.uuid AS user_uuid, 
            u.first_name, 
            u.last_name, 
            u.email, 
            u.active, 
            j.reference_no, 
            j.name AS job_name,
            ud.national_id,
            ud.gender
        ')
        ->join('users u', 'u.id = ja.user_id', 'left')
        ->join('user_details ud', 'ud.user_id = u.id', 'left') // join user details
        ->join('jobs j', 'j.id = ja.job_id', 'left')
        ->orderBy('ja.created_at', 'DESC');

    // Apply filters
    if (!empty($filters['user_name'])) {
        $builder->like('CONCAT(u.first_name, " ", u.last_name)', $filters['user_name']);
    }

    if (!empty($filters['email'])) {
        $builder->like('u.email', $filters['email']);
    }

   if (!empty($filters['job_ref'])) {
        $builder->like('ja.ref_no', $filters['job_ref']);
    }


    if (!empty($filters['qualification'])) {
        $builder->where('ja.qualification', $filters['qualification']);
    }

    if (!empty($filters['national_id'])) {
        $builder->like('ud.national_id', $filters['national_id']);
    }

    if (!empty($filters['gender'])) {
        $builder->where('ud.gender', $filters['gender']);
    }

      if (!empty($filters['status'])) {
        $builder->where('ja.status', strtolower($filters['status']));
    }

    return $builder->get()->getResultArray();
}


    // ----------------------
    // Summary of jobs and application counts
    // ----------------------
    public function getJobApplicationsSummary(): array
    {
        $builder = $this->db->table('jobs j')
                            ->select('j.id, j.name, j.reference_no, COUNT(ja.id) AS applications_count')
                            ->join('job_applications ja', 'ja.job_id = j.id', 'left')
                            ->groupBy('j.id')
                            ->orderBy('j.created_at', 'DESC');

        return $builder->get()->getResultArray();
    }

    // ----------------------
    // Get jobs with their applications included
    // ----------------------
    public function getJobsWithApplications(): array
    {
        $jobModel = model(\App\Models\JobModel::class);

        $jobs = $jobModel->findAll();

        foreach ($jobs as &$job) {
            $job['applications'] = $this->getApplicationsByJob($job['id']);
            $job['applications_count'] = count($job['applications']);
        }

        return $jobs;
    }


    
    public function countApplications(): int
    {
        return $this->countAllResults();
    }

    public function countQualifiedApplications(): int
    {
        return $this->where('qualification', 'qualify')->countAllResults();
    }


    public function getJobsWithApplicationCounts(int $limit = 20, int $offset = 0): array
{
    return $this->db->table('jobs j')
        ->select('
            j.id,
            j.name,
            j.reference_no,
            j.created_at,
            COUNT(ja.id) AS applications_count
        ')
        ->join('job_applications ja', 'ja.job_id = j.id', 'left')
        ->groupBy('j.id')
        ->orderBy('j.created_at', 'DESC')
        ->limit($limit, $offset)   // <-- pagination
        ->get()
        ->getResultArray();
}



public function getJobsWithApplicationCountsOnly(array $filters = [], int $limit = 20, int $offset = 0): array
{
    $builder = $this->db->table('jobs j')
        ->select('
            j.id,
            j.name,
            j.uuid,
            j.reference_no,
            j.date_open,
            j.date_close,
            j.job_type_id,
            j.discipline_id,
            j.created_at,
            COUNT(ja.id) AS applications_count
        ')
        ->join('job_applications ja', 'ja.job_id = j.id', 'left')
        ->groupBy('j.id')
        ->orderBy('j.created_at', 'DESC');

    // Apply filters
    if (!empty($filters['name'])) {
        $builder->like('j.name', $filters['name']);
    }

    if (!empty($filters['ref_no'])) {
        $builder->like('j.reference_no', $filters['ref_no']);
    }

    if (!empty($filters['job_type_id'])) {
        $builder->where('j.job_type_id', $filters['job_type_id']);
    }

    if (!empty($filters['discipline_id'])) {
        $builder->where('j.discipline_id', $filters['discipline_id']);
    }

    // Apply pagination
    $builder->limit($limit, $offset);

    return $builder->get()->getResultArray();
}

/**
 * Helper to count total jobs with filters for pagination
 */
public function countJobsWithFilters(array $filters = []): int
{
    $builder = $this->db->table('jobs j')
        ->select('j.id')
        ->join('job_applications ja', 'ja.job_id = j.id', 'left')
        ->groupBy('j.id');

    if (!empty($filters['name'])) {
        $builder->like('j.name', $filters['name']);
    }

    if (!empty($filters['ref_no'])) {
        $builder->like('j.reference_no', $filters['ref_no']);
    }

    if (!empty($filters['job_type_id'])) {
        $builder->where('j.job_type_id', $filters['job_type_id']);
    }

    if (!empty($filters['discipline_id'])) {
        $builder->where('j.discipline_id', $filters['discipline_id']);
    }

    return count($builder->get()->getResultArray());
}


}

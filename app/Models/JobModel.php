<?php

namespace App\Models;

use CodeIgniter\Model;
use Ramsey\Uuid\Uuid;

class JobModel extends Model
{
    protected $table            = 'jobs';
    protected $primaryKey       = 'id';
    protected $returnType       = 'array';

    protected $useTimestamps    = true;
    protected $createdField     = 'created_at';
    protected $updatedField     = 'updated_at';

    protected $allowedFields = [
        'uuid',
        'name',
        'reference_no',
        'job_type_id',
        'discipline_id',
        'job_summary',
        'job_description',
        'posts_needed',
        'reports_to',
        'date_open',
        'date_close',
        'min_education_level_id',
        'work_experience_years',
        'certification_required',
        'membership_required',
        'higher_education_required',
        'created_by',
        'active',
    ];

    protected $beforeInsert = ['generateUUID'];

    protected function generateUUID(array $data)
    {
        if (! isset($data['data']['uuid'])) {
            $data['data']['uuid'] = Uuid::uuid4()->toString();
        }
        return $data;
    }

    // -----------------------
    // Job Status
    // -----------------------
    public function getStatus(array $job): string
    {
        $now   = date('Y-m-d H:i:s');
        $open  = $job['date_open'];
        $close = $job['date_close'];

        if ($now < $open) return 'Upcoming';
        if ($now >= $open && $now <= $close) return 'Open';
        return 'Closed';
    }

    // -----------------------
    // Job Requirements
    // -----------------------
    public function getRequirements(array $job): array
    {
        $requirements = [];
        $requirements['minimum_education'] = null;

        if (! empty($job['min_education_level_id'])) {
            $eduModel = new \App\Models\EducationLevelModel();
            $minEdu = $eduModel->find($job['min_education_level_id']);
            $requirements['minimum_education'] = $minEdu['name'] ?? null;
        }

        $requirements['certifications'] = ! empty($job['certification_required'])
            ? array_map('trim', explode(',', $job['certification_required']))
            : [];

        $requirements['memberships'] = ! empty($job['membership_required'])
            ? array_map('trim', explode(',', $job['membership_required']))
            : [];

        $requirements['work_experience_years'] = $job['work_experience_years'] ?? null;

        return $requirements;
    }

    // -----------------------
    // Qualification Check
    // -----------------------
    public function doesUserQualifyWithReason(int $userId, array $job): array
    {
        $eduModel        = new \App\Models\UserEducationModel();
        $workModel       = new \App\Models\UserWorkExperienceModel();
        $certModel       = new \App\Models\UserCertificationModel();
        $membershipModel = new \App\Models\UserMembershipModel();
        $refereeModel    = new \App\Models\UserRefereesModel();
        $eduLevelModel   = new \App\Models\EducationLevelModel();
        $specialityModel = new \App\Models\JobSpecialityModel();

        $requirements = $this->getRequirements($job);

        // Highest Education Check
        $highestEducation = $eduModel->getHighestLevel($userId);
        if (! empty($job['min_education_level_id']) &&
            ! $eduLevelModel->qualifies(
                $highestEducation['level_id'] ?? null,
                $job['min_education_level_id']
            )
        ) {
            return ['qualifies' => false, 'reason' => 'Education level too low'];
        }

        // Work Experience Check
        $totalExperience = $workModel->getTotalExperienceYears($userId);
        if (! empty($requirements['work_experience_years']) &&
            $totalExperience < $requirements['work_experience_years']
        ) {
            return ['qualifies' => false, 'reason' => 'Insufficient work experience'];
        }

        // Certification Check
        if (! empty($requirements['certifications'])) {
            $userCerts = array_map(
                fn($c) => strtolower(trim($c['name'])),
                array_filter($certModel->getByUser($userId), fn($c) => intval($c['active']) === 1)
            );

            foreach ($requirements['certifications'] as $cert) {
                if (! in_array(strtolower(trim($cert)), $userCerts)) {
                    return [
                        'qualifies' => false,
                        'reason'    => "Missing certification: $cert"
                    ];
                }
            }
        }

        // Membership Check
        $userMemberships = array_filter(
            $membershipModel->getByUser($userId),
            fn($m) => intval($m['active']) === 1
        );
        if (empty($userMemberships)) {
            return ['qualifies' => false, 'reason' => 'No active memberships'];
        }

        // Referee Check
        $userRefs = $refereeModel
            ->where('user_id', $userId)
            ->where('active', 1)
            ->findAll();
        if (count($userRefs) < 3) {
            return ['qualifies' => false, 'reason' => 'Less than 3 referees'];
        }

        // Field of Study Check
        $jobFields = $specialityModel->getByJob($job['id']);
        $userEducations = $eduModel->getByUser($userId);

        $requiredFieldIds = array_map('intval', array_column($jobFields, 'id'));
        $userFieldIds     = array_map('intval', array_filter(array_column($userEducations, 'field_id')));

        if (! empty($requiredFieldIds) && empty(array_intersect($userFieldIds, $requiredFieldIds))) {
            return [
                'qualifies' => false,
                'reason'    => 'Field of study does not match job requirements'
            ];
        }

        return ['qualifies' => true, 'reason' => ''];
    }

    // -----------------------
    // Count total jobs
    // -----------------------
    public function countTotalJobs(): int
    {
        return $this->countAllResults();
    }

    // -----------------------
    // Count currently open jobs
    // -----------------------
    public function countOpenJobs(): int
    {
        $now = date('Y-m-d H:i:s');
        return $this->where('date_open <=', $now)
                    ->where('date_close >=', $now)
                    ->where('active', 1)
                    ->countAllResults();
    }

    // -----------------------
    // Get open jobs by job type
    // -----------------------
    public function getOpenJobsByType(int $jobTypeId, array $filters = []): array
    {
        $now = date('Y-m-d H:i:s');

        $builder = $this->select('jobs.*, education_levels.name AS minimum_education')
            ->join('job_specialities', 'job_specialities.job_id = jobs.id', 'left')
            ->join('education_levels', 'education_levels.id = jobs.min_education_level_id', 'left')
            ->where('jobs.job_type_id', $jobTypeId)
            ->where('jobs.active', 1)
            ->where('jobs.date_open <=', $now)
            ->where('jobs.date_close >=', $now);

        // Apply filters
        if (!empty($filters['name'])) {
            $builder->like('jobs.name', $filters['name']);
        }

        if (!empty($filters['reference_no'])) {
            $builder->like('jobs.reference_no', $filters['reference_no']);
        }

        if (!empty($filters['discipline_id'])) {
            $builder->where('jobs.discipline_id', $filters['discipline_id']);
        }

        if (!empty($filters['field_id'])) {
            $builder->where('job_specialities.field_id', $filters['field_id']);
        }

        return $builder
            ->groupBy('jobs.id')
            ->orderBy('jobs.date_open', 'ASC')
            ->findAll();
    }

    // -----------------------
    // Get open jobs by job type and optional discipline
    // -----------------------
    public function getOpenJobsByTypeAndDiscipline(int $jobTypeId, ?int $disciplineId = null): array
    {
        $now = date('Y-m-d H:i:s');

        $builder = $this->where('job_type_id', $jobTypeId)
                        ->where('active', 1)
                        ->where('date_open <=', $now)
                        ->where('date_close >=', $now);

        if ($disciplineId !== null) {
            $builder->where('discipline_id', $disciplineId);
        }

        return $builder->orderBy('date_open', 'ASC')->findAll();
    }
}
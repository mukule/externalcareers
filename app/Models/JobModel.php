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
    public function getStatus(array $job)
    {
        $today = date('Y-m-d');
        $open  = $job['date_open'];
        $close = $job['date_close'];

        if ($today < $open) return 'Upcoming';
        if ($today >= $open && $today <= $close) return 'Open';
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

        // -----------------------
        // Highest Education Check
        // -----------------------
        $highestEducation = $eduModel->getHighestLevel($userId);

        if (! empty($job['min_education_level_id']) &&
            ! $eduLevelModel->qualifies(
                $highestEducation['level_id'] ?? null,
                $job['min_education_level_id']
            )
        ) {
            return ['qualifies' => false, 'reason' => 'Education level too low'];
        }

        // -----------------------
        // Work Experience Check
        // -----------------------
        $totalExperience = $workModel->getTotalExperienceYears($userId);

        if (! empty($requirements['work_experience_years']) &&
            $totalExperience < $requirements['work_experience_years']
        ) {
            return ['qualifies' => false, 'reason' => 'Insufficient work experience'];
        }

        // -----------------------
        // Certification Check
        // -----------------------
        if (! empty($requirements['certifications'])) {

            $userCerts = array_map(
                fn($c) => strtolower(trim($c['name'])),
                array_filter(
                    $certModel->getByUser($userId),
                    fn($c) => intval($c['active']) === 1
                )
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

        // -----------------------
        // Membership Check
        // -----------------------
        $userMemberships = array_filter(
            $membershipModel->getByUser($userId),
            fn($m) => intval($m['active']) === 1
        );

        if (empty($userMemberships)) {
            return ['qualifies' => false, 'reason' => 'No active memberships'];
        }

        // -----------------------
        // Referee Check
        // -----------------------
        $userRefs = $refereeModel
            ->where('user_id', $userId)
            ->where('active', 1)
            ->findAll();

        if (count($userRefs) < 3) {
            return ['qualifies' => false, 'reason' => 'Less than 3 referees'];
        }

        // -----------------------
        // Field of Study Check (UPDATED + SAFE)
        // -----------------------
        $jobFields = $specialityModel->getByJob($job['id']);
        $userEducations = $eduModel->getByUser($userId);

        $requiredFieldIds = array_map(
            'intval',
            array_column($jobFields, 'id')
        );

        $userFieldIds = array_map(
            'intval',
            array_filter(array_column($userEducations, 'field_id'))
        );

        if (! empty($requiredFieldIds)) {

            $matches = array_intersect($userFieldIds, $requiredFieldIds);

            if (empty($matches)) {
                return [
                    'qualifies' => false,
                    'reason'    => 'Field of study does not match job requirements'
                ];
            }
        }

        // -----------------------
        // All Checks Passed
        // -----------------------
        return ['qualifies' => true, 'reason' => ''];
    }


    /**
 * Count total jobs
 */
public function countTotalJobs(): int
{
    return $this->countAllResults();
}

/**
 * Count currently open jobs
 */
public function countOpenJobs(): int
{
    $today = date('Y-m-d');

    return $this->where('date_open <=', $today)
                ->where('date_close >=', $today)
                ->countAllResults();
}



}

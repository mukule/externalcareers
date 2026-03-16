<?php

//namespace App\Helpers;

use App\Models\UserModel;
use App\Models\JobModel;
use App\Models\UserEducationModel;
use App\Models\EducationLevelModel;
use App\Models\UserWorkExperienceModel;
use App\Models\UserCertificationModel;
use App\Models\UserRefereesModel;
use App\Models\UserMembershipModel;

if (!function_exists('calculate_user_work_experience')) {

    function calculate_user_work_experience(int $userId): string
    {
        $workModel = new UserWorkExperienceModel();
        $experiences = $workModel->where('user_id', $userId)
                                 ->where('active', 1)
                                 ->findAll();

        $totalInterval = new \DateInterval('P0D');
        $today = new \DateTime();

        foreach ($experiences as $exp) {
            $start = new \DateTime($exp['start_date']);
            $end = !empty($exp['end_date']) && !$exp['currently_working'] ? new \DateTime($exp['end_date']) : $today;

            if ($end < $start) continue;

            $interval = $start->diff($end);

            // accumulate total
            $totalInterval->y += $interval->y;
            $totalInterval->m += $interval->m;
            $totalInterval->d += $interval->d;
        }

        // Normalize months into years
        $years = $totalInterval->y + intdiv($totalInterval->m, 12);
        $months = $totalInterval->m % 12;
        $days = $totalInterval->d;

        $parts = [];
        if ($years > 0) $parts[] = $years . ' year' . ($years > 1 ? 's' : '');
        if ($months > 0) $parts[] = $months . ' month' . ($months > 1 ? 's' : '');
        if ($days > 0) $parts[] = $days . ' day' . ($days > 1 ? 's' : '');

        return $parts ? implode(', ', $parts) : '0 days';
    }
}

if (!function_exists('is_user_eligible_for_job')) {

   
    function is_user_eligible_for_job(int $userId, int $jobId): array
    {
        $userModel = new UserModel();
        $jobModel = new JobModel();
        $userEducationModel = new UserEducationModel();
        $educationLevelModel = new EducationLevelModel();

        $user = $userModel->find($userId);
        $job = $jobModel->find($jobId);

        if (!$user || !$job) {
            return ['eligible' => false, 'user_experience' => '0 days'];
        }

       
        if (!empty($job['min_education_level_id'])) {
            $minEducation = $educationLevelModel->find($job['min_education_level_id']);
            if (!$minEducation) return ['eligible' => false, 'user_experience' => '0 days'];

            $minIndex = (int) $minEducation['index'];

            $userEducations = $userEducationModel->where('user_id', $userId)
                                                 ->where('active', 1)
                                                 ->findAll();

            $hasRequiredEducation = false;
            foreach ($userEducations as $edu) {
                $eduLevel = $educationLevelModel->find($edu['level_id']);
                if ($eduLevel && (int) $eduLevel['index'] >= $minIndex) {
                    $hasRequiredEducation = true;
                    break;
                }
            }

            if (!$hasRequiredEducation) {
                return ['eligible' => false, 'user_experience' => '0 days'];
            }
        }

        
        $requiredYears = (float) ($job['work_experience_years'] ?? 0);
        $userExperienceStr = calculate_user_work_experience($userId);

        if ($requiredYears > 0) {
            $workModel = new UserWorkExperienceModel();
            $experiences = $workModel->where('user_id', $userId)
                                     ->where('active', 1)
                                     ->findAll();

            $totalDays = 0;
            $today = new \DateTime();

            foreach ($experiences as $exp) {
                $start = new \DateTime($exp['start_date']);
                $end = !empty($exp['end_date']) && !$exp['currently_working'] ? new \DateTime($exp['end_date']) : $today;
                if ($end < $start) continue;
                $totalDays += $start->diff($end)->days;
            }

            $userExperienceYears = $totalDays / 365.25; 
            if ($userExperienceYears < $requiredYears) {
                return ['eligible' => false, 'user_experience' => $userExperienceStr];
            }
        }

        
        if (!empty($job['certification_required']) && empty($user['certifications'])) return ['eligible' => false, 'user_experience' => $userExperienceStr];
        if (!empty($job['membership_required']) && empty($user['memberships'])) return ['eligible' => false, 'user_experience' => $userExperienceStr];
        // if (!empty($job['higher_education_required']) && empty($user['higher_education'])) return ['eligible' => false, 'user_experience' => $userExperienceStr];

        return ['eligible' => true, 'user_experience' => $userExperienceStr];
    }
}

if (!function_exists('get_eligible_jobs_for_user')) {


     
    function get_eligible_jobs_for_user(int $userId): array
    {
        $jobModel = new JobModel();
        $allJobs = $jobModel->getWithDetails();
        $eligibleJobs = [];

        foreach ($allJobs as $job) {
            $result = is_user_eligible_for_job($userId, $job['id']);
            if ($result['eligible']) {
                
                $job['user_experience'] = $result['user_experience'];
                $eligibleJobs[] = $job;
            }
        }

        return $eligibleJobs;
    }
}



if (!function_exists('get_job_application_requirements')) {

    function get_job_application_requirements(int $jobId): array
    {
        $session = session();
        $userId  = $session->get('user_id');

        $jobModel            = new JobModel();
        $certModel           = new UserCertificationModel();
        $membershipModel     = new UserMembershipModel(); // Added membership model
        $educationModel      = new UserEducationModel();
        $workExperienceModel = new UserWorkExperienceModel();
        $refereeModel        = new UserRefereesModel();

        $job = $jobModel->find($jobId);

        if (!$job) {
            return [
                'success' => false,
                'message' => 'Job not found',
                'requirements' => []
            ];
        }

        $jobType      = strtolower($job['job_type_name'] ?? '');
        $isInternship = in_array($jobType, ['internship', 'attachment']);

        $requirements = [];
        $userIdExists = !empty($userId);

        // Internship / Attachment only require authentication
        // if ($isInternship) {
        //     $requirements[] = [
        //         'name' => 'Authentication',
        //         'met'  => $userIdExists,
        //     ];

        //     return [
        //         'success' => true,
        //         'requirements' => $requirements
        //     ];
        // }

        // Regular job requirements
        // $requirements[] = [
        //     'name' => 'Authentication',
        //     'met'  => $userIdExists,
        // ];

        $requirements[] = [
            'name' => 'Education Details',
            'met'  => $userIdExists && $educationModel->where('user_id', $userId)->countAllResults() > 0,
        ];

        $requirements[] = [
            'name' => 'Latest Work Experience',
            'met'  => $userIdExists && $workExperienceModel->where('user_id', $userId)->countAllResults() > 0,
        ];

        $requirements[] = [
            'name' => 'At least 3 referees',
            'met'  => $userIdExists && $refereeModel->where('user_id', $userId)->countAllResults() >= 3,
        ];

        // Certification check (only if job requires it)
        if (!empty($job['certification_required'])) {
            $requirements[] = [
                'name' => 'Certification Required',
                'met'  => $userIdExists && $certModel->where('user_id', $userId)->countAllResults() > 0,
            ];
        }

        // Membership check (only if job requires it)
        if (!empty($job['membership_required'])) {
            $requirements[] = [
                'name' => 'Membership Required',
                'met'  => $userIdExists && $membershipModel->where('user_id', $userId)->countAllResults() > 0,
            ];
        }

        return [
            'success' => true,
            'requirements' => $requirements
        ];
    }
}

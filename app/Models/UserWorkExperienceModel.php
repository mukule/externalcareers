<?php

namespace App\Models;

use CodeIgniter\Model;

class UserWorkExperienceModel extends Model
{
    protected $table      = 'user_work_experience';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'uuid',
        'user_id',
        'company_name',
        'position',
        'start_date',
        'end_date',
        'company_address',  
        'company_phone',
        'currently_working',
        'responsibilities',
        'reference_file',
        'active'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

   
    public function getByUser(int $userId): array
    {
        return $this->where('user_id', $userId)
                    ->orderBy('start_date', 'DESC')
                    ->findAll();
    }

  
    public function getByUUID(string $uuid)
    {
        return $this->where('uuid', $uuid)->first();
    }


    public function getTotalExperienceYears(int $userId): float
{
    $experiences = $this->where('user_id', $userId)
                        ->where('active', 1)
                        ->findAll();

    if (empty($experiences)) {
        return 0;
    }

    $today = new \DateTime();
    $ranges = [];

    // 1️⃣ Build date ranges
    foreach ($experiences as $exp) {

        if (empty($exp['start_date'])) {
            continue;
        }

        $start = new \DateTime($exp['start_date']);

        if (!empty($exp['currently_working']) && $exp['currently_working']) {
            $end = clone $today;
        } elseif (!empty($exp['end_date'])) {
            $end = new \DateTime($exp['end_date']);
        } else {
            $end = clone $today;
        }

        if ($end < $start) {
            continue;
        }

        $ranges[] = [
            'start' => $start,
            'end'   => $end
        ];
    }

    if (empty($ranges)) {
        return 0;
    }

    
    usort($ranges, function ($a, $b) {
        return $a['start'] <=> $b['start'];
    });

    
    $merged = [];
    $current = $ranges[0];

    foreach ($ranges as $range) {

        if ($range['start'] <= $current['end']) {
            
            if ($range['end'] > $current['end']) {
                $current['end'] = $range['end'];
            }
        } else {
            $merged[] = $current;
            $current = $range;
        }
    }

    $merged[] = $current;

    // 4️⃣ Calculate total unique days
    $totalDays = 0;

    foreach ($merged as $range) {
        $interval = $range['start']->diff($range['end']);
        $totalDays += $interval->days;
    }

    // 5️⃣ Convert to years
    return round($totalDays / 365, 2);
}

}




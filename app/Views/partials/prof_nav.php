<?php
$currentStep = $currentStep ?? 1;

$stepUrls = [
    1 => base_url('applicant/profile'),
    2 => base_url('applicant/professional-statement'),
    3 => base_url('applicant/basic-education'),
    4 => base_url('applicant/higher-education'),
    5 => base_url('applicant/membership'),
    6 => base_url('applicant/certification'),
    7 => base_url('applicant/work-experience'),
    8 => base_url('applicant/referees'),
    9 => base_url('applicant/profile-review')
];

$steps = [
    1 => 'Basic Details',
    2 => 'Professional Statement',
    3 => 'Basic Education',
    4 => 'College/University',
    5 => 'Memberships',
    6 => 'Certifications',
    7 => 'Work Experience',
    8 => 'Referees',
    9 => 'Resume Review'
];
?>

<!-- Step Wizard Card -->
<div class="card mb-4 mt-4 shadow-sm border-0">
    <div class="card-body">
        <div class="d-flex flex-wrap justify-content-between align-items-center">
            <?php foreach($steps as $stepNum => $label):
                $isActive = ($currentStep == $stepNum);
                $isPast   = ($currentStep > $stepNum);
            ?>
            <div class="text-center flex-fill mx-1">
                <a href="<?= $stepUrls[$stepNum] ?>" class="text-decoration-none">
                    <div class="rounded-circle mx-auto d-flex align-items-center justify-content-center 
                        <?= $isActive ? 'bg-primary text-white' : ($isPast ? 'bg-success text-white' : 'bg-light text-muted') ?>" 
                        style="width:30px; height:30px; font-weight:bold;">
                        <?= $stepNum ?>
                    </div>
                    <div class="mt-1" style="font-size: 0.75rem;">
                        <?= esc($label) ?>
                    </div>
                </a>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<style>
/* Optional: hover effect for future/past steps */
.card-body a:hover .rounded-circle {
    opacity: 0.85;
}
</style>

<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="container py-5 mt-4">

    <!-- ================= FULL-WIDTH BREADCRUMB ROW ================= -->
    <div class="row mb-4">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb bg-white rounded shadow-sm p-4 mb-0">
                    <li class="breadcrumb-item">
                        <a href="<?= base_url('/') ?>">Home</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="<?= base_url('job_type/' . ($job['job_type_uuid'] ?? '')) ?>">
                            <?= esc($job['job_type_name'] ?? 'Jobs') ?>
                        </a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">
                        <?= esc($job['name']) ?> - <?= esc($job['reference_no']) ?>
                    </li>
                </ol>
            </nav>
        </div>
    </div>

    <!-- ================= MAIN CONTENT ROW ================= -->
    <div class="row g-4">

        <!-- ================= LEFT COLUMN ================= -->
        <div class="col-lg-8">
            <div class="card shadow-sm border-0">
                <div class="card-body p-4">

                    <h2 class="mb-2"><?= esc($job['name']) ?></h2>

                    <?php if (!empty($job['job_type_name'])): ?>
                        <span class="badge bg-light text-dark border mb-3">
                            <?= esc($job['job_type_name']) ?>
                        </span>
                    <?php endif; ?>

                    <div class="mb-3 small text-muted">
                        <div><strong>Reference:</strong> <?= esc($job['reference_no']) ?></div>
                        <div>
                            <strong>Open:</strong> <?= esc($job['date_open']) ?>
                            |
                            <strong>Close:</strong> <?= esc($job['date_close']) ?>
                        </div>
                        <div><strong>Status:</strong> <?= esc($job['status']) ?></div>
                        <?php if(!empty($job['discipline_name'])): ?>
                            <div><strong>Discipline:</strong> <?= esc($job['discipline_name']) ?></div>
                        <?php endif; ?>
                        <?php if(!empty($job['specialities'])): ?>
                            <div><strong>Field(s) of Study:</strong> <?= implode(', ', array_column($job['specialities'], 'name')) ?></div>
                        <?php endif; ?>
                    </div>

                    <hr>

                    <h5 class="fw-semibold">Job Summary</h5>
                    <div class="mb-4"><?= $job['job_summary'] ?></div>

                    <h5 class="fw-semibold">Job Description</h5>
                    <div class="mb-4"><?= $job['job_description'] ?></div>

                    <h5 class="fw-semibold">Requirements</h5>
                    <ul class="mb-4">
                        <li><strong>Minimum Education Level:</strong> <?= esc($job['education_name']) ?></li>
                        <li><strong>Minimum Work Experience:</strong> <?= esc($job['work_experience_years']) ?> years</li>

                        <?php if (!empty($job['certification_required'])): ?>
                            <li>Certification Required</li>
                        <?php endif; ?>

                        <?php if (!empty($job['membership_required'])): ?>
                            <li>Professional Membership Required</li>
                        <?php endif; ?>

                        <?php if (!empty($job['higher_education_required'])): ?>
                            <li>Higher Education Required</li>
                        <?php endif; ?>
                    </ul>

                    <?php
                        $isLoggedIn = !empty($user);
                        $allMet = true;
                        foreach ($requirements as $req) {
                            if (empty($req['met'])) {
                                $allMet = false;
                                break;
                            }
                        }
                    ?>

                    <!-- ================= APPLY / LOGIN BUTTON ================= -->
                    <?php if (!$isLoggedIn): ?>
                        <a href="#" class="btn btn-outline-primary">
                            Login to Apply
                        </a>
                    <?php else: ?>
                        <button 
                            type="button"
                            class="btn btn-primary"
                            data-bs-toggle="modal"
                            data-bs-target="#confirmApplyModal"
                            <?= !$allMet ? 'disabled' : '' ?>
                        >
                            Apply Now
                        </button>

                        <?php if (!$allMet): ?>
                            <p class="text-danger small mt-2">
                                Please complete all required profile sections before applying.
                            </p>
                        <?php endif; ?>
                    <?php endif; ?>

                </div>
            </div>
        </div>

        <!-- ================= RIGHT COLUMN ================= -->
        <div class="col-lg-4">
            <div class="card shadow-sm border-0 p-3">
                <div class="card-header bg-white fw-semibold">
                    Application Requirements
                </div>

                <ul class="list-group list-group-flush">
                    <?php if (!empty($requirements)): ?>
                        <?php foreach ($requirements as $req): ?>
                            <?php 
                                $isMet = !empty($req['met']);
                                $textClass = $isMet ? 'text-success' : 'text-danger';
                                $icon = $isMet 
                                    ? '<i class="bi bi-check-circle-fill me-2"></i>' 
                                    : '<i class="bi bi-x-circle-fill me-2"></i>';
                            ?>
                            <li class="list-group-item <?= $textClass ?>">
                                <?= $icon ?> <?= esc($req['name']) ?>
                            </li>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <li class="list-group-item text-muted">
                            No requirements found.
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>

    </div>
</div>

<!-- ================= MODAL ================= -->
<?php if (!empty($user)): ?>
<div class="modal fade" id="confirmApplyModal" tabindex="-1" aria-labelledby="confirmApplyModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title" id="confirmApplyModalLabel">Confirm Application</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body text-center">
                <p>Are you sure you want to submit your application for:</p>
                <h6 class="fw-bold"><?= esc($job['name']) ?></h6>
                <p class="text-warning small mt-3">
                    Please ensure your CV and profile are up to date before submitting.
                </p>
            </div>

            <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    Cancel
                </button>

                <a href="<?= base_url('applicant/applications/apply/' . $job['uuid']) ?>" 
                   class="btn btn-primary">
                    Confirm Submit
                </a>
            </div>

        </div>
    </div>
</div>
<?php endif; ?>

<?= $this->endSection() ?>
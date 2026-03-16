<?= $this->extend('layouts/dashboard') ?>
<?= $this->section('content') ?>

<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mt-4 mb-3">
        <h4 class="mb-0"><?= esc($title ?? 'Job Details') ?></h4>
        <a href="<?= base_url('admin/jobs') ?>" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-1"></i> Back
        </a>
    </div>
    <hr>

    <div class="card mb-4">
        <div class="card-body p-4">

            <h4 class="mb-3"><?= esc($job['name']) ?> - <small class="text-muted"><?= esc($job['reference_no']) ?></small></h4>

            <div class="row mb-3">
                <div class="col-md-6">
                    <p><strong>Job Type:</strong> <?= esc($job['job_type_name'] ?? 'N/A') ?></p>
                    <p><strong>Minimum Education:</strong> <?= esc($job['education_name'] ?? 'N/A') ?></p>
                    <p><strong>Posts Needed:</strong> <?= esc($job['posts_needed']) ?></p>
                    <p><strong>Reports To:</strong> <?= esc($job['reports_to'] ?? '-') ?></p>
                </div>
                <div class="col-md-6">
                    <p><strong>Open Date:</strong> <?= esc($job['date_open']) ?></p>
                    <p><strong>Close Date:</strong> <?= esc($job['date_close']) ?></p>
                    <p><strong>Experience Required:</strong> <?= esc($job['work_experience_years']) ?> years</p>
                    <?php 
                        $status = $job['status'] ?? 'Unknown';
                        $badgeClass = match($status) {
                            'Upcoming' => 'bg-info',
                            'Open'     => 'bg-primary',
                            'Closed'   => 'bg-secondary',
                            default    => 'bg-secondary'
                        };
                    ?>
                    <span class="badge p-2 <?= $badgeClass ?>"><?= esc($status) ?></span>
                </div>
            </div>

            <hr>

            <h5>Job Summary</h5>
            <p><?= ($job['job_summary']) ?></p>

            <h5>Job Description</h5>
            <p><?= ($job['job_description']) ?></p>

            <h5>Additional Requirements</h5>
            <?php if ($job['certification_required'] || $job['membership_required'] || $job['higher_education_required']): ?>
                <ul>
                    <?php if ($job['certification_required']): ?><li>Certification Required</li><?php endif; ?>
                    <?php if ($job['membership_required']): ?><li>Membership Required</li><?php endif; ?>
                    <?php if ($job['higher_education_required']): ?><li>Higher Education Required</li><?php endif; ?>
                </ul>
            <?php else: ?>
                <p>No additional requirements</p>
            <?php endif; ?>

            <h5>Fields of Study / Disciplines</h5>
            <?php if (!empty($job['fields_of_study'])): ?>
                <ul>
                    <?php foreach ($job['fields_of_study'] as $field): ?>
                        <li><?= esc($field['name'] ?? 'N/A') ?></li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p>No specific disciplines required</p>
            <?php endif; ?>

            <hr>

            <div class="d-flex gap-2">
                <!-- Toggle Active / Inactive -->
                <?php if ($job['active'] == 1): ?>
                    <a href="<?= base_url('admin/jobs/toggle/' . $job['uuid']) ?>"
                       class="btn btn-outline-primary"
                       data-bs-toggle="tooltip" title="Unpublish Job"
                       onclick="return confirm('Unpublish this job?');">
                        <i class="fas fa-eye-slash"></i> Unpublish
                    </a>
                <?php else: ?>
                    <a href="<?= base_url('admin/jobs/toggle/' . $job['uuid']) ?>"
                       class="btn btn-outline-secondary"
                       data-bs-toggle="tooltip" title="Publish Job"
                       onclick="return confirm('Publish this job?');">
                        <i class="fas fa-eye"></i> Publish
                    </a>
                <?php endif; ?>

                <!-- Edit -->
                <a href="<?= base_url('admin/jobs/edit/' . $job['uuid']) ?>" 
                   class="btn btn-outline-primary"
                   data-bs-toggle="tooltip" title="Edit Job">
                    <i class="fas fa-edit"></i> Edit
                </a>
            </div>

        </div>
    </div>
</div>

<?= $this->endSection() ?>

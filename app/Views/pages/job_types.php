<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<style>
.hero-banner {
    position: relative;
    height: 320px;
    overflow: hidden;
}
.hero-banner img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}
.hero-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0,0,0,0.55);
}
.hero-text {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
}

.breadcrumb-custom a {
    color: #fff;
    text-decoration: none;
}
.breadcrumb-custom a:hover {
    text-decoration: underline;
}

.job-card:hover {
    transform: translateY(-4px);
    transition: 0.3s ease;
}
</style>

<div class="hero-banner mb-4">
    <?php if (!empty($jobType['banner'])): ?>
        <img src="<?= base_url('uploads/job_types/' . $jobType['banner']) ?>">
    <?php else: ?>
        <img src="<?= base_url('assets/img/default-banner.jpg') ?>">
    <?php endif; ?>

    <div class="hero-overlay"></div>

    <div class="hero-text text-center text-white">

       

        <h2 class="fw-bold"><?= esc($jobType['display_name']) ?></h2>
        <p><?= esc($jobType['description']) ?></p>

        
        <div class="breadcrumb-custom mb-2 small">
            <a href="<?= base_url('/') ?>">Home</a> /
            <span><?= esc($jobType['display_name']) ?></span>
        </div>


    </div>
</div>

<!-- ================= FILTER SECTION ================= -->
<div class="container mb-4">
    <form method="get" class="card p-3 shadow-sm border-0">
        <div class="row g-2 align-items-end">

            <!-- Job Name -->
            <div class="col-md-3">
                <label class="form-label">Job Name</label>
                <input type="text" name="name" class="form-control"
                       value="<?= esc($filters['name'] ?? '') ?>">
            </div>

            <!-- Discipline -->
            <div class="col-md-3">
                <label class="form-label">Discipline</label>
                <select name="discipline_id" class="form-select">
                    <option value="">All</option>
                    <?php foreach ($disciplines as $d): ?>
                        <option value="<?= $d['id'] ?>"
                            <?= (!empty($filters['discipline_id']) && $filters['discipline_id'] == $d['id']) ? 'selected' : '' ?>>
                            <?= esc($d['display_name'] ?? $d['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Field of Study -->
            <div class="col-md-3">
                <label class="form-label">Speciality</label>
                <select name="field_id" class="form-select">
                    <option value="">All</option>
                    <?php foreach ($fieldsOfStudy as $f): ?>
                        <option value="<?= $f['id'] ?>"
                            <?= (!empty($filters['field_id']) && $filters['field_id'] == $f['id']) ? 'selected' : '' ?>>
                            <?= esc($f['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Buttons -->
            <div class="col-md-3 d-flex gap-2">
                <button class="btn btn-primary w-100">
                    Filter
                </button>

                <?php if (!empty(array_filter($filters))): ?>
                    <a href="<?= current_url() ?>" class="btn btn-outline-secondary w-100">
                        Reset
                    </a>
                <?php endif; ?>
            </div>

        </div>
    </form>
</div>

<!-- ================= JOB LIST ================= -->
<div class="container pb-5">
    <div class="row g-4">

        <?php if (!empty($jobs)): ?>
            <?php foreach ($jobs as $job): ?>
                <div class="col-12">
                    <div class="card job-card shadow-sm border-0">

                        <div class="card-body d-flex flex-column flex-md-row justify-content-between">

                            <!-- LEFT CONTENT -->
                            <div>
                                <h5 class="fw-bold mb-1"><?= esc($job['name']) ?> - <?= esc($job['reference_no']) ?></h5>

                               

                              <p class="mb-2 text-muted">
                                <strong>Minimum Education:</strong> <?= esc($job['minimum_education'] ?? 'N/A') ?>
                            </p>

                               <p class="small text-muted mb-0">
                                    Deadline: <?= date('Y-m-d H:i', strtotime($job['date_close'])) ?>
                                </p>
                            </div>

                            <!-- RIGHT ACTION -->
                            <div class="d-flex align-items-center mt-3 mt-md-0">
                                <a href="<?= base_url('jobs/' . $job['uuid']) ?>"
                                   class="btn btn-primary">
                                    View Details
                                </a>
                            </div>

                        </div>

                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12 text-center">
                <p class="text-muted">No jobs found matching your criteria.</p>
            </div>
        <?php endif; ?>

    </div>
</div>

<?= $this->endSection() ?>
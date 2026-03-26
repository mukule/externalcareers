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
    background: rgba(0,0,0,0.3);
}
.hero-text {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 100%;
    text-align: center;
}
.hero-text-box {
    max-width: 700px;
    background-color: rgba(73, 13, 165, 0.85);
    color: #fff;
    padding: 20px 30px;
    border-radius: 0.75rem;
    display: inline-block;
    text-align: center;
}

/* Breadcrumb sits outside hero-text, anchored to banner bottom */
.hero-breadcrumb-bar {
    position: absolute;
    bottom: 16px;
    left: 0;
    right: 0;
}
.hero-breadcrumb {
    display: inline-flex;
    align-items: center;
    gap: 0;
    font-size: 0.85rem;
    background-color: rgba(73, 13, 165, 0.85);
    border-radius: 50px;
    padding: 6px 16px;
    backdrop-filter: blur(4px);
    -webkit-backdrop-filter: blur(4px);
}
.hero-breadcrumb a {
    color: rgba(255,255,255,0.8);
    text-decoration: none;
    transition: color 0.2s;
}
.hero-breadcrumb a:hover {
    color: #fff;
    text-decoration: underline;
}
.hero-breadcrumb .separator {
    color: rgba(255,255,255,0.4);
    margin: 0 6px;
}
.hero-breadcrumb .current {
    color: #fe862d;
    font-weight: 600;
}

.job-card:hover {
    transform: translateY(-4px);
    transition: 0.3s ease;
}
</style>

<!-- ================= HERO ================= -->
<div class="hero-banner mb-4">
    <?php if (!empty($jobType['banner'])): ?>
        <img src="<?= base_url('uploads/job_types/' . $jobType['banner']) ?>">
    <?php else: ?>
        <img src="<?= base_url('assets/img/default-banner.jpg') ?>">
    <?php endif; ?>

    <div class="hero-overlay"></div>

    
    <div class="hero-text">
        <div class="hero-text-box">
            <h2 class="fw-bold"><?= esc($jobType['display_name']) ?></h2>
            <p class="mb-0"><?= esc($jobType['description']) ?></p>
        </div>
    </div>

   
    <div class="hero-breadcrumb-bar">
        <div class="container">
            <nav class="hero-breadcrumb">
                <a href="<?= base_url('/') ?>">Home</a>
                <span class="separator">›</span>
                <span class="current"><?= esc($jobType['display_name']) ?></span>
            </nav>
        </div>
    </div>
</div>


<div class="container mb-4">
    <form method="get" class="card p-3 shadow-sm border-0">
        <div class="row g-2 align-items-end">

            <!-- Job Name -->
            <div class="col-md-3">
                <label class="form-label">Job Name</label>
                <input type="text" name="name" class="form-control"
                       value="<?= esc($filters['name'] ?? '') ?>">
            </div>

           
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

                           
                            <div>
                                <h5 class="fw-bold mb-1"><?= esc($job['name']) ?> - <?= esc($job['reference_no']) ?></h5>

                                <p class="mb-2 text-muted">
                                    <strong>Minimum Education:</strong> <?= esc($job['minimum_education'] ?? 'N/A') ?>
                                </p>

                                <p class="small text-muted mb-0">
                                    Deadline: <?= date('Y-m-d H:i', strtotime($job['date_close'])) ?>
                                </p>
                            </div>

                           
                            <div class="d-flex align-items-center mt-3 mt-md-0">
                                <a href="<?= base_url('jobs/' . $job['uuid']) ?>" class="btn btn-primary">
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
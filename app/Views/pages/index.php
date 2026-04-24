<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<style>
@media (max-width: 768px) {
    #heroCarousel .overlay-text h2 {
        font-size: 1.5rem;
    }
    #heroCarousel .overlay-text p.lead {
        font-size: 0.9rem;
    }
}

#heroCarousel .carousel-item img {
    width: 100%;
    height: 500px;
    object-fit: cover;
}

@media (max-width: 768px) {
    #heroCarousel .carousel-item img {
        height: 250px; 
    }
}
</style>

<!-- ================= HERO ================= -->
<div id="heroCarousel" class="carousel slide carousel-fade" data-bs-ride="carousel">
    <div class="carousel-inner">

        <?php if (!empty($jobTypes)): ?>
            <?php foreach ($jobTypes as $index => $type): ?>
                <div class="carousel-item <?= $index === 0 ? 'active' : '' ?>">

                    <img src="<?= !empty($type['banner']) 
                        ? base_url('uploads/job_types/' . $type['banner']) 
                        : base_url('assets/img/default-banner.jpg') ?>"
                        class="d-block w-100"
                        alt="<?= esc($type['name']) ?>">

                    <div class="carousel-overlay d-flex justify-content-center align-items-center">
                        <div class="overlay-text text-center text-white p-4 rounded-3 bg-primary bg-opacity-75">
                            <h2 class="fw-bold mb-3"><?= esc($type['display_name']) ?></h2>
                            <p class="lead mb-3"><?= esc($type['description']) ?></p>
                        </div>
                    </div>

                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="carousel-item active">
                <img src="<?= base_url('assets/img/default-banner.jpg') ?>" class="d-block w-100">
                <div class="carousel-overlay d-flex justify-content-center align-items-center">
                    <div class="overlay-text text-center text-white">
                        <h2 class="fw-bold mb-3">Welcome to <?= esc($app_name ?? 'Our Portal') ?></h2>
                        <p class="lead mb-3">Explore job opportunities and apply easily.</p>
                    </div>
                </div>
            </div>
        <?php endif; ?>

    </div>

    <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
        <span class="carousel-control-prev-icon"></span>
    </button>

    <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
        <span class="carousel-control-next-icon"></span>
    </button>
</div>


<!-- ================= FILTERS ================= -->
<div class="container my-5">
    <form method="get" class="card p-3 shadow-sm border-0">

        <div class="d-flex flex-wrap flex-md-nowrap gap-2 align-items-end">

            <!-- Job Name -->
            <div style="min-width: 200px; flex: 1;">
                <label class="form-label small">Job Name</label>
                <input type="text" name="name" class="form-control form-control-sm"
                       value="<?= esc($filters['name'] ?? '') ?>">
            </div>

            <!-- Job Type -->
            <?php if (!empty($jobTypes) && count($jobTypes) > 1): ?>
                <div style="min-width: 180px; flex: 1;">
                    <label class="form-label small">Job Type</label>
                    <select name="job_type_id" class="form-select form-select-sm">
                        <option value="">All</option>
                        <?php foreach ($jobTypes as $jt): ?>
                            <option value="<?= $jt['id'] ?>"
                                <?= (!empty($filters['job_type_id']) && $filters['job_type_id'] == $jt['id']) ? 'selected' : '' ?>>
                                <?= esc($jt['display_name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            <?php endif; ?>

            <!-- Discipline -->
            <div style="min-width: 180px; flex: 1;">
                <label class="form-label small">Discipline</label>
                <select name="discipline_id" class="form-select form-select-sm">
                    <option value="">All</option>
                    <?php foreach ($disciplines as $d): ?>
                        <option value="<?= $d['id'] ?>"
                            <?= (!empty($filters['discipline_id']) && $filters['discipline_id'] == $d['id']) ? 'selected' : '' ?>>
                            <?= esc($d['display_name'] ?? $d['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Buttons -->
            <div class="d-flex gap-2" style="min-width: 220px;">
                <button class="btn btn-primary btn-sm w-100">
                    Filter
                </button>

                <?php if (!empty($_GET)): ?>
                    <a href="<?= current_url() ?>" class="btn btn-outline-secondary btn-sm w-100">
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
                    <div class="card shadow-sm border-0">

                        <div class="card-body d-flex flex-column flex-md-row justify-content-between">

                            <div>
                                <h5 class="fw-bold mb-1">
                                    <?= esc($job['name']) ?> - <?= esc($job['reference_no']) ?>
                                </h5>

                                <p class="mb-2 text-muted">
                                    <strong>Minimum Education:</strong>
                                    <?= esc($job['minimum_education'] ?? 'N/A') ?>
                                </p>

                                <p class="small text-muted mb-0">
                                    Deadline:
                                    <?= date('M d, Y H:i', strtotime($job['date_close'])) ?>
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
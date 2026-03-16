<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<!-- HERO SLIDER -->
<div id="heroCarousel" class="carousel slide carousel-fade" data-bs-ride="carousel">
    <div class="carousel-inner">
        <!-- Slides (same as before) -->
        <div class="carousel-item active">
            <img src="<?= base_url('assets/img/banner3.jpg') ?>" class="d-block w-100">
            <div class="carousel-overlay d-flex align-items-center">
                <div class="overlay-text">
                    <h2 class="text-white fw-bold mb-3">
                        Welcome to <?= esc($app_name ?? 'Our Portal') ?>
                    </h2>
                    <p class="text-white lead mb-3">
                        Find job opportunities and apply easily.
                    </p>
                    <a href="#jobs" class="btn btn-primary">
                        Apply Today <i class="fas fa-arrow-right ms-2"></i>
                    </a>
                </div>
            </div>
        </div>

        <div class="carousel-item">
            <img src="<?= base_url('assets/img/banner1.jpg') ?>" class="d-block w-100">
            <div class="carousel-overlay d-flex align-items-center">
                <div class="overlay-text">
                    <h2 class="text-white fw-bold mb-3">Grow Your Career</h2>
                    <p class="text-white lead mb-3">
                        Access professional opportunities.
                    </p>
                    <a href="#jobs" class="btn btn-primary">
                        Apply Today <i class="fas fa-arrow-right ms-2"></i>
                    </a>
                </div>
            </div>
        </div>

        <div class="carousel-item">
            <img src="<?= base_url('assets/img/banner2.jpg') ?>" class="d-block w-100">
            <div class="carousel-overlay d-flex align-items-center">
                <div class="overlay-text">
                    <h2 class="text-white fw-bold mb-3">Trusted Recruitment</h2>
                    <p class="text-white lead mb-3">
                        A seamless, transparent application process.
                    </p>
                    <a href="#jobs" class="btn btn-primary">
                        Apply Today <i class="fas fa-arrow-right ms-2"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>


<section class="bg-primary text-white py-4">
    <div class="container">
        <div class="row text-center g-3">

            <!-- Column 1 -->
            <div class="col-12 col-sm-6 col-md-4 d-flex flex-column flex-sm-row align-items-center justify-content-center py-3">
                <div class="bg-white text-primary rounded-circle d-flex align-items-center justify-content-center mb-2 mb-sm-0 me-sm-2" style="width:50px; height:50px;">
                    <i class="fas fa-user-plus fa-lg"></i>
                </div>
                <h6 class="mb-0">Create an Account</h6>
            </div>

            <!-- Column 2 -->
            <div class="col-12 col-sm-6 col-md-4 d-flex flex-column flex-sm-row align-items-center justify-content-center py-3">
                <div class="bg-white text-primary rounded-circle d-flex align-items-center justify-content-center mb-2 mb-sm-0 me-sm-2" style="width:50px; height:50px;">
                    <i class="fas fa-search fa-lg"></i>
                </div>
                <h6 class="mb-0">Explore Opportunities</h6>
            </div>

            <!-- Column 3 -->
            <div class="col-12 col-sm-6 col-md-4 d-flex flex-column flex-sm-row align-items-center justify-content-center py-3">
                <div class="bg-white text-primary rounded-circle d-flex align-items-center justify-content-center mb-2 mb-sm-0 me-sm-2" style="width:50px; height:50px;">
                    <i class="fas fa-file-alt fa-lg"></i>
                </div>
                <h6 class="mb-0">Apply</h6>
            </div>

        </div>
    </div>
</section>


<section class="py-5 bg-light" id="jobs">
    <div class="container">
        <h2 class="mb-4 text-center">Job Categories</h2>

        <div class="row g-3 justify-content-center">
            <?php if (!empty($jobTypes)): ?>
                <?php foreach ($jobTypes as $type): 
                    // Count open jobs for this type
                    $count = 0;
                    foreach ($jobs as $job) {
                        if ($job['job_type_id'] == $type['id']) $count++;
                    }
                ?>
                    <div class="col-md-4 col-sm-12">
                        <a href="<?= base_url('home?type=' . $type['id']) ?>" class="text-decoration-none">
                            <div class="d-flex align-items-center p-3 bg-white rounded shadow-sm border">
                                <div class="bg-primary text-white rounded d-flex align-items-center justify-content-center me-3" style="width:50px; height:50px;">
                                    <i class="fas fa-briefcase"></i>
                                </div>
                                <div>
                                    <h6 class="mb-1"><?= esc($type['display_name']) ?></h6>
                                    <small class="text-muted"><?= $count ?> Open <?= $count === 1 ? 'Job' : 'Jobs' ?></small>
                                </div>
                            </div>
                        </a>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="text-center text-muted">No job categories found.</p>
            <?php endif; ?>
        </div>
    </div>
</section>


<section class="py-5">
    <div class="container">
        <h2 class="mb-4 text-center">Available Job Opportunities</h2>

        <?php if (!empty($jobs)): ?>
            <div class="row g-4">
                <?php foreach ($jobs as $job): ?>
                    <div class="col-12">
                        <div class="card shadow-sm h-100 border-0">
                            <div class="card-body d-flex justify-content-between align-items-center">
                                <div>
                                    <h5 class="card-title mb-1">
                                        <a href="<?= base_url('jobs/' . $job['uuid']) ?>" class="text-decoration-none ">
                                            <?= esc($job['name']) ?>
                                        </a>
                                        <?php if (!empty($job['job_type_name'])): ?>
                                            <span class="badge bg-secondary bg-opacity-25 text-dark ms-2">
                                                <?= esc($job['job_type_name']) ?>
                                            </span>
                                        <?php endif; ?>
                                    </h5>
                                    <p class="card-subtitle text-muted mb-1">
                                        Ref: <?= esc($job['reference_no']) ?>
                                    </p>
                                    <small class="text-muted">
                                        Open: <?= esc($job['date_open']) ?> | Close: <?= esc($job['date_close']) ?>
                                    </small>
                                </div>
                                <div>
                                    <a href="<?= base_url('jobs/' . $job['uuid']) ?>" 
                                       class="btn btn-outline-primary">
                                        View / Apply
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p class="text-center text-muted mt-4">No available job opportunities at the moment.</p>
        <?php endif; ?>
    </div>
</section>


<?= $this->endSection() ?>

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

    .card-body h5.card-title {
        font-size: 1rem;
    }
    .card-body p.card-text {
        font-size: 0.85rem;
    }
}


.card-img-square {
    position: relative;
    width: 100%;
    padding-top: 100%; 
    overflow: hidden;
    border-top-left-radius: 1rem;
    border-top-right-radius: 1rem;
}

.card-img-square img {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    object-fit: cover;
    border-top-left-radius: 1rem;
    border-top-right-radius: 1rem;
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

<div id="heroCarousel" class="carousel slide carousel-fade" data-bs-ride="carousel">
    <div class="carousel-inner">
        <?php if (!empty($jobTypes)): ?>
            <?php foreach ($jobTypes as $index => $type): ?>
                <div class="carousel-item <?= $index === 0 ? 'active' : '' ?>">
                    <?php if (!empty($type['banner'])): ?>
                        <img src="<?= base_url('uploads/job_types/' . $type['banner']) ?>" 
                             class="d-block w-100" 
                             alt="<?= esc($type['name']) ?>">
                    <?php else: ?>
                        <img src="<?= base_url('assets/img/default-banner.jpg') ?>" 
                             class="d-block w-100" 
                             alt="Banner">
                    <?php endif; ?>
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
                <img src="<?= base_url('assets/img/default-banner.jpg') ?>" 
                     class="d-block w-100" 
                     alt="Default Banner">
                <div class="carousel-overlay d-flex justify-content-center align-items-center">
                    <div class="overlay-text text-center text-white">
                        <h2 class="fw-bold mb-3">Welcome to <?= esc($app_name ?? 'Our Portal') ?></h2>
                        <p class="lead mb-3">Explore job categories and apply easily.</p>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
    <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
        <span class="carousel-control-prev-icon"></span>
        <span class="visually-hidden">Previous</span>
    </button>
    <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
        <span class="carousel-control-next-icon"></span>
        <span class="visually-hidden">Next</span>
    </button>
</div>

<section class="py-5">
    <div class="container">
        <div class="row g-4 justify-content-center">

            <?php if (!empty($jobTypes)): ?>
                <?php foreach (array_slice($jobTypes, 0, 3) as $type): ?>
                    <div class="col-12 col-md-4 col-lg-4">
                        <div class="card h-100 shadow-sm rounded-4 overflow-hidden">
                            <div class="card-img-square mb-3">
                                <?php if (!empty($type['icon'])): ?>
                                    <img src="<?= base_url('uploads/job_types/' . $type['icon']) ?>" 
                                         alt="<?= esc($type['name']) ?>">
                                <?php else: ?>
                                    <div class="bg-secondary w-100 h-100"></div>
                                <?php endif; ?>
                            </div>
                            <div class="card-body">
                                <h5 class="card-title mb-2"><?= esc($type['display_name']) ?></h5>
                                <p class="card-text text-muted mb-3"><?= esc($type['description']) ?></p>
                                <a href="<?= base_url('job_type/' . $type['uuid']) ?>" class="btn btn-primary">
                                    Explore <i class="fas fa-arrow-right ms-1"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="text-center text-muted">No job categories found.</p>
            <?php endif; ?>

        </div>
    </div>
</section>

<?= $this->endSection() ?>
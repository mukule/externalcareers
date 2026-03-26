<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<style>
.hero-banner {
    position: relative;
    height: 220px;
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
.table-hover tbody tr:hover {
    background-color: #f8f9fa;
}
.status-badge {
    font-size: 0.85rem;
    padding: 5px 10px;
}
</style>

<!-- ================= HERO ================= -->
<div class="hero-banner mb-4">
    <img src="<?= base_url('assets/img/default-banner.png') ?>">
    <div class="hero-overlay"></div>
    <div class="hero-text text-center text-white">
        <h2 class="fw-bold">My Applications</h2>
        <p class="small">Track all jobs you have applied for</p>
    </div>
</div>

<!-- ================= APPLICATIONS TABLE ================= -->
<div class="container pb-5">
    <div class="card shadow-sm border-0">
        <div class="card-body">

            <?php if (!empty($applications)): ?>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle">

                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Job Name</th>
                                <th>Job Ref</th>
                                <th>Application Ref</th>
                                <th>Status</th>
                                <th>Date Applied</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php foreach ($applications as $index => $app): ?>
                                <tr>
                                    <td><?= $index + 1 ?></td>

                                    <td class="fw-semibold"><?= esc($app['job_name']) ?></td>
                                    <td><?= esc($app['job_ref']) ?></td>
                                    <td><?= esc($app['application_ref']) ?></td>

                                    <td>
                                        <?php
                                            $status = strtolower($app['status']);

                                            if ($status === 'pending') {
                                                $displayStatus = 'Processing';
                                                $badgeClass = 'warning';
                                            } else {
                                                $displayStatus = '-';
                                                $badgeClass = 'secondary';
                                            }
                                        ?>
                                        <span class="badge bg-<?= $badgeClass ?> status-badge">
                                            <?= $displayStatus ?>
                                        </span>
                                    </td>

                                    <td>
                                        <?= !empty($app['created_at']) 
                                            ? date('Y-m-d H:i', strtotime($app['created_at'])) 
                                            : '-' ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>

                    </table>
                </div>

            <?php else: ?>
                <div class="text-center py-5">
                    <p class="text-muted mb-2">You have not applied to any jobs yet.</p>
                    <a href="<?= base_url('/') ?>" class="btn btn-primary">
                        Browse Jobs
                    </a>
                </div>
            <?php endif; ?>

        </div>
    </div>
</div>

<?= $this->endSection() ?>
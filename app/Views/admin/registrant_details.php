<?= $this->extend('layouts/dashboard') ?>
<?= $this->section('content') ?>

<div class="container-fluid px-4 py-3">

    <!-- Breadcrumb -->
    <div class="row mb-3">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb bg-white rounded shadow-sm p-3 mb-0">
                    <li class="breadcrumb-item">
                        <a href="<?= base_url('admin') ?>">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item active">
                        <?= esc(trim($user['first_name'] . ' ' . $user['last_name'])) ?> Details
                    </li>
                </ol>
            </nav>
        </div>
    </div>

    <!-- User Details -->
    <div class="card shadow-sm border-0">
        <div class="card-body p-3">

            <div class="row g-4 small">

                <!-- Basic Info -->
                <div class="col-md-6">
                    <span class="text-muted">Full Name:</span>
                    <span class="fw-semibold"><?= esc(trim($user['first_name'] . ' ' . $user['last_name'])) ?></span>
                </div>

                <div class="col-md-6">
                    <span class="text-muted">Email:</span>
                    <span><?= esc($user['email']) ?></span>
                </div>

                <div class="col-md-6">
                    <span class="text-muted">Phone:</span>
                    <span><?= esc($user['phone'] ?? '-') ?></span>
                </div>

                <div class="col-md-6">
                    <span class="text-muted">Gender:</span>
                    <span><?= esc($user['gender'] ?? '-') ?></span>
                </div>

                <div class="col-md-6">
                    <span class="text-muted">DOB:</span>
                    <span><?= !empty($user['dob']) ? date('d M Y', strtotime($user['dob'])) : '-' ?></span>
                </div>

                <div class="col-md-6">
                    <span class="text-muted">Nationality:</span>
                    <span><?= esc($user['nationality'] ?? '-') ?></span>
                </div>

                <!-- Identification -->
                <div class="col-md-6">
                    <span class="text-muted">National ID:</span>
                    <span><?= esc($user['national_id'] ?? '-') ?></span>
                </div>

                <div class="col-md-6">
                    <span class="text-muted">Ethnicity:</span>
                    <span><?= esc($user['ethnicity_id'] ?? '-') ?></span>
                </div>

                <!-- Location -->
                <div class="col-md-6">
                    <span class="text-muted">County (Origin):</span>
                    <span><?= esc($user['county_of_origin_name'] ?? '-') ?></span>
                </div>

                <div class="col-md-6">
                    <span class="text-muted">County (Residence):</span>
                    <span><?= esc($user['county_of_residence_name'] ?? '-') ?></span>
                </div>

                <!-- Disability -->
                <div class="col-md-6">
                    <span class="text-muted">Disability:</span>
                    <?php if (!empty($user['disability_status'])): ?>
                        <span class="badge bg-secondary text-white">Yes</span>
                    <?php else: ?>
                        <span class="badge bg-primary white">No</span>
                    <?php endif; ?>
                </div>

                <?php if (!empty($user['disability_status'])): ?>
                    <div class="col-md-6">
                        <span class="text-muted">Disability No:</span>
                        <span><?= esc($user['disability_number'] ?? '-') ?></span>
                    </div>

                    <div class="col-md-6">
                        <span class="text-muted">Disability Type:</span>
                        <span><?= esc($user['disability_type'] ?? '-') ?></span>
                    </div>
                <?php endif; ?>

                <!-- System Info -->

                <div class="col-md-6">
                    <span class="text-muted">Status:</span>
                    <?php if (!empty($user['active'])): ?>
                        <span class="badge bg-primary">Active</span>
                    <?php else: ?>
                        <span class="badge bg-secondary">Inactive</span>
                    <?php endif; ?>
                </div>

                <div class="col-md-6">
                    <span class="text-muted">Registered:</span>
                    <span><?= !empty($user['created_at']) ? date('d M Y, H:i', strtotime($user['created_at'])) : '-' ?></span>
                </div>

                <div class="col-md-6">
                    <span class="text-muted">Last Login:</span>
                    <span><?= !empty($user['last_login']) ? date('d M Y, H:i', strtotime($user['last_login'])) : '-' ?></span>
                </div>

            </div>
        </div>
    </div>

</div>

<style>
    .card-body span {
        font-size: 0.85rem;
    }
    .text-muted {
        margin-right: 4px;
    }
</style>

<?= $this->endSection() ?>
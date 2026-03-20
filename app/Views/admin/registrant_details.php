<?= $this->extend('layouts/dashboard') ?>
<?= $this->section('content') ?>

<div class="container-fluid px-4 py-4">

    <!-- Breadcrumb -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="<?= base_url('admin/users') ?>">Users</a></li>
                    <li class="breadcrumb-item active" aria-current="page">User Details</li>
                </ol>
            </nav>
        </div>
        <small class="text-muted"><?= date('d M Y, H:i') ?></small>
    </div>

    <!-- Page Header -->
    <div class="mb-4">
        <h4 class="fw-bold mb-1">User Details</h4>
        <p class="text-muted mb-0"><?= esc(trim($user['first_name'] . ' ' . $user['last_name'])) ?></p>
    </div>

    <!-- User Details Card -->
    <div class="card shadow-sm border-0">
        <div class="card-body p-4">
            <div class="row g-3">

                <div class="col-md-6">
                    <p class="fw-semibold mb-1">Full Name</p>
                    <p class="mb-0"><?= esc(trim($user['first_name'] . ' ' . $user['last_name'])) ?></p>
                </div>

                <div class="col-md-6">
                    <p class="fw-semibold mb-1">Email</p>
                    <p class="mb-0"><?= esc($user['email']) ?></p>
                </div>

                <div class="col-md-6">
                    <p class="fw-semibold mb-1">National ID</p>
                    <p class="mb-0"><?= esc($user['national_id'] ?? '-') ?></p>
                </div>

                <div class="col-md-6">
                    <p class="fw-semibold mb-1">Gender</p>
                    <p class="mb-0"><?= esc($user['gender'] ?? '-') ?></p>
                </div>

                <div class="col-md-6">
                    <p class="fw-semibold mb-1">Date of Birth</p>
                    <p class="mb-0"><?= !empty($user['dob']) ? date('d M Y', strtotime($user['dob'])) : '-' ?></p>
                </div>

                <div class="col-md-6">
                    <p class="fw-semibold mb-1">Phone</p>
                    <p class="mb-0"><?= esc($user['phone'] ?? '-') ?></p>
                </div>

                <div class="col-md-6">
                    <p class="fw-semibold mb-1">Ethnicity</p>
                    <p class="mb-0"><?= esc($user['ethnicity_id'] ?? '-') ?></p>
                </div>

                <div class="col-md-6">
                    <p class="fw-semibold mb-1">Disability Status</p>
                    <p class="mb-0"><?= esc($user['disability_status'] ?? '-') ?></p>
                </div>

                <div class="col-md-6">
                    <p class="fw-semibold mb-1">Disability Number</p>
                    <p class="mb-0"><?= esc($user['disability_number'] ?? '-') ?></p>
                </div>

                <div class="col-md-6">
                    <p class="fw-semibold mb-1">County of Origin</p>
                    <p class="mb-0"><?= esc($user['county_of_origin_id'] ?? '-') ?></p>
                </div>

                <div class="col-md-6">
                    <p class="fw-semibold mb-1">County of Residence</p>
                    <p class="mb-0"><?= esc($user['county_of_residence_id'] ?? '-') ?></p>
                </div>

                <div class="col-md-6">
                    <p class="fw-semibold mb-1">Disability Type</p>
                    <p class="mb-0"><?= esc($user['disability_type'] ?? '-') ?></p>
                </div>

                <div class="col-md-6">
                    <p class="fw-semibold mb-1">Nationality</p>
                    <p class="mb-0"><?= esc($user['nationality'] ?? '-') ?></p>
                </div>

                <div class="col-md-6">
                    <p class="fw-semibold mb-1">Profile Completed</p>
                    <p class="mb-0">
                        <?php if (!empty($user['profile_completed']) && $user['profile_completed'] == 1): ?>
                            <span class="badge bg-success">Yes</span>
                        <?php else: ?>
                            <span class="badge bg-secondary">No</span>
                        <?php endif; ?>
                    </p>
                </div>

                <div class="col-md-6">
                    <p class="fw-semibold mb-1">Status</p>
                    <p class="mb-0">
                        <?php if (!empty($user['active']) && $user['active'] == 1): ?>
                            <span class="badge bg-primary">Active</span>
                        <?php else: ?>
                            <span class="badge bg-secondary">Inactive</span>
                        <?php endif; ?>
                    </p>
                </div>

                <div class="col-md-6">
                    <p class="fw-semibold mb-1">Registered On</p>
                    <p class="mb-0"><?= !empty($user['created_at']) ? date('d M Y, H:i', strtotime($user['created_at'])) : '-' ?></p>
                </div>

                <div class="col-md-6">
                    <p class="fw-semibold mb-1">Last Login</p>
                    <p class="mb-0"><?= !empty($user['last_login']) ? date('d M Y, H:i', strtotime($user['last_login'])) : '-' ?></p>
                </div>

            </div>
        </div>
    </div>

    <div class="mt-3">
        <a href="<?= base_url('admin/users') ?>" class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-arrow-left me-1"></i> Back to Users
        </a>
    </div>

</div>

<!-- Custom CSS for smaller details font -->
<style>
    .card-body p {
        font-size: 0.9rem;
    }
</style>

<?= $this->endSection() ?>
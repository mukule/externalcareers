<?= $this->extend('layouts/dashboard') ?>
<?= $this->section('content') ?>

<div class="container-fluid px-4">

    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mt-4">
        <ol class="breadcrumb mb-3">
            <li class="breadcrumb-item">
                <a href="<?= base_url('admin') ?>">Dashboard</a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">
                Applicants
            </li>
        </ol>
    </nav>

    <div class="card mb-4">
        <div class="card-body">
            <?php if (!empty($applicants) && count($applicants) > 0): ?>
                <div class="table-responsive">
                    <table id="datatablesSimple" class="table table-bordered table-striped align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th>#</th>
                                <th>Full Name</th>
                                <th>Email</th>
                                <th>Status</th>
                                <th>Registered</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($applicants as $index => $user): ?>
                                <tr>
                                    <td><?= $index + 1 ?></td>
                                    <td>
                                        <?= esc(trim(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? ''))) ?>
                                    </td>
                                    <td><?= esc($user['email']) ?></td>
                                    <td class="text-center">
                                        <?php if (!empty($user['active'])): ?>
                                            <span class="badge bg-success">Active</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">Inactive</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= esc(date('d M Y', strtotime($user['created_at']))) ?></td>
                                    <td class="text-center">
                                        <a href="<?= base_url('admin/applicants/view/' . $user['uuid']) ?>" 
                                           class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye"></i>
                                        </a>

                                        <a href="<?= base_url('admin/applicants/toggle-status/' . $user['uuid']) ?>" 
                                           class="btn btn-sm btn-outline-warning"
                                           onclick="return confirm('Are you sure you want to change this applicant status?');">
                                           <i class="fas fa-user-slash"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p class="text-muted text-center mb-0">No applicants found.</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

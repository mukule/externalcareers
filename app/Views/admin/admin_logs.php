<?= $this->extend('layouts/dashboard') ?>
<?= $this->section('content') ?>

<div class="container-fluid px-4 py-4">

    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">System Activity Logs</h4>
        <small class="text-muted"><?= date('d M Y, H:i') ?></small>
    </div>

    <!-- Logs Table Card -->
    <div class="row g-4">
        <div class="col-xl-12">
            <div class="card shadow-sm border-0 p-2">

               

                <!-- Filters -->
                <div class="card-body py-2">
                    <form method="get" class="row g-2 align-items-end mb-3">

                        <div class="col-md-3">
                            <label class="form-label small">User Name</label>
                            <input type="text" name="user_name" class="form-control form-control-sm"
                                   value="<?= esc($filters['user_name'] ?? '') ?>" placeholder="Search Name">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label small">Email</label>
                            <input type="text" name="email" class="form-control form-control-sm"
                                   value="<?= esc($filters['email'] ?? '') ?>" placeholder="Search Email">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label small">Action</label>
                            <input type="text" name="action" class="form-control form-control-sm"
                                   value="<?= esc($filters['action'] ?? '') ?>" placeholder="Search Action">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label small">From Date</label>
                            <input type="date" name="date_from" class="form-control form-control-sm"
                                   value="<?= esc($filters['date_from'] ?? '') ?>">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label small">To Date</label>
                            <input type="date" name="date_to" class="form-control form-control-sm"
                                   value="<?= esc($filters['date_to'] ?? '') ?>">
                        </div>

                        <div class="col-md-3 d-flex gap-2">
                            <button type="submit" class="btn btn-primary btn-sm flex-fill">Filter</button>

                            <?php if (!empty(array_filter($filters))): ?>
                                <a href="<?= current_url() ?>" class="btn btn-outline-secondary btn-sm flex-fill">Clear</a>
                            <?php endif; ?>
                        </div>
                    </form>

                    <?php if (!empty($logs)): ?>
                        <div class="table-responsive">
                            <table class="table align-middle dashboard-table table-striped table-bordered">
                                <thead class="table-light">
                                    <tr>
                                        <th>#</th>
                                        <th>User</th>
                                        <th>Email</th>
                                        <th>Action</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($logs as $index => $log): ?>
                                        <tr>
                                            <td><?= esc(($pager->getCurrentPage() - 1) * $pager->getPerPage() + $index + 1) ?></td>
                                            <td><?= esc(trim(($log['first_name'] ?? '') . ' ' . ($log['last_name'] ?? ''))) ?></td>
                                            <td><?= esc($log['email'] ?? '-') ?></td>
                                            <td><?= esc($log['action']) ?></td>
                                            <td><?= esc(date('d M Y, H:i', strtotime($log['date_created']))) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>

                            <!-- Pagination -->
                            <div class="p-2 d-flex justify-content-center">
                                <?= $pager->links('default', 'bootstrap5') ?>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-4">
                            <p class="text-muted mb-0">No logs found.</p>
                        </div>
                    <?php endif; ?>

                </div>
            </div>
        </div>
    </div>

</div>

<!-- Custom CSS for table -->
<style>
.dashboard-table {
    font-size: 0.85rem;
}
.dashboard-table th, 
.dashboard-table td {
    padding: 0.35rem 0.5rem;
    vertical-align: middle;
}
</style>

<?= $this->endSection() ?>
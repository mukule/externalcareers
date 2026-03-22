<?= $this->extend('layouts/dashboard') ?>
<?= $this->section('content') ?>

<div class="container-fluid px-4 py-4">

    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">Mail Queue</h4>
        <small class="text-muted"><?= date('d M Y, H:i') ?></small>
    </div>

    <!-- Mail Queue Table Card -->
    <div class="row g-4">
        <div class="col-xl-12">
            <div class="card shadow-sm border-0 p-2">

                <!-- Filters -->
                <div class="card-body py-2">
                    <form method="get" class="row g-2 align-items-end mb-3">

                        <div class="col-md-3">
                            <label class="form-label small">Recipient Email</label>
                            <input type="text" name="to_email" class="form-control form-control-sm"
                                   value="<?= esc($filters['to_email'] ?? '') ?>" placeholder="Search Email">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label small">Subject</label>
                            <input type="text" name="subject" class="form-control form-control-sm"
                                   value="<?= esc($filters['subject'] ?? '') ?>" placeholder="Search Subject">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label small">Status</label>
                            <select name="status" class="form-control form-control-sm">
                                <option value="">All</option>
                                <option value="pending" <?= (isset($filters['status']) && $filters['status'] === 'pending') ? 'selected' : '' ?>>Pending</option>
                                <option value="processing" <?= (isset($filters['status']) && $filters['status'] === 'processing') ? 'selected' : '' ?>>Processing</option>
                                <option value="failed" <?= (isset($filters['status']) && $filters['status'] === 'failed') ? 'selected' : '' ?>>Failed</option>
                                <option value="sent" <?= (isset($filters['status']) && $filters['status'] === 'sent') ? 'selected' : '' ?>>Sent</option>
                            </select>
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

                    <?php if (!empty($emails)): ?>
                        <div class="table-responsive">
                            <table class="table align-middle dashboard-table table-striped table-bordered">
                                <thead class="table-light">
                                    <tr>
                                        <th>#</th>
                                        <th>Recipient</th>
                                        <th>Subject</th>
                                        <th>Status</th>
                                        <th>Attempts</th>
                                        <th>Created At</th>
                                        <th>Sent At</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($emails as $index => $email): ?>
                                        <tr>
                                            <td><?= esc(($pagination['currentPage'] - 1) * $pagination['perPage'] + $index + 1) ?></td>
                                            <td><?= esc($email['to_email'] ?? '-') ?></td>
                                            <td><?= esc($email['subject'] ?? '-') ?></td>
                                            <td><?= esc(ucfirst($email['status'])) ?></td>
                                            <td><?= esc($email['attempts'] ?? 0) ?></td>
                                            <td><?= esc(date('d M Y, H:i', strtotime($email['created_at']))) ?></td>
                                            <td><?= $email['sent_at'] ? esc(date('d M Y, H:i', strtotime($email['sent_at']))) : '-' ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>

                            <!-- Pagination -->
                            <div class="p-2 d-flex justify-content-center">
                                <?php if (!empty($pager)) : ?>
                                    <?= $pager->links('default', 'bootstrap5') ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-4">
                            <p class="text-muted mb-0">No emails found in the queue.</p>
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
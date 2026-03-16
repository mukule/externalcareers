<?= $this->extend('layouts/dashboard') ?>
<?= $this->section('content') ?>

<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mt-4 mb-3">
        <h1 class="mb-0"><?= esc($title ?? 'Job Disciplines') ?></h1>
        <a href="<?= base_url('admin/job-disciplines/create') ?>" class="btn btn-primary">
            <i class="fas fa-plus me-1"></i> Add Job Discipline
        </a>
    </div>
    <hr>

    <div class="card mb-4">
        <div class="card-body">
            <?php if (!empty($jobDisciplines) && count($jobDisciplines) > 0): ?>
                <div class="table-responsive">
                    <table id="datatablesSimple" class="table table-bordered table-striped align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($jobDisciplines as $index => $discipline): ?>
                                <tr>
                                    <td><?= $index + 1 ?></td>
                                    <td><?= esc($discipline['name']) ?></td>
                                    <td class="text-center">
                                        <?php if ($discipline['active']): ?>
                                            <span class="badge bg-success">Active</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">Inactive</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <a href="<?= base_url('admin/job-disciplines/edit/' . $discipline['uuid']) ?>" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="<?= base_url('admin/job-disciplines/delete/' . $discipline['uuid']) ?>" 
                                           class="btn btn-sm btn-outline-danger"
                                           onclick="return confirm('Are you sure you want to delete this job discipline?');">
                                           <i class="fas fa-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p class="text-muted text-center mb-0">No job disciplines found.</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->extend('layouts/dashboard') ?>
<?= $this->section('content') ?>

<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mt-4 mb-3">
        <h1 class="mb-0"><?= esc($title ?? 'Job Types') ?></h1>
        <a href="<?= base_url('admin/job-types/create') ?>" class="btn btn-primary">
            <i class="fas fa-plus me-1"></i> Add Job Type
        </a>
    </div>
    <hr>

    <div class="card mb-4">
        <div class="card-body">
            <?php if (!empty($jobTypes) && count($jobTypes) > 0): ?>
                <div class="table-responsive">
                    <table id="datatablesSimple" class="table table-bordered table-striped align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th>#</th>
                                <th>Icon</th>
                                <th>Name</th>
                                <th>Display Name</th>
                                <th>Description</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($jobTypes as $index => $jobType): ?>
                                <tr>
                                    <td><?= $index + 1 ?></td>
                                    
                                    <!-- Icon -->
                                    <td class="text-center">
                                        <?php if (!empty($jobType['icon'])): ?>
                                            <img src="<?= base_url('uploads/job_types/' . $jobType['icon']) ?>" 
                                                 alt="Icon" class="img-fluid" style="max-height:40px;">
                                        <?php else: ?>
                                            <span class="text-muted">—</span>
                                        <?php endif; ?>
                                    </td>

                                    <td><?= esc($jobType['name']) ?></td>
                                    <td><?= esc($jobType['display_name']) ?></td>

                                    <!-- Description -->
                                    <td><?= esc($jobType['description']) ?: '<span class="text-muted">—</span>' ?></td>

                                    <td class="text-center">
                                        <a href="<?= base_url('admin/job-types/edit/' . $jobType['uuid']) ?>" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="<?= base_url('admin/job-types/delete/' . $jobType['uuid']) ?>" 
                                           class="btn btn-sm btn-outline-danger"
                                           onclick="return confirm('Are you sure you want to delete this job type?');">
                                           <i class="fas fa-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p class="text-muted text-center mb-0">No job types found.</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
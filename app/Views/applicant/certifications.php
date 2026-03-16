<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="container py-5">

    <?= $this->include('partials/prof_nav', ['currentStep' => 5]) ?>

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <!-- Title and Add Button -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="card-title mb-0">My Certifications</h4>
                <a href="<?= base_url('applicant/certification/create') ?>" class="btn btn-primary">Add New Certification</a>
            </div>

            <!-- Responsive Certification Table -->
            <div class="table-responsive">
                <table class="table table-bordered table-striped mb-4">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Certification</th>
                            <th>Certifying Body</th>
                            <th>Attained Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(!empty($certifications)): ?>
                            <?php foreach($certifications as $i => $cert): ?>
                                <tr>
                                    <td><?= $i + 1 ?></td>
                                    <td><?= esc($cert['name']) ?></td>
                                    <td><?= esc($cert['cert_name'] ?? 'N/A') ?></td>
                                    <td><?= esc($cert['body_name'] ?? 'N/A') ?></td>
                                    <td><?= esc($cert['attained_date'] ?? 'N/A') ?></td>
                                    <td>
                                        <a href="<?= base_url('applicant/certification/edit/' . $cert['uuid']) ?>" class="btn btn-sm btn-outline-primary">Edit</a>
                                        <a href="<?= base_url('applicant/certification/delete/' . $cert['uuid']) ?>" class="btn btn-sm btn-outline-secondary" onclick="return confirm('Are you sure?');">Delete</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center">No certification records found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

        </div>
    </div>

</div>

<?= $this->endSection() ?>

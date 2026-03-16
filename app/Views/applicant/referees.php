<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="container py-5">

    <?= $this->include('partials/prof_nav', ['currentStep' => 7]) ?>

    <div class="card shadow-sm mb-4">
        <div class="card-body">

            <!-- Title + Add Button -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="card-title mb-0">My Referees</h4>
                <a href="<?= base_url('applicant/referees/create') ?>" class="btn btn-primary">Add New Referee</a>
            </div>

           
            <!-- Responsive Referees Table -->
            <div class="table-responsive">
                <table class="table table-bordered table-striped mb-4">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Referee Name</th>
                            <th>Position</th>
                            <th>Company</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($referees)): ?>
                            <?php foreach ($referees as $i => $ref): ?>
                                <tr>
                                    <td><?= $i + 1 ?></td>
                                    <td><?= esc($ref['name']) ?></td>
                                    <td><?= esc($ref['position'] ?? 'N/A') ?></td>
                                    <td><?= esc($ref['organization'] ?? 'N/A') ?></td>
                                    <td><?= esc($ref['email'] ?? 'N/A') ?></td>
                                    <td><?= esc($ref['phone'] ?? 'N/A') ?></td>
                                    <td>
                                        <a href="<?= base_url('applicant/referees/edit/' . $ref['uuid']) ?>"
                                           class="btn btn-sm btn-outline-primary">Edit</a>
                                        <a href="<?= base_url('applicant/referees/delete/' . $ref['uuid']) ?>"
                                           class="btn btn-sm btn-outline-secondary"
                                           onclick="return confirm('Are you sure?');">Delete</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center">No referee records found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

        </div>
    </div>

</div>

<?= $this->endSection() ?>

<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="container py-5">

    <?= $this->include('partials/prof_nav', ['currentStep' => 6]) ?>

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <!-- Title and Add Button -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="card-title mb-0">My Work Experience</h4>
                <a href="<?= base_url('applicant/work-experience/create') ?>" class="btn btn-primary">Add New Experience</a>
            </div>


            <!-- Responsive Work Experience Table -->
            <div class="table-responsive">
                <table class="table table-bordered table-striped mb-4">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Company Name</th>
                            <th>Position</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th>Reference Letter</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(!empty($experiences)): ?>
                            <?php foreach($experiences as $i => $exp): ?>
                                <tr>
                                    <td><?= $i + 1 ?></td>
                                    <td><?= esc($exp['company_name']) ?></td>
                                    <td><?= esc($exp['position']) ?></td>
                                    <td><?= esc($exp['start_date'] ?? 'N/A') ?></td>
                                    <td>
                                        <?php if ($exp['currently_working'] == 1): ?>
                                            <span class="badge bg-success p-2">Currently Working</span>
                                        <?php else: ?>
                                            <?= esc($exp['end_date'] ?? 'N/A') ?>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if(!empty($exp['reference_file'])): ?>
                                            <a href="<?= base_url('uploads/work_experience/' . $exp['reference_file']) ?>" target="_blank">View File</a>
                                        <?php else: ?>
                                            N/A
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <a href="<?= base_url('applicant/work-experience/edit/' . $exp['uuid']) ?>" class="btn btn-sm btn-outline-primary">Edit</a>
                                        <a href="<?= base_url('applicant/work-experience/delete/' . $exp['uuid']) ?>" class="btn btn-sm btn-outline-secondary" onclick="return confirm('Are you sure?');">Delete</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center">No work experience records found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

        </div>
    </div>

</div>

<?= $this->endSection() ?>

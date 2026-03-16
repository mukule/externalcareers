<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="container py-5">

    <?= $this->include('partials/prof_nav', ['currentStep' => 3]) ?>

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <!-- Title and Add Button on same row -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="card-title mb-0">My Education</h4>
                <a href="<?= base_url('applicant/education/create') ?>" class="btn btn-primary">Add New Education</a>
            </div>

            <!-- Responsive Education Table -->
            <div class="table-responsive">
                <table class="table table-bordered table-striped mb-4">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Level</th>
                            <th>Institution</th>
                            <th>Course</th>
                            <th>Grade</th>
                            <th>Years</th>
                            <th>Certificate</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(!empty($educations)): ?>
                            <?php foreach($educations as $i => $edu): ?>
                                <tr>
                                    <td><?= $i + 1 ?></td>
                                    <td><?= esc($edu['level_name'] ?? '') ?></td>
                                    <td><?= esc($edu['institution']) ?></td>
                                    <td><?= esc($edu['course']) ?></td>
                                    <td><?= esc($edu['grade']) ?></td>
                                    <td><?= esc($edu['start_year']) ?> - <?= esc($edu['end_year']) ?></td>
                                    <td>
                                        <?php if($edu['certificate']): ?>
                                            <a href="<?= base_url('uploads/certificates/' . $edu['certificate']) ?>" target="_blank">View PDF</a>
                                        <?php else: ?>
                                            N/A
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <a href="<?= base_url('applicant/education/edit/' . $edu['uuid']) ?>" class="btn btn-sm btn-outline-primary">Edit</a>
                                        <a href="<?= base_url('applicant/education/delete/' . $edu['uuid']) ?>" class="btn btn-sm btn-outline-secondary" onclick="return confirm('Are you sure?');">Delete</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" class="text-center">No education records found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

        </div>
    </div>

</div>

<?= $this->endSection() ?>

<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="container py-5">

    <?= $this->include('partials/prof_nav', ['currentStep' => 6]) ?>

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <!-- Title and Add Button -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="card-title mb-0">Work Experience</h4>
                <a href="<?= base_url('applicant/work-experience/create') ?>" class="btn btn-outline-primary">Add New Experience</a>
            </div>

            <!-- Responsive Work Experience Table -->
            <div class="table-responsive">
                <table class="table table-bordered table-striped mb-4" style="font-size:0.9rem;">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Company Name</th>
                            <th>Position</th>
                            <th>Dates</th>
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
                                    <td>
                                        <?= esc($exp['start_date'] ?? 'N/A') ?> -  
                                        <?php if ($exp['currently_working'] == 1): ?>
                                            <span class="badge bg-success p-2">Current Job</span>
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
                                        <button type="button" class="btn btn-sm btn-primary text-white" data-bs-toggle="modal" data-bs-target="#detailsModal<?= $i ?>">View More</button>
                                    </td>
                                </tr>

                                <!-- Modal for Additional Details -->
                                <div class="modal fade" id="detailsModal<?= $i ?>" tabindex="-1" aria-labelledby="detailsModalLabel<?= $i ?>" aria-hidden="true">
                                    <div class="modal-dialog modal-lg">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="detailsModalLabel<?= $i ?>">Work Experience Details - <?= esc($exp['company_name']) ?></h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <p><strong>Company Name:</strong> <?= esc($exp['company_name']) ?></p>
                                                <p><strong>Position:</strong> <?= esc($exp['position']) ?></p>
                                                <p><strong>Company Address:</strong> <?= esc($exp['company_address'] ?? 'N/A') ?></p>
                                                <p><strong>Company Phone:</strong> <?= esc($exp['company_phone'] ?? 'N/A') ?></p>
                                                <p><strong>Dates:</strong> <?= esc($exp['start_date'] ?? 'N/A') ?> - <?= $exp['currently_working'] ? 'Current Job' : esc($exp['end_date'] ?? 'N/A') ?></p>
                                                <p><strong>Responsibilities / Job Description:</strong><br><?= nl2br(esc($exp['responsibilities'] ?? 'N/A')) ?></p>
                                                <p><strong>Reference Letter:</strong> 
                                                    <?php if(!empty($exp['reference_file'])): ?>
                                                        <a href="<?= base_url('uploads/work_experience/' . $exp['reference_file']) ?>" target="_blank">View File</a>
                                                    <?php else: ?>
                                                        N/A
                                                    <?php endif; ?>
                                                </p>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center">No work experience records found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

        </div>
    </div>

</div>

<?= $this->endSection() ?>
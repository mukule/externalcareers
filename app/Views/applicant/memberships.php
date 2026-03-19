<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="container py-5">

    <?= $this->include('partials/prof_nav', ['currentStep' => 4]) ?>

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <!-- Title and Add Button on same row -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="card-title mb-0">Professional Memberships</h4>
                <a href="<?= base_url('applicant/membership/create') ?>" class="btn btn-outline-primary">Add New Membership</a>
            </div>

            <!-- Responsive Membership Table -->
            <div class="table-responsive">
                <table class="table table-bordered table-striped mb-4" style="font-size: 0.9rem;">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Certifying Body</th>
                            <th>Membership No</th>
                            <th>Joined Year</th>
                            <th>Certificate</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(!empty($memberships)): ?>
                            <?php foreach($memberships as $i => $membership): ?>
                                <tr>
                                    <td><?= $i + 1 ?></td>
                                    <td><?= esc($membership['name']) ?></td>
                                    <td><?= esc($membership['body_name'] ?? 'N/A') ?></td>
                                    <td><?= esc($membership['membership_no'] ?? 'N/A') ?></td>
                                    <td><?= esc($membership['joined_date'] ?? 'N/A') ?></td>
                                    <td>
                                        <?php if($membership['certificate']): ?>
                                            <a href="<?= base_url('uploads/memberships/' . $membership['certificate']) ?>" target="_blank">View PDF</a>
                                        <?php else: ?>
                                            N/A
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <a href="<?= base_url('applicant/membership/edit/' . $membership['uuid']) ?>" class="btn btn-sm btn-outline-primary">Edit</a>
                                        <a href="<?= base_url('applicant/membership/delete/' . $membership['uuid']) ?>" class="btn btn-sm btn-outline-secondary" onclick="return confirm('Are you sure?');">Delete</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center">No membership records found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

        </div>
    </div>

</div>

<?= $this->endSection() ?>
<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="container py-5">

    <?= $this->include('partials/prof_nav', ['currentStep' => 3]) ?>

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <!-- Title and Add Button -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="card-title mb-0">Basic Education</h4>
                <a href="<?= base_url('applicant/basic-education/create') ?>" class="btn btn-outline-primary">Add New Record</a>
            </div>

            <!-- Responsive Basic Education Table -->
            <div class="table-responsive">
                <table class="table table-bordered table-striped mb-4">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>School</th>
                            <th>Certification</th>
                            <th>Dates</th>
                            <th>Grade</th>
                            <th>Certificate</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(!empty($educations)): ?>
                            <?php foreach($educations as $i => $edu): ?>
                                <tr>
                                    <td><?= $i + 1 ?></td>
                                    <td><?= esc($edu['school_name'] ?? 'N/A') ?></td>
                                    <td>
                                        <?php
                                            if (!empty($edu['certification'])) {
                                                // show full name for KCPE/KCSE
                                                if ($edu['certification'] === 'KCPE') echo 'Kenya Certificate of Primary Education (KCPE)';
                                                elseif ($edu['certification'] === 'KCSE') echo 'Kenya Certificate of Secondary Education (KCSE)';
                                                else echo esc($edu['certification']);
                                            } else {
                                                echo 'N/A';
                                            }
                                        ?>
                                    </td>
                                    <td>
                                        <?php
                                            $start = !empty($edu['date_started']) 
                                                ? date('M Y', strtotime($edu['date_started'])) 
                                                : '-';

                                            $end = !empty($edu['date_ended']) 
                                                ? date('M Y', strtotime($edu['date_ended'])) 
                                                : 'Present';

                                            echo $start . ' - ' . $end;
                                        ?>
                                    </td>
                                    <td><?= esc($edu['grade_attained'] ?? 'N/A') ?></td>
                                    <td>
                                        <?php if(!empty($edu['certificate'])): ?>
                                            <a href="<?= base_url('uploads/certificates/' . $edu['certificate']) ?>" target="_blank">View file</a>
                                        <?php else: ?>
                                            N/A
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <a href="<?= base_url('applicant/basic-education/edit/' . $edu['uuid']) ?>" class="btn btn-sm btn-outline-primary">Edit</a>
                                        <a href="<?= base_url('applicant/basic-education/delete/' . $edu['uuid']) ?>" class="btn btn-sm btn-outline-secondary" onclick="return confirm('Are you sure?');">Delete</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center">No basic education records found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

        </div>
    </div>

</div>

<?= $this->endSection() ?>
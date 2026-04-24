<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="container py-5">

    <?= $this->include('partials/prof_nav', ['currentStep' => 3]) ?>

    <div class="card shadow-sm mb-4">
        <div class="card-body">

            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="card-title mb-0">Higher Education</h4>
                <a href="<?= base_url('applicant/higher-education/create') ?>" class="btn btn-outline-primary">
                    Add New Record
                </a>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered table-striped mb-4">

                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Institution</th>
                            <th>Course</th>
                            <th>Certification</th>
                            <th>Years</th>
                            <th>Class Attained</th>
                            <th>Certificate</th>
                            <th>Actions</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php if (!empty($educations)): ?>
                            <?php foreach ($educations as $i => $edu): ?>
                                <tr>

                                    <td><?= $i + 1 ?></td>

                                    <td><?= esc($edu['institution_name'] ?? 'N/A') ?></td>

                                    <td><?= esc($edu['course_name'] ?? 'N/A') ?></td>

                                    <!-- Certification -->
                                    <td>
                                        <?php
                                        if (!empty($levels) && !empty($edu['education_level_id'])) {
                                            $level = array_filter($levels, function ($l) use ($edu) {
                                                return $l['id'] == $edu['education_level_id'];
                                            });

                                            echo $level ? esc(array_values($level)[0]['name']) : 'N/A';
                                        } else {
                                            echo 'N/A';
                                        }
                                        ?>
                                    </td>

                                    <!-- YEARS ONLY -->
                                    <td>
                                        <?php
                                        $startYear = !empty($edu['date_started'])
                                            ? date('Y', strtotime($edu['date_started']))
                                            : '-';

                                        $endYearRaw = $edu['date_ended'] ?? null;

                                        if ($endYearRaw === 'present' || empty($endYearRaw)) {
                                            $endYear = 'Present';
                                        } else {
                                            $endYear = date('Y', strtotime($endYearRaw));
                                        }

                                        echo $startYear . ' - ' . $endYear;
                                        ?>
                                    </td>

                                    <td><?= esc($edu['class_attained'] ?? 'N/A') ?></td>

                                    <td>
                                        <?php if (!empty($edu['certificate'])): ?>
                                            <a href="<?= base_url('uploads/certs/' . $edu['certificate']) ?>" target="_blank">
                                                View file
                                            </a>
                                        <?php else: ?>
                                            N/A
                                        <?php endif; ?>
                                    </td>

                                    <td>
                                        <a href="<?= base_url('applicant/higher-education/edit/' . $edu['uuid']) ?>"
                                           class="btn btn-sm btn-outline-primary">
                                            Edit
                                        </a>

                                        <a href="<?= base_url('applicant/higher-education/delete/' . $edu['uuid']) ?>"
                                           class="btn btn-sm btn-outline-secondary"
                                           onclick="return confirm('Are you sure?');">
                                            Delete
                                        </a>
                                    </td>

                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" class="text-center">
                                    No College/University records found.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>

                </table>
            </div>

        </div>
    </div>

</div>

<?= $this->endSection() ?>
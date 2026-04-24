<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="container py-5">

    <?= $this->include('partials/prof_nav', ['currentStep' => 3]) ?>

    <div class="card shadow-sm mb-4">
        <div class="card-body">

            <h4 class="card-title mb-4">
                <?= isset($edu) ? 'Edit Higher Education' : 'Add New Higher Education' ?>
            </h4>

            <form action="<?= $action ?>" method="POST" enctype="multipart/form-data">
                <?= csrf_field() ?>

                <?php if(isset($edu)): ?>
                    <input type="hidden" name="id" value="<?= $edu['id'] ?>">
                <?php endif; ?>

                <!-- Institution & Course -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Institution <span class="text-danger">*</span></label>
                        <input type="text"
                               class="form-control"
                               name="institution_name"
                               value="<?= old('institution_name', $edu['institution_name'] ?? '') ?>"
                               required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Course <span class="text-danger">*</span></label>
                        <input type="text"
                               class="form-control"
                               name="course_name"
                               value="<?= old('course_name', $edu['course_name'] ?? '') ?>"
                               required>
                    </div>
                </div>

                <!-- Level & Class -->
                <div class="row mb-3">

                    <div class="col-md-6">
                        <label class="form-label">Level of Study <span class="text-danger">*</span></label>
                        <select class="form-select" name="education_level_id" required>
                            <option value="">Select Level</option>
                            <?php foreach($levels as $level): ?>
                                <option value="<?= $level['id'] ?>"
                                    <?= old('education_level_id', $edu['education_level_id'] ?? '') == $level['id'] ? 'selected' : '' ?>>
                                    <?= esc($level['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Class Attained</label>
                        <select class="form-select" name="class_attained">
                            <option value="">Select Class</option>
                            <?php
                            $classes = ['First Class', 'Second Class Upper', 'Second Class Lower', 'Third Class', 'Pass'];
                            foreach($classes as $c): ?>
                                <option value="<?= $c ?>"
                                    <?= old('class_attained', $edu['class_attained'] ?? '') == $c ? 'selected' : '' ?>>
                                    <?= $c ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                </div>

                <!-- YEARS ONLY -->
                <div class="row mb-3">

                    <div class="col-md-3">
                        <label class="form-label">Start Year</label>
                        <select class="form-select" name="date_started">
                            <option value="">Select Year</option>
                            <?php for ($y = date('Y'); $y >= 1980; $y--): ?>
                                <option value="<?= $y ?>"
                                    <?= old('date_started', $edu['date_started'] ?? '') == $y ? 'selected' : '' ?>>
                                    <?= $y ?>
                                </option>
                            <?php endfor; ?>
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">End Year</label>
                        <select class="form-select" name="date_ended">
                            <option value="">Select Year</option>

                            <option value="present"
                                <?= old('date_ended', $edu['date_ended'] ?? '') == 'present' ? 'selected' : '' ?>>
                                Present
                            </option>

                            <?php for ($y = date('Y'); $y >= 1980; $y--): ?>
                                <option value="<?= $y ?>"
                                    <?= old('date_ended', $edu['date_ended'] ?? '') == $y ? 'selected' : '' ?>>
                                    <?= $y ?>
                                </option>
                            <?php endfor; ?>
                        </select>
                    </div>

                </div>

                <!-- Certificate Upload -->
                <div class="row mb-3">
                    <div class="col-md-12">

                        <label class="form-label">
                            Certificate (PDF, max 1MB)
                            <?php if (!isset($edu)): ?>
                                <span class="text-danger">*</span>
                            <?php endif; ?>
                        </label>

                        <input type="file"
                               class="form-control"
                               name="certificate"
                               accept="application/pdf"
                               <?= !isset($edu) ? 'required' : '' ?>>

                        <small class="text-muted d-block mt-1">
                            Max size: 1MB. PDF only.
                        </small>

                        <?php if (!empty($edu['certificate'])): ?>
                            <div class="mt-2">
                                <small>Current:</small><br>
                                <a href="<?= base_url('uploads/certs/' . $edu['certificate']) ?>" target="_blank">
                                    View File
                                </a>
                            </div>
                        <?php endif; ?>

                    </div>
                </div>

                <!-- Buttons -->
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <?= isset($edu) ? 'Update' : 'Submit' ?>
                    </button>

                    <a href="<?= base_url('applicant/higher-education') ?>" class="btn btn-outline-primary">
                        Back
                    </a>
                </div>

            </form>

        </div>
    </div>

</div>

<!-- FRONTEND FILE SIZE VALIDATION -->
<script>
document.addEventListener('DOMContentLoaded', function () {

    const fileInput = document.querySelector('input[name="certificate"]');

    if (!fileInput) return;

    fileInput.addEventListener('change', function () {
        const file = this.files[0];
        if (!file) return;

        const maxSize = 1 * 1024 * 1024; // 1MB

        if (file.size > maxSize) {
            alert('File too large. Maximum allowed size is 1MB.');
            this.value = '';
        }
    });

});
</script>

<?= $this->endSection() ?>
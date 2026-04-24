<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="container py-5">

    <?= $this->include('partials/prof_nav', ['currentStep' => 3]) ?>

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h4 class="card-title mb-4">
                <?= isset($edu) ? 'Edit Basic Education' : 'Add New Basic Education' ?>
            </h4>

            <form action="<?= $action ?>" method="POST" enctype="multipart/form-data">
                <?= csrf_field() ?>

                <?php if (isset($edu)): ?>
                    <input type="hidden" name="id" value="<?= $edu['id'] ?>">
                <?php endif; ?>

                <!-- School Name & Certification -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">School Name <span class="text-danger">*</span></label>
                        <input type="text"
                               class="form-control"
                               name="school_name"
                               value="<?= old('school_name', $edu['school_name'] ?? '') ?>"
                               required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Certification <span class="text-danger">*</span></label>
                        <select class="form-select" name="certification" required>
                            <option value="">Select Certification</option>
                            <?php foreach ($certifications as $code => $name): ?>
                                <option value="<?= esc($code) ?>"
                                    <?= old('certification', $edu['certification'] ?? '') == $code ? 'selected' : '' ?>>
                                    <?= esc($name) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <!-- Years -->
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

                    <div class="col-md-6">
                        <label class="form-label">Grade</label>
                        <select class="form-select" name="grade_attained">
                            <option value="">Select Grade</option>
                            <?php foreach ($grades as $g): ?>
                                <option value="<?= $g ?>"
                                    <?= old('grade_attained', $edu['grade_attained'] ?? '') == $g ? 'selected' : '' ?>>
                                    <?= $g ?>
                                </option>
                            <?php endforeach; ?>
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
                            Only PDF files allowed. Maximum size: 1MB.
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

                    <a href="<?= base_url('applicant/basic-education') ?>" class="btn btn-outline-primary">
                        Back to List
                    </a>
                </div>

            </form>

        </div>
    </div>

</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const fileInput = document.querySelector('input[name="certificate"]');

    fileInput.addEventListener('change', function () {
        const file = this.files[0];
        if (!file) return;

        const maxSize = 1 * 1024 * 1024;

        if (file.size > maxSize) {
            alert('File too large. Maximum allowed size is 1MB.');
            this.value = '';
        }
    });
});
</script>

<?= $this->endSection() ?>
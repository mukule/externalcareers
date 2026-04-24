<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<?php
// =========================
// Normalize DB DATE → MONTH INPUT
// =========================
$startMonth = '';
$endMonth   = '';

if (!empty($experience['start_date'])) {
    $startMonth = date('Y-m', strtotime($experience['start_date']));
}

if (!empty($experience['end_date'])) {
    $endMonth = date('Y-m', strtotime($experience['end_date']));
}
?>

<div class="container py-5">

    <?= $this->include('partials/prof_nav', ['currentStep' => 7]) ?>

    <div class="card shadow-sm mb-4">
        <div class="card-body">

            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="card-title mb-0">
                    <?= isset($experience) ? 'Edit Work Experience' : 'Add New Work Experience' ?>
                </h4>
            </div>

            <?php if(session()->getFlashdata('error')): ?>
                <div class="alert alert-danger">
                    <?= is_array(session()->getFlashdata('error'))
                        ? implode('<br>', session()->getFlashdata('error'))
                        : session()->getFlashdata('error') ?>
                </div>
            <?php endif; ?>

            <form action="<?= $action ?>" method="POST" enctype="multipart/form-data">
                <?= csrf_field() ?>

                <?php if(isset($experience)): ?>
                    <input type="hidden" name="id" value="<?= $experience['id'] ?>">
                <?php endif; ?>

                <!-- COMPANY + POSITION -->
                <div class="row mb-3">

                    <div class="col-md-6">
                        <label class="form-label">Company Name</label>
                        <input type="text"
                               class="form-control"
                               name="company_name"
                               value="<?= old('company_name', $experience['company_name'] ?? '') ?>"
                               required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Position</label>
                        <input type="text"
                               class="form-control"
                               name="position"
                               value="<?= old('position', $experience['position'] ?? '') ?>"
                               required>
                    </div>

                </div>

                <!-- ADDRESS + PHONE -->
                <div class="row mb-3">

                    <div class="col-md-6">
                        <label class="form-label">Company Address</label>
                        <input type="text"
                               class="form-control"
                               name="company_address"
                               value="<?= old('company_address', $experience['company_address'] ?? '') ?>">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Company Phone</label>
                        <input type="text"
                               class="form-control"
                               name="company_phone"
                               value="<?= old('company_phone', $experience['company_phone'] ?? '') ?>">
                    </div>

                </div>

                <!-- DATES -->
                <div class="row mb-3">

                    <div class="col-md-6">
                        <label class="form-label">Date Started</label>
                        <input type="month"
                               class="form-control"
                               name="start_date"
                               value="<?= old('start_date', $startMonth) ?>"
                               required>
                    </div>

                    <div class="col-md-6 d-flex align-items-center mt-4">

                        <div class="form-check">
                            <input type="checkbox"
                                   class="form-check-input"
                                   id="currently_working"
                                   name="currently_working"
                                   value="1"
                                   <?= !empty($experience['currently_working']) ? 'checked' : '' ?>>

                            <label class="form-check-label" for="currently_working">
                                I am currently working here
                            </label>
                        </div>

                    </div>

                </div>

                <div class="row mb-3">

                    <div class="col-md-6">
                        <label class="form-label">Date Ended (Optional)</label>

                        <input type="month"
                               class="form-control"
                               name="end_date"
                               id="end_date"
                               value="<?= old('end_date', $endMonth) ?>"
                               <?= !empty($experience['currently_working']) ? 'disabled' : '' ?>>
                    </div>

                </div>

                <!-- RESPONSIBILITIES -->
                <div class="mb-3">
                    <label class="form-label">Key Responsibilities (Max 500 characters)</label>
                    <textarea class="form-control"
                              name="responsibilities"
                              rows="5"><?= old('responsibilities', $experience['responsibilities'] ?? '') ?></textarea>
                </div>

                <!-- FILE UPLOAD -->
                <div class="mb-3">

                    <label class="form-label">
                        Reference Letter (PDF, max 1MB)
                       
                    </label>

                    <input type="file"
                           class="form-control"
                           name="reference_letter"
                           id="reference_letter"
                           accept="application/pdf"
                          >

                    <small class="text-muted d-block mt-1">
                        PDF only. Maximum size: 1MB.
                    </small>

                    <?php if(!empty($experience['reference_file'])): ?>
                        <div class="mt-2">
                            <small>Current:</small><br>
                            <a href="<?= base_url('uploads/certs/' . $experience['reference_file']) ?>" target="_blank">
                                View File
                            </a>
                        </div>
                    <?php endif; ?>

                </div>

                <!-- BUTTONS -->
                <button type="submit" class="btn btn-primary">
                    <?= isset($experience) ? 'Update Experience' : 'Add Experience' ?>
                </button>

                <a href="<?= base_url('applicant/work-experience') ?>" class="btn btn-secondary">
                    Back to List
                </a>

            </form>

        </div>
    </div>
</div>

<!-- JS -->
<script>
document.addEventListener('DOMContentLoaded', function () {

    const checkbox = document.getElementById('currently_working');
    const endDate  = document.getElementById('end_date');
    const fileInput = document.getElementById('reference_letter');

    function toggleEndDate() {
        endDate.disabled = checkbox.checked;
        if (checkbox.checked) endDate.value = '';
    }

    checkbox.addEventListener('change', toggleEndDate);
    toggleEndDate();

    // FILE VALIDATION (1MB)
    if (fileInput) {
        fileInput.addEventListener('change', function () {

            const file = this.files[0];
            if (!file) return;

            const maxSize = 1 * 1024 * 1024;

            if (file.size > maxSize) {
                alert('File too large. Maximum allowed size is 1MB.');
                this.value = '';
                return;
            }

            if (file.type !== 'application/pdf') {
                alert('Only PDF files are allowed.');
                this.value = '';
            }

        });
    }

});
</script>

<?= $this->endSection() ?>
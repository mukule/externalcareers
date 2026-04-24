<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="container py-5">

    <?= $this->include('partials/prof_nav', ['currentStep' => 4]) ?>

    <div class="card shadow-sm mb-4">
        <div class="card-body">

            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="card-title mb-0">
                    <?= isset($membership) ? 'Edit Membership' : 'Add New Membership' ?>
                </h4>
            </div>

            <form action="<?= $action ?>" method="POST" enctype="multipart/form-data">
                <?= csrf_field() ?>

                <?php if(isset($membership)): ?>
                    <input type="hidden" name="id" value="<?= $membership['id'] ?>">
                <?php endif; ?>

                <!-- TITLE + NUMBER -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">
                            Membership Title <span class="text-danger">*</span>
                        </label>

                        <input type="text"
                               class="form-control"
                               name="name"
                               value="<?= old('name', $membership['name'] ?? '') ?>"
                               required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Membership Number</label>
                        <input type="text"
                               class="form-control"
                               name="membership_no"
                               value="<?= old('membership_no', $membership['membership_no'] ?? '') ?>">
                    </div>
                </div>

                <!-- BODY + YEAR -->
                <div class="row mb-3">

                    <div class="col-md-6">
                        <label class="form-label">
                            Certifying Body <span class="text-danger">*</span>
                        </label>

                        <select name="certifying_body_id" class="form-select" required>
                            <option value="">Select Body</option>

                            <?php foreach($bodies as $body): ?>
                                <option value="<?= esc($body['id']) ?>"
                                    <?= old('certifying_body_id', $membership['certifying_body_id'] ?? '') == $body['id'] ? 'selected' : '' ?>>
                                    <?= esc($body['name']) ?>
                                </option>
                            <?php endforeach; ?>

                        </select>
                    </div>

                    <!-- YEAR JOINED (UPDATED) -->
                    <div class="col-md-6">
                        <label class="form-label">
                            Year Joined <span class="text-danger">*</span>
                        </label>

                        <select class="form-select" name="joined_date" required>
                            <option value="">Select Year</option>

                            <?php for ($y = date('Y'); $y >= 1980; $y--): ?>
                                <option value="<?= $y ?>"
                                    <?= old('joined_date', isset($membership['joined_date']) ? date('Y', strtotime($membership['joined_date'])) : '') == $y ? 'selected' : '' ?>>
                                    <?= $y ?>
                                </option>
                            <?php endfor; ?>

                        </select>
                    </div>

                </div>

                <!-- FILE UPLOAD -->
                <div class="row mb-3">
                    <div class="col-md-6">

                        <label class="form-label">
                            Certificate (PDF, max 1MB)
                            <?php if(!isset($membership)): ?>
                                <span class="text-danger">*</span>
                            <?php endif; ?>
                        </label>

                        <input type="file"
                               class="form-control"
                               name="certificate"
                               id="certificate"
                               accept="application/pdf"
                               <?= !isset($membership) ? 'required' : '' ?>>

                        <small class="text-muted d-block mt-1">
                            PDF only. Maximum size: 1MB.
                        </small>

                        <?php if(isset($membership['certificate']) && $membership['certificate']): ?>
                            <div class="mt-2">
                                <small>Current:</small><br>
                                <a href="<?= base_url('uploads/certs/' . $membership['certificate']) ?>" target="_blank">
                                    View PDF
                                </a>
                            </div>
                        <?php endif; ?>

                    </div>
                </div>

                <!-- BUTTONS -->
                <button type="submit" class="btn btn-primary">
                    <?= isset($membership) ? 'Update Membership' : 'Add Membership' ?>
                </button>

                <a href="<?= base_url('applicant/membership') ?>" class="btn btn-secondary">
                    Back to List
                </a>

            </form>

        </div>
    </div>

</div>

<!-- FRONTEND VALIDATION (1MB) -->
<script>
document.addEventListener('DOMContentLoaded', function () {

    const fileInput = document.getElementById('certificate');

    if (fileInput) {
        fileInput.addEventListener('change', function () {

            const file = this.files[0];
            if (!file) return;

            const maxSize = 1 * 1024 * 1024; // 1MB

            if (file.size > maxSize) {
                alert('File too large. Maximum allowed size is 1MB.');
                this.value = '';
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
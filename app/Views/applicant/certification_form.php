<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="container py-5">

    <?= $this->include('partials/prof_nav', ['currentStep' => 3]) ?>

    <div class="card shadow-sm mb-4">
        <div class="card-body">

            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="card-title mb-0">
                    <?= isset($certification) ? 'Edit Certification' : 'Add New Certification' ?>
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

                <?php if(isset($certification)): ?>
                    <input type="hidden" name="id" value="<?= $certification['id'] ?>">
                <?php endif; ?>

                <!-- TITLE + BODY -->
                <div class="row mb-3">

                    <div class="col-md-6">
                        <label class="form-label">
                            Certificate Title <span class="text-danger">*</span>
                        </label>

                        <input type="text"
                               class="form-control"
                               name="name"
                               value="<?= old('name', $certification['name'] ?? '') ?>"
                               required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">
                            Certifying Body <span class="text-danger">*</span>
                        </label>

                        <select name="certifying_body_id"
                                class="form-control"
                                required>
                            <option value="">Select Certifying Body</option>

                            <?php foreach($bodies as $body): ?>
                                <option value="<?= $body['id'] ?>"
                                    <?= (isset($certification['certifying_body_id']) && $certification['certifying_body_id'] == $body['id']) ? 'selected' : '' ?>>
                                    <?= esc($body['name']) ?>
                                </option>
                            <?php endforeach; ?>

                        </select>
                    </div>

                </div>

                <!-- DATE -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">
                            Attained Date <span class="text-danger">*</span>
                        </label>

                        <input type="date"
                               class="form-control"
                               name="attained_date"
                               value="<?= old('attained_date', $certification['attained_date'] ?? '') ?>"
                               required>
                    </div>
                </div>

                <!-- FILE UPLOAD -->
                <div class="row mb-3">
                    <div class="col-md-6">

                        <label class="form-label">
                            Certificate (PDF, max 1MB)
                            <?php if(!isset($certification)): ?>
                                <span class="text-danger">*</span>
                            <?php endif; ?>
                        </label>

                        <input type="file"
                               class="form-control"
                               name="certificate_file"
                               id="certificate_file"
                               accept="application/pdf"
                               <?= !isset($certification) ? 'required' : '' ?>>

                        <small class="text-muted d-block mt-1">
                            PDF only. Maximum size: 1MB.
                        </small>

                        <?php if(isset($certification['certificate_file']) && $certification['certificate_file']): ?>
                            <div class="mt-2">
                                <small>Current:</small><br>
                                <a href="<?= base_url('uploads/certs/' . $certification['certificate_file']) ?>" target="_blank">
                                    View PDF
                                </a>
                            </div>
                        <?php endif; ?>

                    </div>
                </div>

                <!-- BUTTONS -->
                <button type="submit" class="btn btn-primary">
                    <?= isset($certification) ? 'Update Certification' : 'Add Certification' ?>
                </button>

                <a href="<?= base_url('applicant/certification') ?>" class="btn btn-secondary">
                    Back to List
                </a>

            </form>

        </div>
    </div>

</div>

<!-- FRONTEND VALIDATION -->
<script>
document.addEventListener('DOMContentLoaded', function () {

    const fileInput = document.getElementById('certificate_file');

    if (!fileInput) return;

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

});
</script>

<?= $this->endSection() ?>
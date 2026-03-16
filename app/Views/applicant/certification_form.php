<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="container py-5">

    <?= $this->include('partials/prof_nav', ['currentStep' => 3]) ?>

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="card-title mb-0"><?= isset($certification) ? 'Edit Certification' : 'Add New Certification' ?></h4>
            </div>

            <?php if(session()->getFlashdata('error')): ?>
                <div class="alert alert-danger">
                    <?= is_array(session()->getFlashdata('error')) ? implode('<br>', session()->getFlashdata('error')) : session()->getFlashdata('error') ?>
                </div>
            <?php endif; ?>

            <form action="<?= $action ?>" method="POST" enctype="multipart/form-data">
                <?= csrf_field() ?>
                <?php if(isset($certification)): ?>
                    <input type="hidden" name="id" value="<?= $certification['id'] ?>">
                <?php endif; ?>

                <div class="row mb-3">
                    <!-- Certification Name -->
                    <div class="col-md-6">
                        <label for="name" class="form-label">Certification Name</label>
                        <input type="text" class="form-control" name="name" id="name" value="<?= $certification['name'] ?? '' ?>" required>
                    </div>

                    <!-- Certifying Body -->
                    <div class="col-md-6">
                        <label for="certifying_body_id" class="form-label">Certifying Body</label>
                        <select name="certifying_body_id" id="certifying_body_id" class="form-control" required>
                            <option value="">Select Certifying Body</option>
                            <?php foreach($bodies as $body): ?>
                                <option value="<?= $body['id'] ?>" <?= (isset($certification['certifying_body_id']) && $certification['certifying_body_id'] == $body['id']) ? 'selected' : '' ?>>
                                    <?= esc($body['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="row mb-3">
                    <!-- Certification Dropdown (Optional) -->
                    <div class="col-md-6">
                        <label for="certification_id" class="form-label">Select Certification (Optional)</label>
                        <select required name="certification_id" id="certification_id" class="form-control">
                            <option value="">Select Certification</option>
                            <?php if(isset($certification) && isset($certification['certification_id'])): ?>
                                <option value="<?= $certification['certification_id'] ?>" selected>
                                    <?= esc($certification['cert_name'] ?? '') ?>
                                </option>
                            <?php endif; ?>
                        </select>
                    </div>

                    <!-- Attained Date -->
                    <div class="col-md-6">
                        <label for="attained_date" class="form-label">Attained Date</label>
                        <input type="date" class="form-control" name="attained_date" id="attained_date" value="<?= $certification['attained_date'] ?? '' ?>" required>
                    </div>
                </div>

                <div class="row mb-3">
                    <!-- Certificate Upload -->
                    <div class="col-md-6">
                        <label for="certificate_file" class="form-label">Certificate (PDF, max 2MB)</label>
                        <input type="file" class="form-control" name="certificate_file" id="certificate_file" accept="application/pdf">
                        <?php if(isset($certification['certificate_file']) && $certification['certificate_file']): ?>
                            <small>Current: <a href="<?= base_url('uploads/certifications/' . $certification['certificate_file']) ?>" target="_blank">View PDF</a></small>
                        <?php endif; ?>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary"><?= isset($certification) ? 'Update Certification' : 'Add Certification' ?></button>
                <a href="<?= base_url('applicant/certification') ?>" class="btn btn-secondary">Back to List</a>
            </form>
        </div>
    </div>

</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const bodySelect = document.getElementById('certifying_body_id');
    const certSelect = document.getElementById('certification_id');

    function loadCertifications(bodyId, selectedCertId = null) {
        if (!bodyId) {
            certSelect.innerHTML = '<option value="">Select Certification</option>';
            return;
        }

        certSelect.innerHTML = '<option value="">Loading...</option>';

        fetch(`<?= base_url('applicant/certification/by-body') ?>/${bodyId}`)
            .then(res => res.json())
            .then(data => {
                let options = '<option value="">Select Certification</option>';
                data.forEach(cert => {
                    options += `<option value="${cert.id}" ${cert.id == selectedCertId ? 'selected' : ''}>${cert.name}</option>`;
                });
                certSelect.innerHTML = options;
            })
            .catch(() => {
                certSelect.innerHTML = '<option value="">Error loading certifications</option>';
            });
    }

    bodySelect.addEventListener('change', () => loadCertifications(bodySelect.value));

    <?php if(isset($certification) && isset($certification['certifying_body_id'])): ?>
        loadCertifications(<?= $certification['certifying_body_id'] ?>, <?= $certification['certification_id'] ?? 'null' ?>);
    <?php endif; ?>
});
</script>

<?= $this->endSection() ?>

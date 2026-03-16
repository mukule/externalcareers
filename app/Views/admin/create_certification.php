<?= $this->extend('layouts/dashboard') ?>
<?= $this->section('content') ?>

<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mt-4">
        <h1 class="mb-0"><?= isset($certification) ? 'Edit Certification' : 'Add New Certification' ?></h1>
        <a href="<?= base_url('admin/certifications') ?>" class="btn btn-outline-primary">
            <i class="fas fa-arrow-left me-1"></i> Back
        </a>
    </div>

    <div class="card shadow-sm mt-3">
        <div class="card-body">
            <form action="<?= $action ?>" method="post">
                <?= csrf_field() ?>

                <?php if (isset($certification)): ?>
                    <input type="hidden" name="id" value="<?= esc($certification['id']) ?>">
                <?php endif; ?>

                <div class="row">
                    <!-- Certification Name -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Certification Name <span class="text-danger">*</span></label>
                        <input type="text"
                               name="name"
                               value="<?= esc($certification['name'] ?? old('name')) ?>"
                               class="form-control"
                               placeholder="Enter certification name"
                               required>
                    </div>

                    <!-- Certifying Body Dropdown -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Certifying Body <span class="text-danger">*</span></label>
                        <select name="certifying_body_id" class="form-select" required>
                            <option value="">Select Certifying Body</option>
                            <?php foreach ($certifyingBodies as $body): ?>
                                <option value="<?= esc($body['id']) ?>" 
                                    <?= isset($certification) && $certification['certifying_body_id'] == $body['id'] ? 'selected' : '' ?>>
                                    <?= esc($body['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Active Status -->
                    <div class="col-md-6 mb-3 d-flex align-items-center">
                        <div class="form-check mt-4">
                            <input class="form-check-input" type="checkbox" 
                                   name="active" 
                                   id="active" 
                                   value="1"
                                   <?= isset($certification) ? ($certification['active'] ? 'checked' : '') : 'checked' ?>>
                            <label class="form-check-label" for="active">
                                Active
                            </label>
                        </div>
                    </div>
                </div>

                <div class="text-end mt-4">
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="fas fa-save me-1"></i> <?= isset($certification) ? 'Update' : 'Submit' ?>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

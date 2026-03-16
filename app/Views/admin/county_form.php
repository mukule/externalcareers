<?= $this->extend('layouts/dashboard') ?>
<?= $this->section('content') ?>

<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mt-4">
        <h1 class="mb-0"><?= isset($county) ? 'Edit County' : 'Add New County' ?></h1>
        <a href="<?= base_url('admin/counties') ?>" class="btn btn-outline-primary">
            <i class="fas fa-arrow-left me-1"></i> Back
        </a>
    </div>

    <div class="card shadow-sm mt-3">
        <div class="card-body">
            <form action="<?= $action ?>" method="post">
                <?= csrf_field() ?>

                <?php if (isset($county)): ?>
                    <input type="hidden" name="id" value="<?= esc($county['id']) ?>">
                <?php endif; ?>

                <div class="row">
                    <!-- County Name -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label">County Name <span class="text-danger">*</span></label>
                        <input type="text"
                               name="name"
                               value="<?= esc($county['name'] ?? old('name')) ?>"
                               class="form-control"
                               placeholder="Enter county name"
                               required>
                    </div>

                    <!-- County Code -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label">County Code <span class="text-danger">*</span></label>
                        <input type="text"
                               name="code"
                               value="<?= esc($county['code'] ?? old('code')) ?>"
                               class="form-control"
                               placeholder="Enter county code"
                               required>
                    </div>
                </div>

                <!-- Active Checkbox -->
                <div class="form-check mb-3">
                    <input class="form-check-input"
                           type="checkbox"
                           name="active"
                           id="active"
                           value="1"
                           <?= (isset($county['active']) ? $county['active'] : 1) ? 'checked' : '' ?>>
                    <label class="form-check-label" for="active">
                        Active
                    </label>
                </div>

                <div class="text-end mt-4">
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="fas fa-save me-1"></i> <?= isset($county) ? 'Update' : 'Submit' ?>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->extend('layouts/dashboard') ?>
<?= $this->section('content') ?>

<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mt-4">
        <h1 class="mb-0"><?= isset($jobType) ? 'Edit Job Type' : 'Add New Job Type' ?></h1>
        <a href="<?= base_url('admin/job-types') ?>" class="btn btn-outline-primary">
            <i class="fas fa-arrow-left me-1"></i> Back
        </a>
    </div>

    <div class="card shadow-sm mt-3">
        <div class="card-body">
            <form action="<?= $action ?>" method="post">
                <?= csrf_field() ?>

                <?php if (isset($jobType)): ?>
                    <input type="hidden" name="id" value="<?= esc($jobType['id']) ?>">
                <?php endif; ?>

                <div class="row">
                    <!-- Job Type Name -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Job Type Name <span class="text-danger">*</span></label>
                        <input type="text"
                               name="name"
                               value="<?= esc($jobType['name'] ?? old('name')) ?>"
                               class="form-control"
                               placeholder="Enter job type name"
                               required>
                    </div>

                    <!-- Display Name -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Display Name</label>
                        <input type="text"
                               name="display_name"
                               value="<?= esc($jobType['display_name'] ?? old('display_name')) ?>"
                               class="form-control"
                               placeholder="Enter display name (optional)">
                    </div>
                </div>

                <!-- Active Checkbox -->
                <div class="form-check mb-3">
                    <input class="form-check-input"
                           type="checkbox"
                           name="active"
                           id="active"
                           value="1"
                           <?= (isset($jobType['active']) ? $jobType['active'] : 1) ? 'checked' : '' ?>>
                    <label class="form-check-label" for="active">
                        Active
                    </label>
                </div>

                <div class="text-end mt-4">
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="fas fa-save me-1"></i> <?= isset($jobType) ? 'Update' : 'Submit' ?>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->extend('layouts/dashboard') ?>
<?= $this->section('content') ?>

<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mt-4">
        <h1 class="mb-0"><?= isset($discipline) ? 'Edit Job Discipline' : 'Add New Job Discipline' ?></h1>
        <a href="<?= base_url('admin/job-disciplines') ?>" class="btn btn-outline-primary">
            <i class="fas fa-arrow-left me-1"></i> Back
        </a>
    </div>

    <div class="card shadow-sm mt-3">
        <div class="card-body">
            <form action="<?= $action ?>" method="post">
                <?= csrf_field() ?>

                <?php if (isset($discipline)): ?>
                    <input type="hidden" name="id" value="<?= esc($discipline['id']) ?>">
                <?php endif; ?>

                <div class="row">
                    <!-- Job Discipline Name -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Job Discipline Name <span class="text-danger">*</span></label>
                        <input type="text"
                               name="name"
                               value="<?= esc($discipline['name'] ?? old('name')) ?>"
                               class="form-control"
                               placeholder="Enter job discipline name"
                               required>
                    </div>

                    <!-- Active Status -->
                    <div class="col-md-6 mb-3 d-flex align-items-center">
                        <div class="form-check mt-4">
                            <input class="form-check-input" type="checkbox" 
                                   name="active" 
                                   id="active" 
                                   value="1"
                                   <?= isset($discipline) ? ($discipline['active'] ? 'checked' : '') : 'checked' ?>>
                            <label class="form-check-label" for="active">
                                Active
                            </label>
                        </div>
                    </div>
                </div>

                <div class="text-end mt-4">
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="fas fa-save me-1"></i> <?= isset($discipline) ? 'Update' : 'Submit' ?>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->extend('layouts/dashboard') ?>
<?= $this->section('content') ?>

<div class="container-fluid px-4 mb-4">
    <div class="d-flex justify-content-between align-items-center mt-4">
        <h4 class="mb-0"><?= isset($jobType) ? 'Edit Job Type' : 'Add New Job Type' ?></h4>
        <a href="<?= base_url('admin/job-types') ?>" class="btn btn-outline-primary">
            <i class="fas fa-arrow-left me-1"></i> Back
        </a>
    </div>

    <div class="card shadow-sm mt-3">
        <div class="card-body">
            <form action="<?= $action ?>" method="post" enctype="multipart/form-data">
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

                <div class="row">
                    <!-- Banner Upload -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Banner Image</label>
                        <input type="file"
                               name="banner"
                               id="bannerInput"
                               class="form-control"
                               accept="image/*">
                        <div class="mt-2">
                            <?php if (!empty($jobType['banner'])): ?>
                                <img id="bannerPreview" src="<?= base_url('uploads/job_types/' . $jobType['banner']) ?>" 
                                     alt="Banner" class="img-fluid" style="max-height:100px;">
                            <?php else: ?>
                                <img id="bannerPreview" src="#" alt="Banner Preview" class="img-fluid" style="max-height:100px; display:none;">
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Icon Upload -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Icon Image</label>
                        <input type="file"
                               name="icon"
                               id="iconInput"
                               class="form-control"
                               accept="image/*">
                        <div class="mt-2">
                            <?php if (!empty($jobType['icon'])): ?>
                                <img id="iconPreview" src="<?= base_url('uploads/job_types/' . $jobType['icon']) ?>" 
                                     alt="Icon" class="img-fluid" style="max-height:50px;">
                            <?php else: ?>
                                <img id="iconPreview" src="#" alt="Icon Preview" class="img-fluid" style="max-height:50px; display:none;">
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Description -->
                <div class="mb-3">
                    <label class="form-label">Description</label>
                    <textarea name="description" 
                              class="form-control" 
                              rows="4" 
                              placeholder="Enter job type description"><?= esc($jobType['description'] ?? old('description')) ?></textarea>
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

<!-- Image Preview Script -->
<script>
    const bannerInput = document.getElementById('bannerInput');
    const bannerPreview = document.getElementById('bannerPreview');

    bannerInput.addEventListener('change', function() {
        const file = this.files[0];
        if (file) {
            bannerPreview.src = URL.createObjectURL(file);
            bannerPreview.style.display = 'block';
        }
    });

    const iconInput = document.getElementById('iconInput');
    const iconPreview = document.getElementById('iconPreview');

    iconInput.addEventListener('change', function() {
        const file = this.files[0];
        if (file) {
            iconPreview.src = URL.createObjectURL(file);
            iconPreview.style.display = 'block';
        }
    });
</script>

<?= $this->endSection() ?>
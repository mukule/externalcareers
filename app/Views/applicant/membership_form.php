<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="container py-5">

    <?= $this->include('partials/prof_nav', ['currentStep' => 4]) ?>

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="card-title mb-0"><?= isset($membership) ? 'Edit Membership' : 'Add New Membership' ?></h4>
            </div>

           

            <form action="<?= $action ?>" method="POST" enctype="multipart/form-data">
                <?= csrf_field() ?>
                <?php if(isset($membership)): ?>
                    <input type="hidden" name="id" value="<?= $membership['id'] ?>">
                <?php endif; ?>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="name" class="form-label">Membership Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="name" id="name" placeholder="Membership name" value="<?= old('name', $membership['name'] ?? '') ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label for="membership_no" class="form-label">Membership Number</label>
                        <input type="text" class="form-control" name="membership_no" id="membership_no" placeholder="Membership number" value="<?= old('membership_no', $membership['membership_no'] ?? '') ?>">
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="certifying_body_id" class="form-label">Certifying Body <span class="text-danger">*</span></label>
                        <select name="certifying_body_id" id="certifying_body_id" class="form-select" required>
                            <option value="">Select Body</option>
                            <?php if(!empty($bodies)): ?>
                                <?php foreach($bodies as $body): ?>
                                    <option value="<?= esc($body['id']) ?>" <?= old('certifying_body_id', $membership['certifying_body_id'] ?? '') == $body['id'] ? 'selected' : '' ?>>
                                        <?= esc($body['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label for="joined_date" class="form-label">Joined Year <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" name="joined_date" id="joined_date" placeholder="YYYY" min="1900" max="<?= date('Y') ?>" value="<?= old('joined_date', $membership['joined_date'] ?? '') ?>" required>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="certificate" class="form-label">Certificate (PDF, max 2MB)</label>
                        <input type="file" class="form-control" name="certificate" id="certificate" accept="application/pdf">
                        <?php if(isset($membership['certificate']) && $membership['certificate']): ?>
                            <small>Current: <a href="<?= base_url('uploads/certs/' . $membership['certificate']) ?>" target="_blank">View PDF</a></small>
                        <?php endif; ?>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary"><?= isset($membership) ? 'Update Membership' : 'Add Membership' ?></button>
                <a href="<?= base_url('applicant/membership') ?>" class="btn btn-secondary">Back to List</a>
            </form>

        </div>
    </div>

</div>

<?= $this->endSection() ?>
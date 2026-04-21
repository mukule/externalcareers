<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="container py-5">

    <?= $this->include('partials/prof_nav', ['currentStep' => 3]) ?>

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h4 class="card-title mb-4">
                <?= isset($edu) ? 'Edit Basic Education' : 'Add New Basic Education' ?>
            </h4>

            <form action="<?= $action ?>" method="POST" enctype="multipart/form-data">
                <?= csrf_field() ?>

                <?php if(isset($edu)): ?>
                    <input type="hidden" name="id" value="<?= $edu['id'] ?>">
                <?php endif; ?>

                <!-- School Name & Certification -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">School Name <span class="text-danger">*</span></label>
                        <input type="text"
                               class="form-control"
                               name="school_name"
                               placeholder="Enter school name"
                               value="<?= old('school_name', $edu['school_name'] ?? '') ?>"
                               required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Certification <span class="text-danger">*</span></label>
                        <select class="form-select" name="certification" required>
                            <option value="">Select Certification</option>
                            <?php foreach($certifications as $code => $name): ?>
                                <option value="<?= esc($code) ?>"
                                    <?= old('certification', $edu['certification'] ?? '') == $code ? 'selected' : '' ?>>
                                    <?= esc($name) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <!-- Dates & Grade -->
                <div class="row mb-3">
                    <div class="col-md-3">
                        <label class="form-label">Start Date</label>
                        <input type="date"
                               class="form-control"
                               name="date_started"
                               value="<?= old('date_started', $edu['date_started'] ?? '') ?>">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">End Date</label>
                        <input type="date"
                               class="form-control"
                               name="date_ended"
                               value="<?= old('date_ended', $edu['date_ended'] ?? '') ?>">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Grade</label>
                        <select class="form-select" name="grade_attained">
                            <option value="">Select Grade</option>
                            <?php foreach($grades as $g): ?>
                                <option value="<?= $g ?>"
                                    <?= old('grade_attained', $edu['grade_attained'] ?? '') == $g ? 'selected' : '' ?>>
                                    <?= $g ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <!-- Certificate Upload -->
                <div class="row mb-3">
                    <div class="col-md-12">
                        <label class="form-label">Certificate (PDF, max 2MB)</label>
                        <input type="file"
                               class="form-control"
                               name="certificate"
                               accept="application/pdf">

                        <?php if(isset($edu['certificate']) && $edu['certificate']): ?>
                            <small class="d-block mt-2">
                                Current:
                                <a href="<?= base_url('uploads/certs/' . $edu['certificate']) ?>" target="_blank">
                                    View File
                                </a>
                            </small>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Submit Buttons -->
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <?= isset($edu) ? 'Update' : 'Submit' ?>
                    </button>

                    <a href="<?= base_url('applicant/basic-education') ?>" class="btn btn-outline-primary">
                        Back to List
                    </a>
                </div>

            </form>

        </div>
    </div>

</div>

<?= $this->endSection() ?>
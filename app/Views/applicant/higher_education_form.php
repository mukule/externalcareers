<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="container py-5">

    <?= $this->include('partials/prof_nav', ['currentStep' => 3]) ?>

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h4 class="card-title mb-4">
                <?= isset($edu) ? 'Edit Higher Education' : 'Add New Higher Education' ?>
            </h4>

            <form action="<?= $action ?>" method="POST" enctype="multipart/form-data">
                <?= csrf_field() ?>

                <?php if(isset($edu)): ?>
                    <input type="hidden" name="id" value="<?= $edu['id'] ?>">
                <?php endif; ?>

                <!-- Institution & Course -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Institution <span class="text-danger">*</span></label>
                        <input type="text"
                               class="form-control"
                               name="institution_name"
                               placeholder="Enter institution name"
                               value="<?= old('institution_name', $edu['institution_name'] ?? '') ?>"
                               required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Course <span class="text-danger">*</span></label>
                        <input type="text"
                               class="form-control"
                               name="course_name"
                               placeholder="Enter course"
                               value="<?= old('course_name', $edu['course_name'] ?? '') ?>"
                               required>
                    </div>
                </div>

                <!-- Level of Study & Class Attained -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Certification <span class="text-danger">*</span></label>
                        <select class="form-select" name="education_level_id" required>
                            <option value="">Select Level</option>
                            <?php foreach($levels as $level): ?>
                                <option value="<?= esc($level['id']) ?>"
                                    <?= old('education_level_id', $edu['education_level_id'] ?? '') == $level['id'] ? 'selected' : '' ?>>
                                    <?= esc($level['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Class Attained</label>
                        <select class="form-select" name="class_attained">
                            <option value="">Select Class</option>
                            <?php 
                            $classes = ['First Class', 'Second Class Upper', 'Second Class Lower', 'Third Class', 'Pass'];
                            foreach($classes as $c): ?>
                                <option value="<?= $c ?>"
                                    <?= old('class_attained', $edu['class_attained'] ?? '') == $c ? 'selected' : '' ?>>
                                    <?= $c ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <!-- Dates -->
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

                    <a href="<?= base_url('applicant/higher-education') ?>" class="btn btn-outline-primary">
                        Back
                    </a>
                </div>

            </form>

        </div>
    </div>

</div>

<?= $this->endSection() ?>
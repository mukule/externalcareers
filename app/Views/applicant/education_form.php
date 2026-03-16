<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="container py-5">

    <?= $this->include('partials/prof_nav', ['currentStep' => 3]) ?>

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h4 class="card-title mb-4"><?= isset($edu) ? 'Edit Education' : 'Add New Education' ?></h4>

            <form action="<?= $action ?>" method="POST" enctype="multipart/form-data">
                <?= csrf_field() ?>
                <?php if(isset($edu)): ?>
                    <input type="hidden" name="id" value="<?= $edu['id'] ?>">
                <?php endif; ?>

                <!-- Education Level & Institution -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="level_id" class="form-label">Education Level</label>
                        <select class="form-select" name="level_id" id="level_id" required>
                            <option value="">Select Level</option>
                            <?php foreach($levels as $level): ?>
                                <option value="<?= $level['id'] ?>" <?= (isset($edu) && $edu['level_id']==$level['id']) ? 'selected' : '' ?>>
                                    <?= esc($level['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="institution" class="form-label">Institution</label>
                        <input type="text" class="form-control" name="institution" id="institution" placeholder="Institution name" value="<?= $edu['institution'] ?? '' ?>" required>
                    </div>
                </div>

                <!-- Certifications (required) & Field of Study (optional) -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="course" class="form-label">Certification <span class="text-danger">*</span></label>
                        <select class="form-select" name="course" id="course" required>
                            <option value="">Select Certification</option>
                            <?php foreach($certifications as $system => $certList): ?>
                                <optgroup label="<?= esc($system) ?>">
                                    <?php foreach($certList as $cert): ?>
                                        <option value="<?= esc($cert) ?>" <?= (isset($edu) && $edu['course']==$cert) ? 'selected' : '' ?>>
                                            <?= esc($cert) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </optgroup>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="field_of_study" class="form-label">Field of Study</label>
                       <select class="form-select"  name="field_id" id="field_of_study">
                            <option value="">Select Field</option>
                            <?php foreach($fieldsOfStudy as $field): ?>
                                <option value="<?= $field['id'] ?>" <?= (isset($edu) && ($edu['field_id'] ?? '') == $field['id']) ? 'selected' : '' ?>>
                                    <?= esc($field['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>

                    </div>
                </div>

                <!-- Years & Grade -->
                <div class="row mb-3">
                    <div class="col-md-3">
                        <label for="start_year" class="form-label">Start Year</label>
                        <input type="text" class="form-control yearpicker" name="start_year" id="start_year" placeholder="YYYY" autocomplete="off" value="<?= $edu['start_year'] ?? '' ?>">
                    </div>
                    <div class="col-md-3">
                        <label for="end_year" class="form-label">End Year</label>
                        <input type="text" class="form-control yearpicker" name="end_year" id="end_year" placeholder="YYYY" autocomplete="off" value="<?= $edu['end_year'] ?? '' ?>">
                    </div>
                    <div class="col-md-6">
                        <label for="grade" class="form-label">Grade/Score</label>
                        <input type="text" class="form-control" name="grade" id="grade" placeholder="Grade or score" value="<?= $edu['grade'] ?? '' ?>">
                    </div>
                </div>

                <!-- Certificate Upload -->
                <div class="row mb-3">
                    <div class="col-md-12">
                        <label for="certificate" class="form-label">Certificate (PDF, max 2MB)</label>
                        <input type="file" class="form-control" name="certificate" id="certificate" accept="application/pdf">
                        <?php if(isset($edu['certificate']) && $edu['certificate']): ?>
                            <small>Current: <a href="<?= base_url('uploads/certificates/' . $edu['certificate']) ?>" target="_blank">View PDF</a></small>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Submit Buttons -->
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary"><?= isset($edu) ? 'Update Education' : 'Add Education' ?></button>
                    <a href="<?= base_url('applicant/education') ?>" class="btn btn-secondary">Back to List</a>
                </div>
            </form>

        </div>
    </div>

</div>

<?= $this->endSection() ?>

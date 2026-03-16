<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="container py-5">

    <?= $this->include('partials/prof_nav', ['currentStep' => 4]) ?>

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="card-title mb-0"><?= isset($experience) ? 'Edit Work Experience' : 'Add New Work Experience' ?></h4>
            </div>

            <?php if(session()->getFlashdata('error')): ?>
                <div class="alert alert-danger">
                    <?= is_array(session()->getFlashdata('error')) ? implode('<br>', session()->getFlashdata('error')) : session()->getFlashdata('error') ?>
                </div>
            <?php endif; ?>

            <form action="<?= $action ?>" method="POST" enctype="multipart/form-data">
                <?= csrf_field() ?>
                <?php if(isset($experience)): ?>
                    <input type="hidden" name="id" value="<?= $experience['id'] ?>">
                <?php endif; ?>

                <div class="row mb-3">
                    <!-- Company Name -->
                    <div class="col-md-6">
                        <label for="company_name" class="form-label">Company Name</label>
                        <input type="text" class="form-control" name="company_name" id="company_name" placeholder="Enter company name" value="<?= $experience['company_name'] ?? '' ?>" required>
                    </div>

                    <!-- Position -->
                    <div class="col-md-6">
                        <label for="position" class="form-label">Position</label>
                        <input type="text" class="form-control" name="position" id="position" placeholder="Enter your position" value="<?= $experience['position'] ?? '' ?>" required>
                    </div>
                </div>

                <div class="row mb-3">
                    <!-- Start Date -->
                    <div class="col-md-6">
                        <label for="start_date" class="form-label">Date Started</label>
                        <input type="date" class="form-control" name="start_date" id="start_date" placeholder="YYYY-MM-DD" value="<?= $experience['start_date'] ?? '' ?>" required>
                    </div>

                    <!-- End Date -->
                    <div class="col-md-6">
                        <label for="end_date" class="form-label">Last Day (Optional)</label>
                        <input type="date" class="form-control" name="end_date" id="end_date" placeholder="YYYY-MM-DD" value="<?= $experience['end_date'] ?? '' ?>">
                    </div>
                </div>

                <!-- Responsibilities / Job Description -->
                <div class="mb-3">
                    <label for="responsibilities" class="form-label">Responsibilities / Job Description</label>
                    <textarea class="form-control" name="responsibilities" id="description" rows="5" placeholder="Describe your key responsibilities, projects, or achievements"><?= $experience['responsibilities'] ?? '' ?></textarea>
                </div>

                <!-- Reference Letter Upload -->
                <div class="mb-3">
                    <label for="reference_letter" class="form-label">Reference Letter (PDF, max 2MB)</label>
                    <input type="file" class="form-control" name="reference_letter" id="reference_letter" accept="application/pdf">
                    <?php if(isset($experience['reference_letter']) && $experience['reference_letter']): ?>
                        <small>Current: <a href="<?= base_url('uploads/work_experience/' . $experience['reference_letter']) ?>" target="_blank">View PDF</a></small>
                    <?php endif; ?>
                </div>

                <!-- Currently Working Here -->
                <div class="form-check mb-3">
                    <input type="checkbox" class="form-check-input" id="currently_working" name="currently_working" value="1" <?= isset($experience['currently_working']) && $experience['currently_working'] ? 'checked' : '' ?>>
                    <label class="form-check-label" for="currently_working">I am currently working here</label>
                </div>

                <button type="submit" class="btn btn-primary"><?= isset($experience) ? 'Update Experience' : 'Add Experience' ?></button>
                <a href="<?= base_url('applicant/work-experience') ?>" class="btn btn-secondary">Back to List</a>
            </form>
        </div>
    </div>

</div>

<?= $this->endSection() ?>

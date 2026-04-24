<?= $this->extend('layouts/dashboard') ?>
<?= $this->section('content') ?>

<div class="container-fluid px-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mt-4">
        <h4 class="mb-0">
            <?= isset($job) ? 'Edit Job Vacancy' : 'Add New Job Vacancy' ?>
        </h4>
        <a href="<?= base_url('admin/jobs') ?>" class="btn btn-outline-primary">
            <i class="fas fa-arrow-left me-1"></i> Back
        </a>
    </div>

    <!-- Form Card -->
    <div class="card border-0 mt-3 mb-3 p-5">
        <div class="card-body">
            <form action="<?= $action ?>" method="post">
                <?= csrf_field() ?>
                
                <?php if (isset($job)): ?>
                    <input type="hidden" name="id" value="<?= esc($job['id']) ?>">
                <?php endif; ?>

                <div class="row g-2">

                    <!-- Job Title -->
                    <div class="col-md-4 text-start d-flex flex-column justify-content-center">
                        <label class="form-label fw-semibold">Job Title <span class="text-danger">*</span></label>
                    </div>
                    <div class="col-md-8">
                        <input type="text" name="name" class="form-control"
                               value="<?= esc($job['name'] ?? old('name')) ?>"
                               placeholder="Enter job title" required>
                    </div>

                    <!-- Reference Number -->
                    <div class="col-md-4 text-start d-flex flex-column justify-content-center">
                        <label class="form-label fw-semibold">Reference Number <span class="text-danger">*</span></label>
                    </div>
                    <div class="col-md-8">
                        <input type="text" name="reference_no" class="form-control"
                               value="<?= esc($job['reference_no'] ?? old('reference_no')) ?>"
                               placeholder="Enter reference number" required>
                    </div>

                    <!-- Job Type -->
                    <div class="col-md-4 text-start d-flex flex-column justify-content-center">
                        <label class="form-label fw-semibold">Job Type <span class="text-danger">*</span></label>
                    </div>
                    <div class="col-md-8">
                        <select name="job_type_id" class="form-select" required>
                            <option value="">Select Job Type</option>
                            <?php foreach ($jobTypes as $type): ?>
                                <option value="<?= esc($type['id']) ?>"
                                    <?= isset($job) && $job['job_type_id'] == $type['id'] ? 'selected' : '' ?>>
                                    <?= esc($type['display_name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Discipline -->
                    <div class="col-md-4 text-start d-flex flex-column justify-content-center">
                        <label class="form-label fw-semibold">Discipline <span class="text-danger">*</span></label>
                    </div>
                    <div class="col-md-8">
                        <select name="discipline_id" class="form-select" required>
                            <option value="">Select Discipline</option>
                            <?php foreach ($jobDisciplines as $discipline): ?>
                                <option value="<?= esc($discipline['id']) ?>"
                                    <?= isset($job) && $job['discipline_id'] == $discipline['id'] ? 'selected' : '' ?>>
                                    <?= esc($discipline['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Posts Needed -->
                    <div class="col-md-4 text-start d-flex flex-column justify-content-center">
                        <label class="form-label fw-semibold">Number of Posts <span class="text-danger">*</span></label>
                    </div>
                    <div class="col-md-8">
                        <input type="number" name="posts_needed" class="form-control"
                               value="<?= esc($job['posts_needed'] ?? old('posts_needed')) ?>" 
                               min="1" required>
                    </div>

                    <!-- Reports To -->
                    <div class="col-md-4 text-start d-flex flex-column justify-content-center">
                        <label class="form-label fw-semibold">Reports To</label>
                    </div>
                    <div class="col-md-8">
                        <input type="text" name="reports_to" class="form-control"
                               value="<?= esc($job['reports_to'] ?? old('reports_to')) ?>"
                               placeholder="e.g. Director of HR">
                    </div>

                    <!-- Minimum Education Level -->
                    <div class="col-md-4 text-start d-flex flex-column justify-content-center">
                        <label class="form-label fw-semibold">Minimum Education Level <span class="text-danger">*</span></label>
                    </div>
                    <div class="col-md-8">
                        <select name="min_education_level_id" class="form-select" required>
                            <option value="">Select Education Level</option>
                            <?php foreach ($educationLevels as $level): ?>
                                <option value="<?= esc($level['id']) ?>"
                                    <?= isset($job) && $job['min_education_level_id'] == $level['id'] ? 'selected' : '' ?>>
                                    <?= esc($level['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Work Experience Years -->
                    <div class="col-md-4 text-start d-flex flex-column justify-content-center">
                        <label class="form-label fw-semibold">Work Experience (Years) <span class="text-danger">*</span></label>
                    </div>
                    <div class="col-md-8">
                        <input type="number" name="work_experience_years" class="form-control"
                               value="<?= esc($job['work_experience_years'] ?? old('work_experience_years')) ?>" 
                               min="0" step="0.5" required>
                    </div>

                    <!-- Date Open -->
                    <div class="col-md-4 text-start d-flex flex-column justify-content-center">
                        <label class="form-label fw-semibold">Date Open <span class="text-danger">*</span></label>
                    </div>
                    <div class="col-md-8">
                        <input type="datetime-local" name="date_open" class="form-control"
                               value="<?= isset($job['date_open']) ? date('Y-m-d\TH:i', strtotime($job['date_open'])) : old('date_open') ?>"
                               required>
                    </div>

                    <!-- Date Close -->
                    <div class="col-md-4 text-start d-flex flex-column justify-content-center">
                        <label class="form-label fw-semibold">Date Close <span class="text-danger">*</span></label>
                    </div>
                    <div class="col-md-8">
                        <input type="datetime-local" name="date_close" class="form-control"
                               value="<?= isset($job['date_close']) ? date('Y-m-d\TH:i', strtotime($job['date_close'])) : old('date_close') ?>"
                               required>
                    </div>

                    <!-- Job Summary -->
                    <div class="col-md-4 text-start d-flex flex-column justify-content-center">
                        <label class="form-label fw-semibold">Job Summary <span class="text-danger">*</span></label>
                    </div>
                    <div class="col-md-8">
                        <textarea name="job_summary" class="form-control" id="summary" rows="3" 
                                  placeholder="Brief overview of the job role"
                                  required><?= esc($job['job_summary'] ?? old('job_summary')) ?></textarea>
                    </div>

                    <!-- Job Description -->
                    <div class="col-md-4 text-start d-flex flex-column justify-content-center">
                        <label class="form-label fw-semibold">Job Description <span class="text-danger">*</span></label>
                    </div>
                    <div class="col-md-8">
                        <textarea name="job_description" id="description" class="form-control" rows="6" 
                                  placeholder="Detailed description of responsibilities and requirements"
                                  required><?= esc($job['job_description'] ?? old('job_description')) ?></textarea>
                    </div>

                    <!-- Optional Requirements -->
                    <div class="col-md-4 text-start d-flex flex-column justify-content-center">
                        <label class="form-label fw-semibold">Additional Requirements</label>
                    </div>
                    <div class="col-md-8">
                        <div class="border p-3 rounded">
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" name="certification_required" value="1"
                                    <?= isset($job) && $job['certification_required'] ? 'checked' : '' ?>>
                                <label class="form-check-label">Certification Required</label>
                            </div>

                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" name="membership_required" value="1"
                                    <?= isset($job) && $job['membership_required'] ? 'checked' : '' ?>>
                                <label class="form-check-label">Membership Required</label>
                            </div>

                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" name="higher_education_required" value="1"
                                    <?= isset($job) && $job['higher_education_required'] ? 'checked' : '' ?>>
                                <label class="form-check-label">Higher Education Required (College/University)</label>
                            </div>
                        </div>
                    </div>

                    <!-- Fields of Study -->
                   

                </div>
               
                <div class="text-start mt-4">
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="fas fa-save me-1"></i>
                        <?= isset($job) ? 'Update Job' : 'Create Job' ?>
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
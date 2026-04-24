<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<style>
    .resume-container { 
        margin: auto; 
        background: #fff; 
        padding: 30px; 
        border-radius: 8px; 
        max-width: 100%; 
    }
    .resume-header { text-align: center; margin-bottom: 30px; }
    .resume-header h1 { margin: 0; font-size: 28px; }
    .resume-header p { margin: 0; color: #555; }

    .section-title { 
        font-size: 20px; 
        margin-top: 30px; 
        border-bottom: 1px solid #ddd; 
        padding-bottom: 5px; 
    }
    .section-content { margin-top: 15px; }

    table.table {
        font-size: 14px;
        margin-bottom: 10px !important;
    }
    table.table th, table.table td {
        padding: 6px 10px !important;
        vertical-align: middle;
    }
    @media (max-width: 768px) {
        table.table { font-size: 13px; }
        .resume-container { padding: 15px; }
    }
</style>

<div class="container py-5">
    <?= $this->include('partials/prof_nav', ['currentStep' => 9]) ?>

    <div class="resume-container" style="margin-bottom: 50px;">

        <!-- Header -->
        <div class="resume-header">
            <h1><?= esc($title) ?></h1>
            <?php if (!empty($details['highest_edu_level']) || !empty($details['study_field'])): ?>
                <p>
                    <?= esc($details['highest_edu_level'] ?? '') ?>
                    <?php if (!empty($details['study_field'])): ?>
                        - <?= esc($details['study_field']) ?>
                    <?php endif; ?>
                </p>
            <?php endif; ?>
        </div>

        <!-- Bio Data -->
        <?php if (!empty($details)): ?>
        <div class="section">
            <h2 class="section-title">Bio Data</h2>
            <div class="section-content">
                <div class="row g-2">
                    <div class="col-md-6"><strong>Full Name:</strong> <?= esc(trim(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? ''))) ?></div>
                    <div class="col-md-6"><strong>Email:</strong> <?= esc($user['email'] ?? '') ?></div>
                    <div class="col-md-6"><strong>National ID:</strong> <?= esc($details['national_id'] ?? '') ?></div>
                    <div class="col-md-6"><strong>Gender:</strong> <?= esc($details['gender_name'] ?? '') ?></div>
                    <div class="col-md-6"><strong>Date of Birth:</strong> <?= esc($details['dob'] ?? '') ?></div>
                    <div class="col-md-6"><strong>Phone:</strong> <?= esc($details['phone'] ?? '') ?></div>
                    <div class="col-md-6"><strong>Ethnicity:</strong> <?= esc($details['ethnicity_name'] ?? '') ?></div>
                    <div class="col-md-6"><strong>Country of Birth:</strong> <?= esc($details['country_of_birth_name'] ?? '') ?></div>
                    <div class="col-md-6"><strong>Country of Residence:</strong> <?= esc($details['country_of_residence_name'] ?? '') ?></div>
                    <div class="col-md-6"><strong>Permanent Address:</strong> <?= esc($details['county_of_origin_name'] ?? '') ?></div>
                    <div class="col-md-6"><strong>Temporary Address:</strong> <?= esc($details['county_of_residence_name'] ?? '') ?></div>
                    <div class="col-md-6"><strong>Disability:</strong> <?= ($details['disability_status'] ?? 0) == 1 ? 'Yes' : 'No' ?></div>
                    <?php if (($details['disability_status'] ?? 0) == 1): ?>
                        <div class="col-md-6"><strong>NCPWD No.:</strong> <?= esc($details['disability_number'] ?? '') ?></div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Academic Summary -->
        <?php if (!empty($details['highest_edu_level']) || !empty($details['study_field'])): ?>
        <div class="section">
            <h2 class="section-title">Academic Summary</h2>
            <div class="section-content">
                <div class="row g-2">
                    <div class="col-md-6">
                        <strong>Highest Education Level:</strong> <?= esc($details['highest_edu_level'] ?? 'N/A') ?>
                    </div>
                    
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Professional Statement -->
        <?php if (!empty($statement['statement'])): ?>
        <div class="section">
            <h2 class="section-title">Professional Statement</h2>
            <div class="section-content">
                <p><?= ($statement['statement']) ?></p>
            </div>
        </div>
        <?php endif; ?>

        <!-- Education -->
        <?php if (!empty($basicEducation) || !empty($higherEducation)): ?>
        <div class="section">
            <h2 class="section-title">Education</h2>

            <!-- Basic Education -->
            <?php if (!empty($basicEducation)): ?>
            <div class="section-content table-responsive">
                <h6 class="mb-2">Basic Education</h6>
                <table class="table table-bordered table-striped">
                    <thead class="table-light">
                        <tr>
                            <th>School</th>
                            <th>Certification</th>
                            <th>Grade</th>
                            <th>Years</th>
                            <th>Certificate</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($basicEducation as $edu): ?>
                        <tr>
                            <td><?= esc($edu['school_name']) ?></td>
                            <td><?= esc($edu['certification'] ?? '') ?></td>
                            <td><?= esc($edu['grade_attained'] ?? '') ?></td>
                           <td>
                                <?= !empty($edu['date_started']) ? date('Y', strtotime($edu['date_started'])) : '' ?>
                                -
                                <?= !empty($edu['date_ended']) ? date('Y', strtotime($edu['date_ended'])) : 'Present' ?>
                            </td>
                            <td>
                                <?php if (!empty($edu['certificate'])): ?>
                                <a href="<?= base_url('uploads/certs/' . $edu['certificate']) ?>" target="_blank" class="btn btn-sm btn-primary">View</a>
                                <?php else: ?><em>N/A</em><?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>

            <!-- Higher Education -->
            <?php if (!empty($higherEducation)): ?>
            <div class="section-content table-responsive mt-3">
                <h6 class="mb-2">College/University</h6>
                <table class="table table-bordered table-striped">
                    <thead class="table-light">
                        <tr>
                            <th>Institution</th>
                            <th>Certification</th>
                            <th>Course</th>
                            <th>Years</th>
                            <th>Certificate</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($higherEducation as $edu): ?>
                        <tr>
                            <td><?= esc($edu['institution_name']) ?></td>
                            <td><?= esc($edu['level_name']) ?></td>
                            <td><?= esc($edu['course_name']) ?></td>
                           <td>
                                <?= !empty($edu['date_started']) ? date('Y', strtotime($edu['date_started'])) : '' ?>
                                -
                                <?= !empty($edu['date_ended']) ? date('Y', strtotime($edu['date_ended'])) : 'Present' ?>
                            </td>
                            <td>
                                <?php if (!empty($edu['certificate'])): ?>
                                <a href="<?= base_url('uploads/certs/' . $edu['certificate']) ?>" target="_blank" class="btn btn-sm btn-primary">View</a>
                                <?php else: ?><em>N/A</em><?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>

        </div>
        <?php endif; ?>

        <!-- Certifications -->
        <?php if (!empty($certifications)): ?>
        <div class="section">
            <h2 class="section-title">Professional Certificates</h2>
            <div class="section-content table-responsive">
                <table class="table table-bordered table-striped">
                    <thead class="table-light">
                        <tr>
                            <th>Certification Name</th>
                            <th>Issuing Body</th>
                            <th>Attained Date</th>
                            <th>Certificate</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($certifications as $cert): ?>
                        <tr>
                            <td><?= esc($cert['cert_name'] ?? '') ?></td>
                            <td><?= esc($cert['issuing_body'] ?? '') ?></td>
                            <td><?= esc($cert['attained_date'] ?? '') ?></td>
                            <td>
                                <?php if (!empty($cert['certificate_url'])): ?>
                                <a href="<?= esc($cert['certificate_url']) ?>" target="_blank" class="btn btn-sm btn-primary">View</a>
                                <?php else: ?><em>N/A</em><?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php endif; ?>

        <!-- Professional Memberships -->
        <?php if (!empty($memberships)): ?>
        <div class="section">
            <h2 class="section-title">Professional Memberships</h2>
            <div class="section-content table-responsive">
                <table class="table table-bordered table-striped">
                    <thead class="table-light">
                        <tr>
                            <th>Membership Name</th>
                            <th>Certifying Body</th>
                            <th>Membership No</th>
                            <th>Attained Date</th>
                            <th>Certificate</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($memberships as $mem): ?>
                        <tr>
                            <td><?= esc($mem['name'] ?? '-') ?></td>
                            <td><?= esc($mem['body_name'] ?? '-') ?></td>
                            <td><?= esc($mem['membership_no'] ?? '-') ?></td>
                            <td><?= esc($mem['joined_date'] ?? '-') ?></td>
                            <td>
                                <?php if (!empty($mem['certificate'])): ?>
                                    <a href="<?= base_url('uploads/certs/' . $mem['certificate']) ?>" target="_blank" class="btn btn-sm btn-primary">View</a>
                                <?php else: ?><em>N/A</em><?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php endif; ?>

        <!-- Work Experience -->
        <?php if (!empty($workExperience)): ?>
        <div class="section">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <h2 class="section-title mb-0">Work Experience</h2>
                <?php if (!empty($totalExperience)): ?>
                    <span class="badge bg-primary p-2"><?= esc($totalExperience) ?></span>
                <?php endif; ?>
            </div>
            <hr>

            <div class="section-content table-responsive">
                <table class="table table-bordered table-striped">
                    <thead class="table-light">
                        <tr>
                            <th>Company</th>
                            <th>Position</th>
                            <th>Period</th>
                            <th>Responsibilities</th>
                            <th>Reference File</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($workExperience as $index => $work): ?>
                        <tr>
                            <td><?= esc($work['company_name']) ?></td>
                            <td><?= esc($work['position']) ?></td>
                            <td><?= esc($work['start_date']) ?> - <?= $work['currently_working'] == 1 ? 'Present' : esc($work['end_date']) ?></td>
                            <td>
                                <button class="btn btn-sm btn-outline-primary" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#responsibilityModal<?= $index ?>">
                                    View
                                </button>

                                <div class="modal fade" id="responsibilityModal<?= $index ?>" tabindex="-1">
                                    <div class="modal-dialog modal-lg">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title"><?= esc($work['position']) ?> - <?= esc($work['company_name']) ?></h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <?= nl2br(esc($work['responsibilities'])) ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <?php if (!empty($work['reference_file'])): ?>
                                <a href="<?= base_url('uploads/certs/' . $work['reference_file']) ?>" target="_blank" class="btn btn-sm btn-primary">View</a>
                                <?php else: ?><em>N/A</em><?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php endif; ?>

        <!-- Referees -->
        <?php if (!empty($referees)): ?>
        <div class="section">
            <h2 class="section-title">Referees</h2>
            <div class="section-content table-responsive">
                <table class="table table-bordered table-striped">
                    <thead class="table-light">
                        <tr>
                            <th>Name</th>
                            <th>Position</th>
                            <th>Organization</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Relationship</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($referees as $ref): ?>
                        <tr>
                            <td><?= esc($ref['name']) ?></td>
                            <td><?= esc($ref['position']) ?></td>
                            <td><?= esc($ref['organization']) ?></td>
                            <td><?= esc($ref['email']) ?></td>
                            <td><?= esc($ref['phone']) ?></td>
                            <td><?= esc($ref['relationship']) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php endif; ?>

    </div>
</div>

<?= $this->endSection() ?>
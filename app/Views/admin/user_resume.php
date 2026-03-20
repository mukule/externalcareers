<?= $this->extend('layouts/dashboard') ?>
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

<div class="container mt-3">

 <div class="row mb-3">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb bg-white rounded shadow-sm p-3 mb-0">
                    <li class="breadcrumb-item">
                        <a href="<?= base_url('admin') ?>">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item active">
                        <?= esc(trim($resume['first_name'] . ' ' . $resume['last_name'])) ?> Resume
                    </li>
                </ol>
            </nav>
        </div>
    </div>

   
    <div class="resume-container">

        
        <!-- Bio Data -->
        <?php if (!empty($details)): ?>
        <div class="section">
            <h2 class="section-title">Bio Data</h2>
            <div class="section-content">
                <div class="row g-2">
                    <div class="col-md-6"><strong>Full Name:</strong> <?= esc(trim(($resume['first_name'] ?? '') . ' ' . ($resume['last_name'] ?? ''))) ?></div>
                    <div class="col-md-6"><strong>Email:</strong> <?= esc($resume['email'] ?? '') ?></div>
                    <div class="col-md-6"><strong>Phone:</strong> <?= esc($details['phone'] ?? '') ?></div>
                    <div class="col-md-6"><strong>Gender:</strong> <?= esc($details['gender_name'] ?? '') ?></div>
                    <div class="col-md-6"><strong>Date of Birth:</strong> <?= esc($details['dob'] ?? '') ?></div>
                    <div class="col-md-6"><strong>Nationality:</strong> <?= esc($details['nationality'] ?? '') ?></div>
                    <div class="col-md-6"><strong>National ID:</strong> <?= esc($details['national_id'] ?? '') ?></div>
                    <div class="col-md-6"><strong>Ethnicity:</strong> <?= esc($details['ethnicity_name'] ?? '') ?></div>
                    <div class="col-md-6"><strong>Marital Status:</strong> <?= esc($details['marital_status_name'] ?? 'N/A') ?></div>
                    <div class="col-md-6"><strong>Country of Birth:</strong> <?= esc($details['country_of_birth_name'] ?? '') ?></div>
                    <div class="col-md-6"><strong>Country of Residence:</strong> <?= esc($details['country_of_residence_name'] ?? '') ?></div>
                    <div class="col-md-6"><strong>County of Origin:</strong> <?= esc($details['county_of_origin_name'] ?? '') ?></div>
                    <div class="col-md-6"><strong>County of Residence:</strong> <?= esc($details['county_of_residence_name'] ?? '') ?></div>
                    <div class="col-md-6"><strong>Disability:</strong> <?= ($details['disability_status'] ?? 0) == 1 ? 'Yes' : 'No' ?></div>
                    <?php if (($details['disability_status'] ?? 0) == 1): ?>
                        <div class="col-md-6"><strong>Disability Number:</strong> <?= esc($details['disability_number'] ?? '') ?></div>
                        <div class="col-md-6"><strong>Disability Type:</strong> <?= esc($details['disability_type'] ?? '') ?></div>
                    <?php endif; ?>
                    <div class="col-md-6"><strong>Status:</strong> <?= !empty($applicant['active']) ? 'Active' : 'Inactive' ?></div>
                    <div class="col-md-6"><strong>Registered:</strong> <?= !empty($applicant['created_at']) ? date('d M Y, H:i', strtotime($applicant['created_at'])) : '-' ?></div>
                    <div class="col-md-6"><strong>Last Login:</strong> <?= !empty($applicant['last_login']) ? date('d M Y, H:i', strtotime($applicant['last_login'])) : '-' ?></div>
                      <div class="col-md-6"><strong>Highest Education Level:</strong> <?= esc($details['highest_edu_level'] ?? 'N/A') ?></div>
                <div class="col-md-6"><strong>Specialization:</strong> <?= esc($details['study_field'] ?? '-') ?></div>
                </div>
            </div>
        </div>
        <?php endif; ?>

       
        <!-- Professional Statement -->
        <?php if (!empty($statement['statement'])): ?>
        <div class="section">
            <h2 class="section-title">Professional Statement</h2>
            <div class="section-content">
                <p><?= nl2br(($statement['statement'])) ?></p>
            </div>
        </div>
        <?php endif; ?>

        <!-- Education Tables -->
        <?php if (!empty($basicEducation) || !empty($higherEducation)): ?>
        <div class="section">
            <h2 class="section-title">Education</h2>

            <?php if (!empty($basicEducation)): ?>
            <div class="section-content table-responsive">
                <h6>Basic Education</h6>
                <table class="table table-bordered table-striped">
                    <thead>
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
                            <td><?= esc($edu['date_started'] ?? '') ?> - <?= esc($edu['date_ended'] ?? '') ?></td>
                            <td>
                                <?php if (!empty($edu['certificate'])): ?>
                                    <a href="<?= base_url('uploads/certificates/' . $edu['certificate']) ?>" target="_blank" class="btn btn-sm btn-primary">View</a>
                                <?php else: ?><em>N/A</em><?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>

            <?php if (!empty($higherEducation)): ?>
            <div class="section-content table-responsive mt-3">
                <h6>Higher Education</h6>
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Institution</th>
                            <th>Level</th>
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
                            <td><?= esc($edu['date_started']) ?> - <?= esc($edu['date_ended']) ?></td>
                            <td>
                                <?php if (!empty($edu['certificate'])): ?>
                                    <a href="<?= base_url('uploads/certificates/' . $edu['certificate']) ?>" target="_blank" class="btn btn-sm btn-primary">View</a>
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
                    <thead>
                        <tr>
                            <th>Name</th>
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

        <!-- Work Experience -->
        <?php if (!empty($workExperience)): ?>
        <div class="section">
            <h2 class="section-title">Work Experience <?php if (!empty($totalExperience)): ?><span class="badge bg-primary"><?= esc($totalExperience) ?></span><?php endif; ?></h2>
            <div class="section-content table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Company</th>
                            <th>Position</th>
                            <th>Period</th>
                            <th>Responsibilities</th>
                            <th>Reference</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($workExperience as $index => $work): ?>
                        <tr>
                            <td><?= esc($work['company_name']) ?></td>
                            <td><?= esc($work['position']) ?></td>
                            <td><?= esc($work['start_date']) ?> - <?= $work['currently_working'] == 1 ? 'Present' : esc($work['end_date']) ?></td>
                            <td>
                                <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#workModal<?= $index ?>">View</button>
                                <div class="modal fade" id="workModal<?= $index ?>" tabindex="-1">
                                    <div class="modal-dialog modal-lg">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title"><?= esc($work['position']) ?> - <?= esc($work['company_name']) ?></h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body"><?= nl2br(esc($work['responsibilities'])) ?></div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <?php if (!empty($work['reference_file'])): ?>
                                    <a href="<?= base_url('uploads/work_experience/' . $work['reference_file']) ?>" target="_blank" class="btn btn-sm btn-primary">View</a>
                                <?php else: ?><em>N/A</em><?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php endif; ?>

        <!-- Memberships -->
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
                                    <a href="<?= base_url('uploads/memberships/' . $mem['certificate']) ?>" target="_blank" class="btn btn-sm btn-primary">View</a>
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
                    <thead>
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
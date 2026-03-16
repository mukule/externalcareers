<?= $this->extend('layouts/dashboard') ?>
<?= $this->section('content') ?>

<div class="container-fluid px-4 py-4">
    
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">Dashboard Overview</h2>
        <small class="text-muted"><?= date('d M Y, H:i') ?></small>
    </div>
    
    <!-- Statistics Cards -->
    <div class="row g-4 mb-3">

        <!-- Total Applicants -->
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-4 d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted text-uppercase small fw-semibold mb-1">Total Applicants</p>
                        <h3 class="mb-0 fw-bold"><?= esc($totalApplicants ?? 0) ?></h3>
                    </div>
                    <div class="text-primary">
                        <i class="fas fa-users fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Jobs -->
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-4 d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted text-uppercase small fw-semibold mb-1">Total Jobs</p>
                        <h3 class="mb-0 fw-bold"><?= esc($totalJobs ?? 0) ?></h3>
                    </div>
                    <div class="text-success">
                        <i class="fas fa-briefcase fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Open Jobs -->
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-4 d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted text-uppercase small fw-semibold mb-1">Open Jobs</p>
                        <h3 class="mb-0 fw-bold"><?= esc($openJobs ?? 0) ?></h3>
                    </div>
                    <div class="text-warning">
                        <i class="fas fa-folder-open fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Applications -->
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-4 d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted text-uppercase small fw-semibold mb-1">Applications</p>
                        <h3 class="mb-0 fw-bold"><?= esc($totalApplications ?? 0) ?></h3>
                    </div>
                    <div class="text-info">
                        <i class="fas fa-file-alt fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- Two-column row: Jobs & Applicants -->
    <div class="row g-4">

        <!-- Jobs with Application Counts -->
        <div class="col-xl-6">
            <div class="card shadow-sm border-0 p-2">
                 <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold">Jobs</h5>
                    <a href="<?= base_url('admin/jobs-applications') ?>" class="btn btn-sm btn-outline-primary">
                        View All <i class="fas fa-arrow-right ms-1"></i>
                    </a>
                </div>
                <div class="card-body p-2">
                    <?php if (!empty($jobsWithCounts)): ?>
                        <div class="table-responsive">
                            <table id="datatablesSimple" class="table table-bordered table-striped align-middle dashboard-table">
                                <thead class="table-dark">
                                    <tr>
                                        <th>#</th>
                                        <th>Job Title</th>
                                       
                                        <th>Created On</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($jobsWithCounts as $index => $job): ?>
                                        <tr>
                                            <td><?= $index + 1 ?></td>
                                            <td><?= esc($job['name']) ?>   - <span class="badge bg-info"><?= esc($job['applications_count']) ?></span></td>
                                           
                                            <td><?= date('d M Y', strtotime($job['created_at'])) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <p class="text-muted mb-0">No jobs found.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Latest Applicants -->
        <div class="col-xl-6">
            <div class="card shadow-sm border-0 p-2">
                <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold">Registrants</h5>
                    <a href="<?= base_url('admin/applicants') ?>" class="btn btn-sm btn-outline-primary">
                        View All <i class="fas fa-arrow-right ms-1"></i>
                    </a>
                </div>
                <div class="card-body p-2">
                    <?php if (!empty($latestApplicants)): ?>
                        <div class="table-responsive">
                            <table id="datatablesSimple" class="table align-middle dashboard-table datatable-simple">
                                <thead class="table-dark">
                                    <tr>
                                        <th>#</th>
                                        <th>Full Name</th>
                                        <th>Email</th>
                                        <th>Registered On</th>
                                       
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($latestApplicants as $index => $applicant): ?>
                                        <tr>
                                            <td><?= $index + 1 ?></td>
                                            <td><?= esc(trim($applicant['first_name'] . ' ' . $applicant['last_name'])) ?></td>
                                            <td><?= esc($applicant['email']) ?></td>
                                            <td><?= date('d M Y, H:i', strtotime($applicant['created_at'])) ?></td>
                                           
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-4">
                            <p class="text-muted mb-0">No applicants found.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

    </div>

</div>

<!-- Custom CSS for smaller table fonts -->
<style>
    .dashboard-table {
        font-size: 0.85rem; /* smaller font */
    }
    .dashboard-table th, 
    .dashboard-table td {
        padding: 0.35rem 0.5rem;
        vertical-align: middle;
    }
</style>

<?= $this->endSection() ?>

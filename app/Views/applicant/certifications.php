<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="container py-5">

<style>
    
    .table {
        font-size: 0.85rem; 
    }

    
    .table th, .table td {
        padding: 0.35rem 0.5rem; 
    }
</style>

    <?= $this->include('partials/prof_nav', ['currentStep' => 5]) ?>

    <div class="card shadow-sm mb-4">
        <div class="card-body">
          
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="card-title mb-0">Professional Certificates</h4>
                <a href="<?= base_url('applicant/certification/create') ?>" class="btn btn-outline-primary">Add New Certification</a>
            </div>

           
            <div class="table-responsive">
                <table class="table table-bordered table-striped mb-4">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Title</th>
                            <th>Certification</th>
                            <th>Certifying Body</th>
                            <th>Attained Date</th>
                            <th>File</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(!empty($certifications)): ?>
                            <?php foreach($certifications as $i => $cert): ?>
                                <tr>
                                    <td><?= $i + 1 ?></td>
                                    <td><?= esc($cert['name']) ?></td>
                                    <td><?= esc($cert['cert_name'] ?? 'N/A') ?></td>
                                    <td><?= esc($cert['body_name'] ?? 'N/A') ?></td>
                                    <td><?= !empty($cert['attained_date']) ? date('M Y', strtotime($cert['attained_date'])) : 'N/A' ?></td>
                                    <td>
                                        <?php if(!empty($cert['certificate_file'])): ?>
                                            <a href="<?= base_url('uploads/certs/' . $cert['certificate_file']) ?>" target="_blank">File</a>
                                        <?php else: ?>
                                            N/A
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <a href="<?= base_url('applicant/certification/edit/' . $cert['uuid']) ?>" class="btn btn-sm btn-outline-primary">Edit</a>
                                        <a href="<?= base_url('applicant/certification/delete/' . $cert['uuid']) ?>" class="btn btn-sm btn-outline-secondary" onclick="return confirm('Are you sure?');">Del</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center">No certification records found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

        </div>
    </div>

</div>

<?= $this->endSection() ?>
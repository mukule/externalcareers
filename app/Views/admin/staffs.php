<?= $this->extend('layouts/dashboard') ?>
<?= $this->section('content') ?>

<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mt-4 mb-3">
        <h1 class="mb-0"><?= esc($title ?? 'Staff Members') ?></h1>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#staffModal">
            <i class="fas fa-user-plus me-1"></i> Add / Promote Admin
        </button>
    </div>
    <hr>

    <div class="card mb-4">
        <div class="card-body">
            <?php if (!empty($staff) && count($staff) > 0): ?>
                <div class="table-responsive">
                    <table id="datatablesSimple" class="table table-bordered table-striped align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th>#</th>
                                <th>Full Name</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Status</th>
                                <th>Last Login</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($staff as $index => $user): ?>
                                <tr>
                                    <td><?= $index + 1 ?></td>
                                    <td><?= esc($user['first_name'] . ' ' . $user['last_name']) ?></td>
                                    <td><?= esc($user['email']) ?></td>
                                    <td><?= ucfirst(esc($user['role'])) ?></td>
                                    <td>
                                        <a href="<?= base_url('admin/staffs/toggle-active/' . $user['id']) ?>"
                                           class="btn btn-sm <?= $user['active'] ? 'btn-success' : 'btn-secondary' ?>">
                                           <?= $user['active'] ? 'Active' : 'Inactive' ?>
                                        </a>
                                    </td>
                                    <td>
                                        <?= !empty($user['last_login']) 
                                            ? date('d M Y, H:i', strtotime($user['last_login'])) 
                                            : '-' ?>
                                    </td>
                                    <td class="text-center">
                                        <a href="<?= base_url('admin/staffs/edit/' . $user['uuid']) ?>" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p class="text-muted text-center mb-0">No staff members found.</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Add / Promote Admin Modal -->
<div class="modal fade" id="staffModal" tabindex="-1" aria-labelledby="staffModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="post" action="<?= base_url('admin/staffs/save') ?>">
                <?= csrf_field() ?>
                <div class="modal-header">
                    <h5 class="modal-title" id="staffModalLabel">Add / Promote Admin</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Promote existing applicant -->
                    <div class="mb-3">
                        <label for="existing_user" class="form-label">Select Applicant to Promote</label>
                        <select name="existing_user" id="existing_user" class="form-select">
                            <option value="">-- Select Applicant --</option>
                            <?php if (!empty($applicants)): ?>
                                <?php foreach ($applicants as $applicant): ?>
                                    <option value="<?= esc($applicant['id']) ?>" 
                                        data-user='<?= json_encode([
                                            'first_name' => $applicant['first_name'],
                                            'last_name'  => $applicant['last_name'],
                                            'email'      => $applicant['email'],
                                        ]) ?>'>
                                        <?= esc($applicant['first_name'] . ' ' . $applicant['last_name'] . ' (' . $applicant['email'] . ')') ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                        <small class="text-muted">Choose an existing applicant to promote to admin.</small>
                    </div>

                    <hr>
                    <p class="text-muted">Or create a new admin:</p>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="first_name" class="form-label">First Name</label>
                            <input type="text" name="first_name" id="first_name" class="form-control" value="<?= set_value('first_name') ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="last_name" class="form-label">Last Name</label>
                            <input type="text" name="last_name" id="last_name" class="form-control" value="<?= set_value('last_name') ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" name="email" id="email" class="form-control" value="<?= set_value('email') ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" name="password" id="password" class="form-control">
                            <small class="text-muted">Leave blank to auto-generate a password for a new admin or leave blank when promoting.</small>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Save Admin</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.getElementById('existing_user').addEventListener('change', function() {
    const selectedOption = this.options[this.selectedIndex];
    if (selectedOption.value) {
        const data = JSON.parse(selectedOption.dataset.user);
        document.getElementById('first_name').value = data.first_name;
        document.getElementById('last_name').value = data.last_name;
        document.getElementById('email').value = data.email;
        document.getElementById('password').value = ''; 
    } else {
        document.getElementById('first_name').value = '';
        document.getElementById('last_name').value = '';
        document.getElementById('email').value = '';
        document.getElementById('password').value = '';
    }
});
</script>

<?= $this->endSection() ?>

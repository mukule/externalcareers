<?= $this->extend('layouts/dashboard') ?>
<?= $this->section('content') ?>

<div class="container-fluid px-4 mt-4">
    
    <div class="card mb-3 shadow-sm">
        <div class="card-body d-flex justify-content-between align-items-center">
            <nav aria-label="breadcrumb" class="mb-0">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="<?= base_url('admin') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page"><?= esc($title ?? 'Staff Members') ?></li>
                </ol>
            </nav>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#staffModal">
                <i class="fas fa-user-plus me-1"></i> Add Admin
            </button>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-body">

            <!-- Filters -->
            <form method="get" class="mb-3 d-flex gap-2 align-items-center">
                <input type="text" name="search" class="form-control" placeholder="Search by name or email" value="<?= esc($search ?? '') ?>">
                <select name="role" class="form-select">
                    <option value="">All Roles</option>
                    <?php if (!empty($roles)): ?>
                        <?php foreach ($roles as $role): ?>
                            <option value="<?= esc($role['id']) ?>" <?= (isset($roleFilter) && $roleFilter == $role['id']) ? 'selected' : '' ?>>
                                <?= esc(ucfirst($role['name'])) ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
                <button type="submit" class="btn btn-primary">Filter</button>

                <?php if (!empty($search) || !empty($roleFilter)): ?>
                    <a href="<?= base_url('admin/staffs') ?>" class="btn btn-secondary">Clear</a>
                <?php endif; ?>
            </form>

            <!-- Staff Table -->
            <?php if (!empty($staff) && count($staff) > 0): ?>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Full Name</th>
                                <th>Email</th>
                                <th>Roles</th>
                                <th>Status</th>
                                <th>Last Login</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($staff as $index => $user): ?>
                                <tr>
                                    <td><?= ($pager->getCurrentPage() - 1) * $perPage + $index + 1 ?></td>
                                    <td><?= esc($user['first_name'] . ' ' . $user['last_name']) ?></td>
                                    <td><?= esc($user['email']) ?></td>
                                    <td>
                                        <?php 
                                            if (!empty($user['roles'])) {
                                                echo implode(', ', array_map('ucfirst', array_column($user['roles'], 'name')));
                                            } else {
                                                echo '-';
                                            }
                                        ?>
                                    </td>
                                    <td>
                                        <a href="<?= base_url('admin/staffs/toggle-active/' . $user['id']) ?>"
                                           class="btn btn-sm <?= $user['active'] ? 'btn-primary' : 'btn-secondary' ?>">
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

                <div class="d-flex justify-content-end mt-3">
                    <?= $pager->links('group1', 'bootstrap5') ?>
                </div>
            <?php else: ?>
                <p class="text-muted text-center mb-0">No staff members found.</p>
            <?php endif; ?>

        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="staffModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="post" action="<?= base_url('admin/staffs/save') ?>">
                <?= csrf_field() ?>

                <div class="modal-header">
                    <h5 class="modal-title">Add or Convert Admin</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    <!-- Existing -->
                    <div class="mb-3">
                        <label class="form-label">Convert Existing Applicant</label>
                        <input type="text" id="existing_user_email" class="form-control" placeholder="Search email...">
                        <input type="hidden" name="existing_user" id="existing_user_id">
                        <div id="searchResults" class="list-group mt-1"></div>
                    </div>

                    <hr>

                    <!-- New -->
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label>First Name</label>
                            <input type="text" name="first_name" id="first_name" class="form-control">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label>Last Name</label>
                            <input type="text" name="last_name" id="last_name" class="form-control">
                        </div>

                        <div class="col-md-12 mb-3">
                            <label>Email</label>
                            <input type="email" name="email" id="email" class="form-control">
                        </div>

                        <!-- ✅ NEW FIELD -->
                        <div class="col-md-12 mb-3">
                            <label>National ID</label>
                            <input type="text" name="national_id" id="national_id" class="form-control" placeholder="Enter National ID">
                        </div>

                        <div class="col-md-12 mb-3">
                            <label>Assign Roles</label>
                            <div class="d-flex flex-wrap gap-2">
                                <?php foreach ($roles as $role): ?>
                                    <div class="form-check">
                                        <input type="checkbox" name="roles[]" value="<?= esc($role['id']) ?>" class="form-check-input">
                                        <label class="form-check-label"><?= esc(ucfirst($role['name'])) ?></label>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="modal-footer">
                    <button class="btn btn-primary">Save Admin</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                </div>

            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const emailInput = document.getElementById('existing_user_email');
    const hiddenId = document.getElementById('existing_user_id');
    const resultsBox = document.getElementById('searchResults');

    const fields = ['first_name','last_name','email','national_id'];

    let timer = null;

    emailInput.addEventListener('input', function() {
        const query = this.value.trim();

        hiddenId.value = '';
        fields.forEach(id => document.getElementById(id).disabled = false);
        resultsBox.innerHTML = '';

        if (query.length < 3) return;

        if (timer) clearTimeout(timer);
        timer = setTimeout(async () => {
            const res = await fetch("<?= base_url('admin/get-user-by-email') ?>?email=" + query);
            let data = await res.json();
            if (!Array.isArray(data)) data = [data];

            resultsBox.innerHTML = '';

            data.forEach(user => {
                const item = document.createElement('a');
                item.href = '#';
                item.className = 'list-group-item list-group-item-action';
                item.textContent = `${user.first_name} ${user.last_name} (${user.email})`;

                item.onclick = function(e) {
                    e.preventDefault();

                    hiddenId.value = user.id;
                    emailInput.value = user.email;

                    document.getElementById('first_name').value = user.first_name;
                    document.getElementById('last_name').value = user.last_name;
                    document.getElementById('email').value = user.email;

                    // optional: if you later return national_id
                    if (user.national_id) {
                        document.getElementById('national_id').value = user.national_id;
                    }

                    fields.forEach(id => document.getElementById(id).disabled = true);
                    resultsBox.innerHTML = '';
                };

                resultsBox.appendChild(item);
            });
        }, 300);
    });
});
</script>

<?= $this->endSection() ?>
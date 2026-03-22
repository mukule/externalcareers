<?= $this->extend('layouts/dashboard') ?>
<?= $this->section('content') ?>

<div class="container-fluid px-4">
  
    <div class="card mb-3 shadow-sm">
        <div class="card-body d-flex justify-content-between align-items-center">
            <nav aria-label="breadcrumb" class="mb-0">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="<?= base_url('admin') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="<?= base_url('admin/staffs') ?>">Staffs</a></li>
                    <li class="breadcrumb-item active" aria-current="page"><?= esc($title) ?></li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <form method="post" action="<?= base_url('admin/staffs/save') ?>">
                <?= csrf_field() ?>

                <!-- Existing user ID -->
                <?php if (!empty($staff['id'])): ?>
                    <input type="hidden" name="existing_user" value="<?= esc($staff['id']) ?>">
                <?php endif; ?>

                <!-- Staff Number / National ID -->
                <div class="mb-3">
                    <label for="national_id" class="form-label">Staff Number</label>
                    <input type="text" name="national_id" id="national_id" class="form-control"
                    value="<?= set_value('national_id', $nationalId ?? '') ?>"
                    placeholder="Enter Staff Number">
                </div>

                <!-- First Name -->
                <div class="mb-3">
                    <label for="first_name" class="form-label">First Name</label>
                    <input type="text" name="first_name" id="first_name" class="form-control" 
                        value="<?= set_value('first_name', $staff['first_name'] ?? '') ?>">
                </div>

                <!-- Last Name -->
                <div class="mb-3">
                    <label for="last_name" class="form-label">Last Name</label>
                    <input type="text" name="last_name" id="last_name" class="form-control" 
                        value="<?= set_value('last_name', $staff['last_name'] ?? '') ?>">
                </div>

                <!-- Email -->
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" name="email" id="email" class="form-control" 
                        value="<?= set_value('email', $staff['email'] ?? '') ?>">
                </div>

                <!-- Roles -->
                <div class="mb-3">
                    <label class="form-label">Assign Roles</label>
                    <div class="d-flex flex-wrap gap-2">
                        <!-- Applicant Checkbox -->
                        <div class="form-check">
                            <input 
                                class="form-check-input" 
                                type="checkbox" 
                                name="roles[]" 
                                value="applicant" 
                                id="role_applicant"
                                <?= empty($assignedRoles) || (isset($staff['role']) && $staff['role'] === 'applicant') ? 'checked' : '' ?>
                            >
                            <label class="form-check-label" for="role_applicant">
                                Applicant
                            </label>
                        </div>

                        <?php if (!empty($allRoles)): ?>
                            <?php foreach ($allRoles as $role): ?>
                                <div class="form-check">
                                    <input 
                                        class="form-check-input role-checkbox" 
                                        type="checkbox" 
                                        name="roles[]" 
                                        value="<?= esc($role['id']) ?>" 
                                        id="role_<?= esc($role['id']) ?>"
                                        <?= !empty($assignedRoles) && in_array($role['id'], $assignedRoles) ? 'checked' : '' ?>
                                    >
                                    <label class="form-check-label" for="role_<?= esc($role['id']) ?>">
                                        <?= esc(ucfirst($role['name'])) ?>
                                    </label>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p class="text-muted">No roles available. Please create roles first.</p>
                        <?php endif; ?>
                    </div>
                    <small class="text-muted">Select roles for the user. Checking Applicant will remove all other roles.</small>
                </div>

                <!-- Submit -->
                <button type="submit" class="btn btn-primary">
                    <?= !empty($staff) ? 'Update User' : 'Save User' ?>
                </button>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const applicantCheckbox = document.getElementById('role_applicant');
    const otherRoles = document.querySelectorAll('.role-checkbox');

    applicantCheckbox.addEventListener('change', function() {
        if (this.checked) {
            // Uncheck all other roles when applicant is checked
            otherRoles.forEach(r => r.checked = false);
        }
    });

    otherRoles.forEach(r => {
        r.addEventListener('change', function() {
            if (this.checked) {
                // Uncheck applicant if any other role is checked
                applicantCheckbox.checked = false;
            }
        });
    });
});
</script>

<?= $this->endSection() ?>
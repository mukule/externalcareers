<?= $this->extend('layouts/dashboard') ?>
<?= $this->section('content') ?>

<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mt-4">
        <h1 class="mb-0"><?= isset($staff) ? 'Edit Staff Member' : 'Add New Staff Member' ?></h1>
        <a href="<?= base_url('admin/staff') ?>" class="btn btn-outline-primary">
            <i class="fas fa-arrow-left me-1"></i> Back
        </a>
    </div>

    <div class="card shadow-sm mt-3">
        <div class="card-body">
            <form action="<?= $action ?>" method="post">
                <?= csrf_field() ?>

                <?php if (isset($staff)): ?>
                    <input type="hidden" name="id" value="<?= esc($staff['id']) ?>">
                <?php else: ?>
                    <input type="hidden" name="active" value="1">
                <?php endif; ?>

                <div class="row">
                    <!-- Username -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Username <span class="text-danger">*</span></label>
                        <input type="text"
                               name="username"
                               value="<?= esc($staff['username'] ?? old('username')) ?>"
                               class="form-control"
                               placeholder="Enter username"
                               required>
                    </div>

                    <!-- Email -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Email <span class="text-danger">*</span></label>
                        <input type="email"
                               name="email"
                               value="<?= esc($staff['email'] ?? old('email')) ?>"
                               class="form-control"
                               placeholder="Enter email address"
                               required>
                    </div>

                    <!-- Staff Number -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Staff Number</label>
                        <input type="text"
                               name="staff_no"
                               value="<?= esc($staff['staff_no'] ?? old('staff_no')) ?>"
                               class="form-control"
                               placeholder="Enter staff number">
                    </div>

                    <!-- First Name -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label">First Name</label>
                        <input type="text"
                               name="first_name"
                               value="<?= esc($staff['first_name'] ?? old('first_name')) ?>"
                               class="form-control"
                               placeholder="Enter first name">
                    </div>

                    <!-- Last Name -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Last Name</label>
                        <input type="text"
                               name="last_name"
                               value="<?= esc($staff['last_name'] ?? old('last_name')) ?>"
                               class="form-control"
                               placeholder="Enter last name">
                    </div>

                    <!-- Phone -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Phone</label>
                        <input type="text"
                               name="phone"
                               value="<?= esc($staff['phone'] ?? old('phone')) ?>"
                               class="form-control"
                               placeholder="Enter phone number">
                    </div>

                    <!-- Department -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Department</label>
                        <select name="department_id" class="form-select">
                            <option value="">-- Select Department --</option>
                            <?php foreach ($departments as $dept): ?>
                                <option value="<?= esc($dept['id']) ?>"
                                    <?= (isset($staff['department_id']) && $staff['department_id'] == $dept['id']) ? 'selected' : '' ?>>
                                    <?= esc($dept['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Password (new staff only) -->
                    <?php if (!isset($staff)): ?>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Password</label>
                            <input type="password"
                                   name="password"
                                   class="form-control"
                                   placeholder="Enter password (optional)">
                        </div>
                    <?php endif; ?>

                    <!-- Role -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Role</label>
                        <select name="role" id="roleSelect" class="form-select">
                            <?php
                                $roles = ['admin', 'security', 'receptionist', 'staff', 'executive'];
                                $selectedRole = $staff['role'] ?? old('role', 'staff');
                                foreach ($roles as $role): ?>
                                    <option value="<?= $role ?>" <?= ($selectedRole === $role) ? 'selected' : '' ?>>
                                        <?= ucfirst($role) ?>
                                    </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Assigned Executive (only for receptionists) -->
                    <div class="col-md-6 mb-3" id="executiveDiv" style="display: none;">
                        <label class="form-label">Executive Office</label>
                        <select name="assigned_exec_id" class="form-select">
                            <option value="">-- Select Executive --</option>
                            <?php foreach ($executives as $exec): ?>
                                <option value="<?= esc($exec['id']) ?>"
                                    <?= (isset($staff['assigned_exec_id']) && $staff['assigned_exec_id'] == $exec['id']) ? 'selected' : '' ?>>
                                    <?= esc($exec['first_name'] . ' ' . $exec['last_name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Active toggle (editing only) -->
                    <?php if (isset($staff)): ?>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Status</label>
                            <select name="active" class="form-select">
                                <option value="1" <?= ($staff['active'] == 1) ? 'selected' : '' ?>>Active</option>
                                <option value="0" <?= ($staff['active'] == 0) ? 'selected' : '' ?>>Inactive</option>
                            </select>
                        </div>
                    <?php endif; ?>

                </div>

                <div class="text-end mt-4">
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="fas fa-save me-1"></i> <?= isset($staff) ? 'Update Staff' : 'Add Staff' ?>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const roleSelect = document.getElementById('roleSelect');
        const execDiv = document.getElementById('executiveDiv');

        function toggleExecutive() {
            if (roleSelect.value === 'receptionist') {
                execDiv.style.display = 'block';
            } else {
                execDiv.style.display = 'none';
            }
        }

        // Initial toggle
        toggleExecutive();

        // Change event
        roleSelect.addEventListener('change', toggleExecutive);
    });
</script>

<?= $this->endSection() ?>

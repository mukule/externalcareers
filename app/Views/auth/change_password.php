<?= $this->extend('layouts/dashboard') ?>
<?= $this->section('content') ?>

<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mt-4">
        <h1 class="mb-0">Change Password</h1>
        <a href="<?= base_url('dashboard') ?>" class="btn btn-outline-primary">
            <i class="fas fa-arrow-left me-1"></i> Back
        </a>
    </div>

    <div class="card shadow-sm mt-3">
        <div class="card-body">


            <form action="<?= base_url('auth/update-password') ?>" method="post">
                <?= csrf_field() ?>

                <div class="row">
                    <!-- Current password -->
                    <div class="col-12 mb-3 position-relative">
                        <label class="form-label">Current Password</label>
                        <div class="input-group">
                            <input type="password"
                                   name="current_password"
                                   class="form-control"
                                   placeholder="Enter your current password"
                                   required>
                            <button class="btn btn-outline-secondary toggle-password" type="button">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>

                    <!-- New and Confirm password -->
                    <div class="col-md-6 mb-3 position-relative">
                        <label class="form-label">New Password</label>
                        <div class="input-group">
                            <input type="password"
                                   name="new_password"
                                   class="form-control"
                                   placeholder="Enter new password"
                                   required>
                            <button class="btn btn-outline-secondary toggle-password" type="button">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>

                    <div class="col-md-6 mb-3 position-relative">
                        <label class="form-label">Confirm New Password</label>
                        <div class="input-group">
                            <input type="password"
                                   name="confirm_new_password"
                                   class="form-control"
                                   placeholder="Re-enter new password"
                                   required>
                            <button class="btn btn-outline-secondary toggle-password" type="button">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="text-end mt-4">
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="fas fa-key me-1"></i> Update Password
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>

<!-- Password visibility toggler -->
<script>
    document.querySelectorAll('.toggle-password').forEach(btn => {
        btn.addEventListener('click', function() {
            const input = this.previousElementSibling;
            const icon = this.querySelector('i');
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.replace('fa-eye', 'fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.replace('fa-eye-slash', 'fa-eye');
            }
        });
    });
</script>

<?= $this->endSection() ?>

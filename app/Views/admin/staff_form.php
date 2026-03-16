<?= $this->extend('layouts/dashboard') ?>
<?= $this->section('content') ?>

<div class="container-fluid px-4">
    <h1 class="mt-4 mb-3"><?= esc($title) ?></h1>

    <div class="card mb-4">
        <div class="card-body">
            <form method="post" action="<?= base_url('admin/staffs/save') ?>">
                <?= csrf_field() ?>

                <?php if (!empty($staff['id'])): ?>
                    <input type="hidden" name="existing_user" value="<?= esc($staff['id']) ?>">
                <?php endif; ?>

                <div class="mb-3">
                    <label for="first_name" class="form-label">First Name</label>
                    <input type="text" name="first_name" id="first_name" class="form-control" value="<?= set_value('first_name', $staff['first_name'] ?? '') ?>">
                </div>

                <div class="mb-3">
                    <label for="last_name" class="form-label">Last Name</label>
                    <input type="text" name="last_name" id="last_name" class="form-control" value="<?= set_value('last_name', $staff['last_name'] ?? '') ?>">
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" name="email" id="email" class="form-control" value="<?= set_value('email', $staff['email'] ?? '') ?>">
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" name="password" id="password" class="form-control">
                    <small class="text-muted">
                        Leave blank to keep the current password or auto-generate a new one for new users.
                    </small>
                </div>

                <button type="submit" class="btn btn-primary">
                    <?= !empty($staff) ? 'Update Admin' : 'Save Admin' ?>
                </button>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title><?= esc($app_name ?? 'SIIMS') ?> | <?= esc($title ?? 'Change Password') ?></title>

    <link href="<?= base_url('css/styles.css') ?>" rel="stylesheet" />
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
    <link rel="icon" type="image/png" href="<?= base_url('favicon.ico') ?>" />

    <style>
        .password-toggle { cursor: pointer; z-index: 10; }
        .bg-blur { 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
    </style>
</head>

<body class="bg-blur">
    <div id="layoutAuthentication">
        <div id="layoutAuthentication_content">
            <main>
                <div class="container">
                    <div class="row justify-content-center">
                        <div class="col-lg-5">
                            <div class="card shadow-lg border-0 rounded-lg mt-5">
                                
                                <div class="card-header text-center border-0 bg-white p-4">
                                    <img src="<?= base_url('logo.png') ?>" alt="Logo" style="max-width: 120px;" class="mb-2" />
                                    <h3 class="fw-light my-2">Update Password</h3>
                                    <?php if (session()->get('must_change_password')): ?>
                                        <p class="text-muted small">For security, please set a new password to continue.</p>
                                    <?php endif; ?>
                                </div>

                                <div class="card-body px-4">
                                    <?= $this->include('partials/messages') ?>

                                    <form method="post" action="<?= base_url('auth/update-password') ?>" autocomplete="off">
                                        <?= csrf_field() ?>

                                        <?php if (!session()->get('must_change_password')): ?>
                                            <div class="form-floating mb-3 position-relative">
                                                <input class="form-control" id="currentPassword" type="password" name="current_password" placeholder="Current Password" required />
                                                <label for="currentPassword">Current Password</label>
                                                <span class="position-absolute password-toggle" onclick="togglePass('currentPassword', 'icon0')" style="top: 50%; right: 15px; transform: translateY(-50%);">
                                                    <i class="fas fa-eye" id="icon0"></i>
                                                </span>
                                            </div>
                                        <?php endif; ?>

                                        <div class="form-floating mb-3 position-relative">
                                            <input class="form-control" id="newPassword" type="password" name="new_password" placeholder="New Password" required />
                                            <label for="newPassword">New Password</label>
                                            <span class="position-absolute password-toggle" onclick="togglePass('newPassword', 'icon1')" style="top: 50%; right: 15px; transform: translateY(-50%);">
                                                <i class="fas fa-eye" id="icon1"></i>
                                            </span>
                                        </div>

                                        <div class="form-floating mb-4 position-relative">
                                            <input class="form-control" id="confirmPassword" type="password" name="confirm_new_password" placeholder="Confirm New Password" required />
                                            <label for="confirmPassword">Confirm New Password</label>
                                            <span class="position-absolute password-toggle" onclick="togglePass('confirmPassword', 'icon2')" style="top: 50%; right: 15px; transform: translateY(-50%);">
                                                <i class="fas fa-eye" id="icon2"></i>
                                            </span>
                                        </div>

                                        <div class="d-grid mb-3">
                                            <button type="submit" class="btn btn-primary btn-block py-2">Update & Sign In</button>
                                        </div>
                                    </form>
                                </div>

                                <div class="card-footer text-center py-3 border-0 bg-light">
                                    <div class="small">
                                        <?php if (!session()->get('must_change_password')): ?>
                                            <a href="<?= base_url('index') ?>" class="text-decoration-none text-muted">Cancel and return</a>
                                        <?php else: ?>
                                            <a href="<?= base_url('logout') ?>" class="text-decoration-none text-muted">Logout</a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function togglePass(inputId, iconId) {
            const input = document.getElementById(inputId);
            const icon = document.getElementById(iconId);
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.replace('fa-eye', 'fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.replace('fa-eye-slash', 'fa-eye');
            }
        }
    </script>
</body>
</html>
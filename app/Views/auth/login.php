<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title><?= esc($app_name) ?> | <?= esc($title ?? 'Login') ?></title>

    <link href="<?= base_url('css/styles.css') ?>" rel="stylesheet" />
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
    <link rel="icon" type="image/png" href="<?= base_url('favicon.ico') ?>" />
    <style>
        .forgot-link a, .register-link a {
             color: inherit !important;
            text-decoration: none !important;
        }
        .forgot-link a:hover, .register-link a:hover {
            text-decoration: underline;
        }
        .password-toggle { cursor: pointer; }
    </style>
</head>
<body class="bg-blur">
    <div id="layoutAuthentication p-4">
        <div id="layoutAuthentication_content">
            <main>
                <div class="container">
                    <div class="row justify-content-center">
                        <div class="col-lg-5">
                            <div class="card shadow-lg border-0 rounded-lg mt-5">
                                <div class="card-header text-center border-0 bg-white p-3">
                                    <img src="<?= base_url('logo.png') ?>" alt="<?= esc($app_name) ?>" class="mb-3" style="max-width: 200px;" />
                                    
                                    <!-- Mobile heading -->
                                    <h6 class="d-block d-md-none text-center font-weight-light">
                                        <?= esc($heading ?? 'Welcome Back') ?>
                                    </h6>
                                    <!-- Desktop heading -->
                                    <h4 class="d-none d-md-block text-center font-weight-light">
                                        <?= esc($heading ?? 'Welcome Back') ?>
                                    </h4>
                                </div>

                                <div class="card-body">
                                    <!-- Flash Messages -->
                                    <?= $this->include('partials/messages') ?>

                                    <form method="post" action="<?= base_url('/login') ?>" autocomplete="off">
                                        <?= csrf_field() ?>

                                        <!-- Email or Username -->
                                        <div class="form-floating mb-3">
                                            <input class="form-control"
                                                id="inputIdentifier"
                                                type="text"
                                                name="identifier"
                                                placeholder="Email address"
                                                value="<?= old('identifier') ?>"
                                                autocomplete="off"
                                                required />
                                            <label for="inputIdentifier">Email or Username</label>
                                        </div>

                                        <!-- Password with eye toggle -->
                                        <div class="form-floating mb-3 position-relative">
                                            <input class="form-control"
                                                id="inputPassword"
                                                type="password"
                                                name="password"
                                                placeholder="Password"
                                                autocomplete="new-password"
                                                required />
                                            <label for="inputPassword">Password</label>
                                            
                                            <span class="position-absolute password-toggle"
                                                  style="top: 50%; right: 15px; transform: translateY(-50%);"
                                                  onclick="togglePassword()">
                                                <i class="fas fa-eye" id="password-toggle-icon"></i>
                                            </span>
                                        </div>

                                        <!-- Remember password -->
                                        <div class="form-check mb-3">
                                            <input class="form-check-input" id="inputRememberPassword" type="checkbox" name="remember" <?= old('remember') ? 'checked' : '' ?> />
                                            <label class="form-check-label" for="inputRememberPassword">Remember Password</label>
                                        </div>

                                        <!-- Centered Login Button -->
                                        <div class="text-center mt-4 mb-3">
                                            <button type="submit" class="btn btn-primary px-5">Login</button>
                                        </div>

                                        <!-- Forgot Password and Register Links -->
                                        <div class="card-footer text-center py-3 border-0">
                                            <div class="small mb-1 forgot-link">
                                                <a href="<?= base_url('/password') ?>">Forgot password? Reset</a>
                                            </div>
                                            <div class="small register-link">
                                                <a href="<?= base_url('/register') ?>">Don't have an account? Register</a>
                                            </div>
                                        </div>
                                    </form>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="<?= base_url('js/scripts.js') ?>"></script>

    <script>
        function togglePassword() {
            const input = document.getElementById('inputPassword');
            const icon = document.getElementById('password-toggle-icon');
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }
    </script>
</body>
</html>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title><?= esc($app_name) ?> | <?= esc($title ?? 'Create Account') ?></title>

    <link href="<?= base_url('css/styles.css') ?>" rel="stylesheet" />
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
    <link rel="icon" type="image/png" href="<?= base_url('favicon.ico') ?>" />

    <style>
        .password-toggle { cursor: pointer; }

        .auth-link a {
            color: inherit !important;
            text-decoration: none !important;
        }
        .auth-link a:hover {
            text-decoration: underline !important;
        }

        .bg-blur { 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }

        .form-floating > .form-control {
    height: 38px !important;
    min-height: 38px !important;
    font-size: 0.8rem;
}

.form-floating > label {
    font-size: 0.75rem;
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

                                <!-- Header -->
                                <div class="card-header text-center border-0 bg-white p-2">
                                    <a href="<?= base_url('/') ?>">
                                        <img src="<?= base_url('logo.png') ?>" alt="<?= esc($app_name) ?>" class="mb-1" style="max-width: 150px;" />
                                    </a>
                                </div>

                                <div class="card-body">

                                    <!-- Flash messages -->
                                    <?= $this->include('partials/messages') ?>

                                    <form method="post" action="<?= esc($action) ?>" autocomplete="off">
                                        <?= csrf_field() ?>

                                        <!-- First Name -->
                                        <div class="form-floating mb-2">
                                            <input class="form-control"
                                                   id="firstName"
                                                   type="text"
                                                   name="first_name"
                                                   placeholder="First Name"
                                                   value="<?= old('first_name') ?>"
                                                   required />
                                            <label for="firstName">First Name</label>
                                        </div>

                                        <!-- Last Name -->
                                        <div class="form-floating mb-2">
                                            <input class="form-control"
                                                   id="lastName"
                                                   type="text"
                                                   name="last_name"
                                                   placeholder="Last Name"
                                                   value="<?= old('last_name') ?>"
                                                   required />
                                            <label for="lastName">Last Name</label>
                                        </div>

                                        <!-- Email -->
                                        <div class="form-floating mb-2">
                                            <input class="form-control"
                                                   id="email"
                                                   type="email"
                                                   name="email"
                                                   placeholder="Email Address"
                                                   value="<?= old('email') ?>"
                                                   required />
                                            <label for="email">Email Address</label>
                                        </div>

                                        <!-- National ID -->
                                        <div class="form-floating mb-2">
                                            <input class="form-control"
                                                   id="nationalId"
                                                   type="text"
                                                   name="national_id"
                                                   placeholder="National ID"
                                                   value="<?= old('national_id') ?>"
                                                   pattern="[0-9]{6,10}"
                                                   maxlength="10"
                                                   required />
                                            <label for="nationalId">National ID</label>
                                        </div>

                                        <!-- Password -->
                                        <div class="row g-2 mb-2">
                                            <div class="col-md-6 position-relative form-floating">
                                                <input class="form-control"
                                                    id="password"
                                                    type="password"
                                                    name="password"
                                                    placeholder="Password"
                                                    required />
                                                <label for="password">Password</label>
                                                <span class="position-absolute password-toggle"
                                                    onclick="togglePassword('password','icon1')"
                                                    style="top: 50%; right: 15px; transform: translateY(-50%);">
                                                    <i class="fas fa-eye" id="icon1"></i>
                                                </span>
                                            </div>

                                            <!-- Confirm Password -->
                                            <div class="col-md-6 position-relative form-floating">
                                                <input class="form-control"
                                                    id="confirmPassword"
                                                    type="password"
                                                    name="confirm_password"
                                                    placeholder="Confirm Password"
                                                    required />
                                                <label for="confirmPassword">Confirm Password</label>
                                                <span class="position-absolute password-toggle"
                                                    onclick="togglePassword('confirmPassword','icon2')"
                                                    style="top: 50%; right: 15px; transform: translateY(-50%);">
                                                    <i class="fas fa-eye" id="icon2"></i>
                                                </span>
                                            </div>
                                        </div>

                                        <!-- Register Button -->
                                        <div class="text-center mt-4 mb-3">
                                            <button type="submit" class="btn btn-primary px-5">Create Account</button>
                                        </div>

                                        <!-- Footer Links -->
                                        <div class="card-footer text-center py-3 border-0 auth-link">
                                            <div>
                                                <a href="<?= base_url('/login') ?>">Already have an account? Login</a>
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

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        function togglePassword(inputId, iconId) {
            const input = document.getElementById(inputId);
            const icon = document.getElementById(iconId);

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
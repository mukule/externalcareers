<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title><?= esc($app_name) ?> | <?= esc($title ?? 'Reset Password') ?></title>

    <link href="<?= base_url('css/styles.css') ?>" rel="stylesheet" />
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
<link rel="icon" type="image/png" href="<?= base_url('favicon.ico') ?>" />
    <style>
        .forgot-link a {
            text-decoration: none;
            color: #0d6efd;
        }
        .forgot-link a:hover {
            text-decoration: underline;
        }
        .btn-center {
            display: flex;
            justify-content: center;
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
                                <div class="card-header text-center border-0">
                                    <img src="<?= base_url('logo.png') ?>" alt="<?= esc($app_name) ?>" class="mb-3" style="max-width: 150px;" />
                                    <h3 class="text-center font-weight-light"><?= esc($heading ?? 'Reset Your Password') ?></h3>
                                    <p class="text-center text-muted mb-0"><?= esc($subheading ?? 'Enter your registered email to reset your password') ?></p>
                                </div>
                                <div class="card-body">
                                    <!-- Include flash messages -->
                                    <?= $this->include('partials/messages') ?>

                                    <form method="post" action="<?= base_url('/forgot-password') ?>" autocomplete="off">
                                        <?= csrf_field() ?>
                                        <div class="form-floating mb-3">
                                            <input 
                                                class="form-control" 
                                                id="inputEmail" 
                                                type="email" 
                                                name="email" 
                                                placeholder="name@example.com" 
                                                autocomplete="off" 
                                                required />
                                            <label for="inputEmail">Email address</label>
                                        </div>
                                        <div class="btn-center mt-4 mb-0">
                                            <button type="submit" class="btn btn-primary">Reset Password</button>
                                        </div>
                                    </form>

                                    <div class="forgot-link text-center mt-4">
                                        <a href="<?= base_url('login') ?>">← Back to Login</a>
                                    </div>
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
</body>
</html>

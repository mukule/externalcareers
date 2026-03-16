<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Welcome to <?= esc($appName ?? 'MyApp') ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f7f9fc;
            color: #333;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 90%;
            max-width: 600px;
            margin: 30px auto;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.05);
            padding: 20px;
        }
        h2 {
            color: #203d8d;
        }
        p {
            line-height: 1.6;
        }
        .button {
            display: inline-block;
            background-color: #203d8d;
            color: #fff !important;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 15px;
        }
        .footer {
            font-size: 12px;
            color: #999;
            margin-top: 30px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Welcome, <?= esc($first_name) ?>!</h2>
        <p>Thank you for registering on <?= esc($appName ?? 'CRVWWDA RECRUITMENT PORTAL') ?>. Your account has been successfully created.</p>
        <p>You can now log in to apply for your future Job.</p>
        <a href="<?= base_url('/login') ?>" class="button">Go to Login</a>
        <div class="footer">
            &copy; <?= date('Y') ?> <?= esc($appName ?? 'CRVWWDA RECRUITMENT PORTAL') ?>. All rights reserved.
        </div>
    </div>
</body>
</html>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Activate Your Account | <?= esc($appName ?? 'MyApp') ?></title>
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
            color: #5016aa;
        }
        p {
            line-height: 1.6;
        }
        .button {
            display: inline-block;
            background-color: #5016aa;
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
        <p>Thank you for registering on <?= esc($appName ?? 'Kengen Recruitment Portal') ?>.</p>
        <p>Before you can log in and apply for jobs, you need to activate your account.</p>
        <a href="<?= esc($activationLink) ?>" class="button">Activate My Account</a>
        <p>If the button doesn’t work, copy and paste the following link into your browser:</p>
        <p><a href="<?= esc($activationLink) ?>"><?= esc($activationLink) ?></a></p>
        <div class="footer">
            &copy; <?= date('Y') ?> <?= esc($appName ?? 'Kengen Recruitment Portal') ?>. All rights reserved.
        </div>
    </div>
</body>
</html>
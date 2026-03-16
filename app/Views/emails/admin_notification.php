<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Admin Account Notification</title>
</head>
<body style="font-family: Arial, sans-serif; background: #f9f9f9; padding: 20px;">
    <div style="max-width: 600px; background: #fff; padding: 25px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.05);">
        <h2 style="color: #007bff;">Hello <?= esc($first_name) ?>,</h2>

        <?php if (!empty($is_new_admin)): ?>
            <p>Congratulations! Your account has been created as an <strong>Administrator</strong> on our portal.</p>
            <p>Here are your login details:</p>
            <table style="width:100%; border-collapse: collapse;">
                <tr>
                    <td style="padding: 6px 0;"><strong>Email:</strong></td>
                    <td style="padding: 6px 0;"><?= esc($email) ?></td>
                </tr>
                <tr>
                    <td style="padding: 6px 0;"><strong>Password:</strong></td>
                    <td style="padding: 6px 0;"><?= esc($password) ?></td>
                </tr>
            </table>
        <?php else: ?>
            <p>Your account has been successfully <strong>promoted to Administrator</strong> on our portal.</p>
            <p>You can now access the admin panel using your existing credentials (email: <strong><?= esc($email) ?></strong>).</p>
        <?php endif; ?>

        <p style="margin-top: 20px;">
            Please log in and change your password if a new password was generated for you, to keep your account secure.
        </p>

        <p style="margin-top: 10px;">
            Thank you for being part of the CRVWWDA team.
        </p>

        <hr style="margin-top: 30px;">
        <p style="font-size: 12px; color: #777;">
            This is an automated message from CRVWWDA Recruitment Portal. Please do not reply to this email.
        </p>
    </div>
</body>
</html>

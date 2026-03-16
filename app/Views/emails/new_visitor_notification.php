<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>New Visitor Notification</title>
</head>
<body style="font-family: Arial, sans-serif; background: #f9f9f9; padding: 20px;">
    <div style="max-width: 600px; background: #fff; padding: 20px; border-radius: 8px;">
        <h2 style="color: #88bf51;">Hello <?= esc($host['first_name']) ?>,</h2>
        <p>You have a new visitor who has just checked in at the reception.</p>

        <h3 style="margin-top: 20px;">Visitor Details</h3>
        <table style="width:100%; border-collapse: collapse;">
            <tr>
                <td style="padding: 6px 0;"><strong>Name:</strong></td>
                <td style="padding: 6px 0;"><?= esc($visitor['first_name'] . ' ' . $visitor['last_name']) ?></td>
            </tr>
            <tr>
                <td style="padding: 6px 0;"><strong>Phone:</strong></td>
                <td style="padding: 6px 0;"><?= esc($visitor['phone'] ?? 'N/A') ?></td>
            </tr>
            <tr>
                <td style="padding: 6px 0;"><strong>Purpose:</strong></td>
                <td style="padding: 6px 0;"><?= esc($visitor['purpose'] ?? 'N/A') ?></td>
            </tr>
            <tr>
                <td style="padding: 6px 0;"><strong>Visit Type:</strong></td>
                <td style="padding: 6px 0;"><?= ucfirst(esc($visitor['visit_type'])) ?></td>
            </tr>
        </table>

        <p style="margin-top: 20px;">
            <strong><?= esc($visitor['first_name'] . ' ' . $visitor['last_name']) ?></strong> is on the way to meet you.
        </p>

        <hr style="margin-top: 30px;">
        <p style="font-size: 12px; color: #777;">
            This is an automated message from the Visitor Management System.
        </p>
    </div>
</body>
</html>

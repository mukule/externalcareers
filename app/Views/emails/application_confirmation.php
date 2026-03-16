<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Application Confirmation</title>
</head>
<body style="font-family: Arial, sans-serif; background: #f9f9f9; padding: 20px;">
    <div style="max-width: 600px; background: #fff; padding: 20px; border-radius: 8px;">
        <h2 style="color: #007bff;">Hello <?= esc($first_name) ?>,</h2>
        <p>Your application for the position <strong><?= esc($job_name) ?></strong> has been successfully submitted.</p>

        <h3 style="margin-top: 20px;">Application Details</h3>
        <table style="width:100%; border-collapse: collapse;">
            <tr>
                <td style="padding: 6px 0;"><strong>Reference Number:</strong></td>
                <td style="padding: 6px 0;"><?= esc($ref_no) ?></td>
            </tr>
            <tr>
                <td style="padding: 6px 0;"><strong>Position:</strong></td>
                <td style="padding: 6px 0;"><?= esc($job_name) ?></td>
            </tr>
        </table>

        <p style="margin-top: 20px;">
            Please make sure your CV and application details are up to date. Only shortlisted candidates will be contacted for the next steps.
        </p>

        <p style="margin-top: 10px;">
            Thank you for applying and for your interest in joining us.
        </p>

        <hr style="margin-top: 30px;">
        <p style="font-size: 12px; color: #777;">
            This is an automated message from CRVWWDA Recruitment Portal. Please do not reply to this email.
        </p>
    </div>
</body>
</html>

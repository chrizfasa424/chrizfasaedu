<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Contact Message</title>
</head>
<body style="font-family: Arial, sans-serif; color: #0f172a; line-height: 1.5; margin: 0; padding: 20px; background: #f8fafc;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width: 680px; margin: 0 auto; background: #ffffff; border: 1px solid #e2e8f0; border-radius: 8px;">
        <tr>
            <td style="padding: 20px 20px 10px;">
                <h2 style="margin: 0; font-size: 20px;">New Contact Form Submission</h2>
                <p style="margin: 8px 0 0; color: #475569;"><?php echo e($schoolName); ?></p>
            </td>
        </tr>
        <tr>
            <td style="padding: 10px 20px 20px;">
                <p style="margin: 0 0 10px;"><strong>Full Name:</strong> <?php echo e($payload['full_name']); ?></p>
                <p style="margin: 0 0 10px;"><strong>Email:</strong> <?php echo e($payload['email']); ?></p>
                <p style="margin: 0 0 10px;"><strong>Phone Number:</strong> <?php echo e($payload['phone_number'] ?: 'Not provided'); ?></p>
                <p style="margin: 0 0 10px;"><strong>Subject:</strong> <?php echo e($payload['subject']); ?></p>
                <p style="margin: 0 0 10px;"><strong>Submitted At:</strong> <?php echo e($submittedAt->format('Y-m-d H:i:s')); ?></p>
                <p style="margin: 0 0 10px;"><strong>IP Address:</strong> <?php echo e($requestIp); ?></p>

                <div style="margin-top: 16px; border-top: 1px solid #e2e8f0; padding-top: 16px;">
                    <p style="margin: 0 0 6px;"><strong>Message</strong></p>
                    <p style="margin: 0; white-space: pre-wrap;"><?php echo e($payload['message']); ?></p>
                </div>
            </td>
        </tr>
    </table>
</body>
</html>

<?php /**PATH C:\wamp64\www\chrizfasaedu\resources\views\emails\public-contact.blade.php ENDPATH**/ ?>
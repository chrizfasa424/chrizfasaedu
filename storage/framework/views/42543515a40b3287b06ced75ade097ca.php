<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SMTP Test Email</title>
</head>
<body style="font-family: Arial, sans-serif; color: #0f172a; line-height: 1.5; margin: 0; padding: 20px; background: #f8fafc;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width: 680px; margin: 0 auto; background: #ffffff; border: 1px solid #e2e8f0; border-radius: 8px;">
        <tr>
            <td style="padding: 20px;">
                <h2 style="margin: 0; font-size: 20px;">SMTP Test Successful</h2>
                <p style="margin: 8px 0 0; color: #475569;">This test message confirms your SMTP settings are working.</p>
            </td>
        </tr>
        <tr>
            <td style="padding: 0 20px 20px;">
                <p style="margin: 0 0 8px;"><strong>School:</strong> <?php echo e($school?->name ?? 'School'); ?></p>
                <p style="margin: 0 0 8px;"><strong>Recipient:</strong> <?php echo e($recipient); ?></p>
                <p style="margin: 0 0 8px;"><strong>Sent At:</strong> <?php echo e($sentAt->format('Y-m-d H:i:s')); ?></p>
                <p style="margin: 0 0 8px;"><strong>SMTP Host:</strong> <?php echo e($smtp['host'] ?? 'N/A'); ?></p>
                <p style="margin: 0;"><strong>SMTP Port:</strong> <?php echo e($smtp['port'] ?? 'N/A'); ?></p>
            </td>
        </tr>
    </table>
</body>
</html>

<?php /**PATH C:\wamp64\www\chrizfasaedu\resources\views\emails\smtp-test.blade.php ENDPATH**/ ?>
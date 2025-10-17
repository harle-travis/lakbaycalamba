<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Your Password - Lakbay Calamba</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            text-align: center;
            background-color: #1e3a8a;
            color: white;
            padding: 20px;
            border-radius: 8px 8px 0 0;
        }
        .content {
            background-color: #f8f9fa;
            padding: 30px;
            border-radius: 0 0 8px 8px;
        }
        .button {
            display: inline-block;
            background-color: #1e3a8a;
            color: white;
            padding: 12px 30px;
            text-decoration: none;
            border-radius: 5px;
            margin: 20px 0;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            font-size: 12px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Lakbay Calamba</h1>
        <p>Password Reset Request</p>
    </div>
    
    <div class="content">
        <h2>Hello {{ $user->name }},</h2>
        
        <p>We received a request to reset your password for your Lakbay Calamba account. If you made this request, click the button below to reset your password:</p>
        
        <div style="text-align: center;">
            <a href="{{ $resetUrl }}" class="button">Reset My Password</a>
        </div>
        
        <p>If the button doesn't work, you can copy and paste this link into your browser:</p>
        <p style="word-break: break-all; background-color: #e9ecef; padding: 10px; border-radius: 4px;">
            {{ $resetUrl }}
        </p>
        
        <p><strong>Important:</strong></p>
        <ul>
            <li>This link will expire in 1 hour for security reasons</li>
            <li>If you didn't request this password reset, please ignore this email</li>
            <li>Your password will remain unchanged until you create a new one</li>
        </ul>
        
        <p>If you're having trouble with the link above, please contact our support team.</p>
        
        <p>Best regards,<br>
        The Lakbay Calamba Team</p>
    </div>
    
    <div class="footer">
        <p>This email was sent from Lakbay Calamba Tourism Monitoring System</p>
        <p>© {{ date('Y') }} Lakbay Calamba. All rights reserved.</p>
    </div>
</body>
</html>

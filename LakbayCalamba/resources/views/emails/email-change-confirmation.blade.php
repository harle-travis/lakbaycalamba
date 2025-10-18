<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirm Email Change - Lakbay Calamba</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f8f9fa;
        }
        .container {
            background: white;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .logo {
            font-size: 24px;
            font-weight: bold;
            color: #2563eb;
            margin-bottom: 10px;
        }
        .title {
            font-size: 20px;
            color: #1f2937;
            margin-bottom: 20px;
        }
        .content {
            margin-bottom: 30px;
        }
        .button {
            display: inline-block;
            background-color: #2563eb;
            color: white;
            padding: 12px 30px;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 500;
            margin: 20px 0;
        }
        .button:hover {
            background-color: #1d4ed8;
        }
        .warning {
            background-color: #fef3c7;
            border: 1px solid #f59e0b;
            border-radius: 6px;
            padding: 15px;
            margin: 20px 0;
        }
        .footer {
            text-align: center;
            color: #6b7280;
            font-size: 14px;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
        }
        .expiry {
            color: #dc2626;
            font-weight: 500;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">🏛️ Lakbay Calamba</div>
            <h1 class="title">Confirm Email Change</h1>
        </div>

        <div class="content">
            <p>Hello <strong>{{ $user->name }}</strong>,</p>
            
            <p>You have requested to change your email address for your Lakbay Calamba account. To complete this change, please click the confirmation button below:</p>

            <div style="text-align: center;">
                <a href="{{ $confirmationUrl }}" class="button">Confirm Email Change</a>
            </div>

            <div class="warning">
                <strong>⚠️ Important Security Notice:</strong>
                <ul>
                    <li>This link will expire in <span class="expiry">24 hours</span></li>
                    <li>If you did not request this change, please ignore this email</li>
                    <li>Your current email will remain active until you confirm this change</li>
                </ul>
            </div>

            <p>If the button above doesn't work, you can copy and paste this link into your browser:</p>
            <p style="word-break: break-all; color: #2563eb;">{{ $confirmationUrl }}</p>

            <p><strong>New Email:</strong> {{ $newEmail }}</p>
            <p><strong>Current Email:</strong> {{ $user->email }}</p>
        </div>

        <div class="footer">
            <p>This email was sent from Lakbay Calamba Tourism Monitoring System</p>
            <p>If you have any questions, please contact our support team.</p>
        </div>
    </div>
</body>
</html>

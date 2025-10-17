<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Reward Eligibility Notification</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0; }
        .content { background: #f9f9f9; padding: 30px; border-radius: 0 0 10px 10px; }
        .reward-box { background: #fff; border: 2px solid #4CAF50; border-radius: 10px; padding: 20px; margin: 20px 0; text-align: center; }
        .button { display: inline-block; background: #4CAF50; color: white; padding: 12px 30px; text-decoration: none; border-radius: 5px; margin: 20px 0; }
        .footer { text-align: center; color: #666; font-size: 12px; margin-top: 30px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🎉 Congratulations!</h1>
            <p>You've earned a special reward!</p>
        </div>
        
        <div class="content">
            <p>Dear <strong>{{ $user->name }}</strong>,</p>
            
            <div class="reward-box">
                <h2>🏆 Reward Eligible!</h2>
                <p>You have collected <strong>{{ $user->stamps_count }} stamps</strong> from our tourist destinations!</p>
                <p>You are now eligible to claim your special reward.</p>
            </div>
            
            <h3>How to Claim Your Reward:</h3>
            <ol>
                <li>Visit the Calamba Tourism Office</li>
                <li>Present your Lakbay ID: <strong>{{ $user->lakbay_id }}</strong></li>
                <li>Show this email as proof of eligibility</li>
                <li>Claim your reward!</li>
            </ol>
            
            <p><strong>Tourism Office Location:</strong><br>
            Calamba City Hall<br>
            Calamba City, Laguna</p>
            
            <p><strong>Office Hours:</strong><br>
            Monday to Friday: 8:00 AM - 5:00 PM<br>
            Saturday: 8:00 AM - 12:00 PM</p>
            
            <p>Thank you for exploring our beautiful tourist destinations in Calamba!</p>
            
            <p>Best regards,<br>
            <strong>Calamba Tourism Office</strong></p>
        </div>
        
        <div class="footer">
            <p>This is an automated message from the Tourism Monitoring System.</p>
            <p>If you have any questions, please contact us at tourism@calamba.gov.ph</p>
        </div>
    </div>
</body>
</html>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Your Password - KickOff Stats</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 20px auto;
            background: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .header {
            background: linear-gradient(135deg, #00D26A 0%, #00A854 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .content {
            padding: 30px;
        }
        .button {
            display: inline-block;
            padding: 12px 30px;
            background: #00D26A;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin: 20px 0;
            font-weight: bold;
        }
        .button:hover {
            background: #00A854;
        }
        .footer {
            background: #f8f8f8;
            padding: 20px;
            text-align: center;
            font-size: 12px;
            color: #666;
        }
        .link-box {
            background: #f8f8f8;
            padding: 15px;
            border-radius: 5px;
            margin: 15px 0;
            word-break: break-all;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🔐 Reset Your Password</h1>
        </div>
        <div class="content">
            <p>Hello!</p>
            <p>You are receiving this email because we received a password reset request for your account.</p>
            <p>Click the button below to reset your password:</p>
            
            <center>
                <a href="{{ route('password.reset', ['token' => $token, 'email' => $email]) }}" class="button">
                    Reset Password
                </a>
            </center>

            <p>Or copy and paste this link into your browser:</p>
            <div class="link-box">
                {{ route('password.reset', ['token' => $token, 'email' => $email]) }}
            </div>

            <p><strong>This password reset link will expire in 60 minutes.</strong></p>

            <p>If you did not request a password reset, no further action is required.</p>

            <p>Regards,<br>KickOff Stats Team</p>
        </div>
        <div class="footer">
            <p>If you're having trouble clicking the "Reset Password" button, copy and paste the URL above into your web browser.</p>
            <p>&copy; {{ date('Y') }} KickOff Stats. All rights reserved.</p>
        </div>
    </div>
</body>
</html>

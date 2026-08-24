<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your OTP Code - Grow Capital Research</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            background-color: #f8fafc;
            color: #334155;
            margin: 0;
            padding: 0;
            line-height: 1.6;
        }
        .container {
            max-width: 580px;
            margin: 40px auto;
            background-color: #ffffff;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            border: 1px solid #e2e8f0;
        }
        .header {
            background-color: #004b87;
            padding: 32px;
            text-align: center;
            border-bottom: 4px solid #003b6b;
        }
        .header h1 {
            color: #ffffff;
            font-size: 22px;
            font-weight: 700;
            margin: 0;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }
        .body {
            padding: 40px 32px;
        }
        .body p {
            margin: 0 0 20px 0;
            font-size: 15px;
            color: #475569;
        }
        .otp-container {
            text-align: center;
            margin: 30px 0;
            padding: 20px;
            background-color: #f0f7ff;
            border: 2px dashed #004b87;
            border-radius: 12px;
        }
        .otp-code {
            font-family: 'Courier New', Courier, monospace;
            font-size: 36px;
            font-weight: 800;
            color: #004b87;
            letter-spacing: 6px;
            margin: 0;
            display: inline-block;
        }
        .expiry-text {
            font-size: 13px;
            color: #64748b;
            margin-top: 10px;
            font-weight: 500;
        }
        .warning-text {
            font-size: 13px;
            color: #ef4444;
            background-color: #fef2f2;
            border: 1px solid #fee2e2;
            padding: 12px 16px;
            border-radius: 8px;
            margin-top: 24px;
        }
        .footer {
            background-color: #f8fafc;
            padding: 24px 32px;
            text-align: center;
            border-top: 1px solid #e2e8f0;
        }
        .footer p {
            margin: 0 0 8px 0;
            font-size: 12px;
            color: #64748b;
        }
        .footer a {
            color: #004b87;
            text-decoration: none;
            font-weight: 500;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>Grow Capital Research</h1>
        </div>

        <!-- Body -->
        <div class="body">
            <p>Hello,</p>
            <p>You have requested a secure One-Time Password (OTP) to log into your account on the Grow Capital Research portal.</p>
            
            <!-- OTP Box -->
            <div class="otp-container">
                <div class="otp-code">{{ $otp }}</div>
                <div class="expiry-text">This code is valid for <strong>10 minutes</strong></div>
            </div>

            <p>Please enter this code on the verification screen to complete your login process.</p>

            <!-- Warning -->
            <div class="warning-text">
                <strong>Important Security Notice:</strong> Never share this OTP with anyone, including members of our support team. We will never ask for your password or verification code.
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>&copy; {{ date('Y') }} Grow Capital Research. All rights reserved.</p>
            <p>Registered SEBI Research Analyst: INH000029397</p>
            <p>Need assistance? Contact us at <a href="mailto:info@growcapitalresearch.com">info@growcapitalresearch.com</a></p>
        </div>
    </div>
</body>
</html>

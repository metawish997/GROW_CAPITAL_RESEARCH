<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Executed Service Agreement - Grow Capital Research</title>
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
        .body h2 {
            font-size: 18px;
            font-weight: 700;
            color: #0f172a;
            margin-top: 0;
            margin-bottom: 16px;
        }
        .body p {
            margin: 0 0 20px 0;
            font-size: 15px;
            color: #475569;
        }
        .info-box {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 24px;
            margin: 28px 0;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #f1f5f9;
            font-size: 14px;
        }
        .info-row:last-child {
            border-bottom: none;
        }
        .info-label {
            font-weight: 600;
            color: #64748b;
        }
        .info-value {
            color: #0f172a;
            font-weight: 500;
            text-align: right;
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
            <h2>Welcome to Grow Capital Research</h2>
            <p>Dear {{ $customer_name }},</p>
            <p>Thank you for subscribing to <strong>Grow Capital Research</strong>. Your digital KYC verification and Service Agreement have been successfully executed and registered.</p>
            <p>We have attached a copy of your digitally signed Service Agreement as a PDF attachment to this email for your records.</p>
            
            <!-- Summary Box -->
            <div class="info-box">
                <div style="font-size:12px; font-weight:700; text-transform:uppercase; color:#64748b; letter-spacing:0.5px; margin-bottom:12px; border-bottom:1px solid #e2e8f0; padding-bottom:8px;">Agreement Execution Summary</div>
                
                <table style="width:100%; border-collapse:collapse;">
                    <tr style="border-bottom:1px solid #f1f5f9;">
                        <td style="padding:10px 0; font-weight:600; color:#64748b; font-size:14px;">Customer Name</td>
                        <td style="padding:10px 0; color:#0f172a; font-weight:500; text-align:right; font-size:14px;">{{ $customer_name }}</td>
                    </tr>
                    <tr style="border-bottom:1px solid #f1f5f9;">
                        <td style="padding:10px 0; font-weight:600; color:#64748b; font-size:14px;">Email Address</td>
                        <td style="padding:10px 0; color:#0f172a; font-weight:500; text-align:right; font-size:14px;">{{ $email }}</td>
                    </tr>
                    <tr>
                        <td style="padding:10px 0; font-weight:600; color:#64748b; font-size:14px;">Executed On</td>
                        <td style="padding:10px 0; color:#0f172a; font-weight:500; text-align:right; font-size:14px;">{{ $date }}</td>
                    </tr>
                </table>
            </div>

            <p>If you have any questions regarding your subscription or require assistance setting up your account, please don't hesitate to reach out to our support desk.</p>
            
            <p>Best regards,<br><strong>Grow Capital Research Team</strong></p>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>&copy; {{ date('Y') }} Grow Capital Research. All rights reserved.</p>
            <p>Registered SEBI Research Analyst: INH000000000</p>
            <p>Need assistance? Contact us at <a href="mailto:support@growcapitals.com">support@growcapitals.com</a></p>
        </div>
    </div>
</body>
</html>

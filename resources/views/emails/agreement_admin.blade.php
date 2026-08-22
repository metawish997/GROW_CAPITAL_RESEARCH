<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Executed Agreement - Grow Capital Research</title>
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
            background-color: #0f172a;
            padding: 32px;
            text-align: center;
            border-bottom: 4px solid #1e293b;
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
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>Compliance Alert</h1>
        </div>

        <!-- Body -->
        <div class="body">
            <h2>New Service Agreement Executed</h2>
            <p>Hello Admin,</p>
            <p>A new digital Service Agreement has been successfully executed by the following customer and verified via the Digio portal:</p>
            
            <!-- Summary Box -->
            <div class="info-box">
                <div style="font-size:12px; font-weight:700; text-transform:uppercase; color:#64748b; letter-spacing:0.5px; margin-bottom:12px; border-bottom:1px solid #e2e8f0; padding-bottom:8px;">Customer Registration Info</div>
                
                <table style="width:100%; border-collapse:collapse;">
                    <tr style="border-bottom:1px solid #f1f5f9;">
                        <td style="padding:10px 0; font-weight:600; color:#64748b; font-size:14px;">Customer ID</td>
                        <td style="padding:10px 0; color:#0f172a; font-weight:500; text-align:right; font-size:14px;">#{{ $user_id }}</td>
                    </tr>
                    <tr style="border-bottom:1px solid #f1f5f9;">
                        <td style="padding:10px 0; font-weight:600; color:#64748b; font-size:14px;">Customer Name</td>
                        <td style="padding:10px 0; color:#0f172a; font-weight:500; text-align:right; font-size:14px;">{{ $customer_name }}</td>
                    </tr>
                    <tr style="border-bottom:1px solid #f1f5f9;">
                        <td style="padding:10px 0; font-weight:600; color:#64748b; font-size:14px;">Email Address</td>
                        <td style="padding:10px 0; color:#0f172a; font-weight:500; text-align:right; font-size:14px;">{{ $email }}</td>
                    </tr>
                    <tr style="border-bottom:1px solid #f1f5f9;">
                        <td style="padding:10px 0; font-weight:600; color:#64748b; font-size:14px;">Mobile Number</td>
                        <td style="padding:10px 0; color:#0f172a; font-weight:500; text-align:right; font-size:14px;">{{ $mobile }}</td>
                    </tr>
                    <tr>
                        <td style="padding:10px 0; font-weight:600; color:#64748b; font-size:14px;">Execution Timestamp</td>
                        <td style="padding:10px 0; color:#0f172a; font-weight:500; text-align:right; font-size:14px;">{{ $date }}</td>
                    </tr>
                </table>
            </div>

            <p>The executed agreement PDF is attached to this email and has been archived in the local storage disk.</p>
            <p>You can manage and inspect this customer's details and verification files directly inside the admin panel.</p>
            
            <p>Best regards,<br><strong>Grow Capital Compliance System</strong></p>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>&copy; {{ date('Y') }} Grow Capital Research. Compliance & Risk Operations.</p>
        </div>
    </div>
</body>
</html>

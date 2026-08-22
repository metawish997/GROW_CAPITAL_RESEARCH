<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to Grow Capital Research</title>
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
            max-width: 620px;
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
        .section-title {
            font-size: 15px;
            font-weight: 700;
            color: #0f172a;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-top: 28px;
            margin-bottom: 12px;
            border-bottom: 2px solid #e2e8f0;
            padding-bottom: 6px;
        }
        .bullet-list {
            margin: 0 0 20px 0;
            padding-left: 20px;
        }
        .bullet-list li {
            font-size: 14px;
            color: #475569;
            margin-bottom: 8px;
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
            <h2>Welcome onboard!</h2>
            <p>Greetings for the day!!!!</p>
            <p>Dear {{ $customer_name }},</p>
            <p>Yogendra Singh Tomar Proprietor of Grow Capital Research is a SEBI (Securities and Exchange Board of India) registered Research Analyst having registration number: <strong>INH000029397</strong> offering research services.</p>
            <p>You are receiving this mail as you have subscribed to our research services.</p>
            <p>I request you to kindly go through this Welcome mail and visit our website <a href="https://growcapitals.com" target="_blank" style="color:#004b87; font-weight:600; text-decoration:none;">growcapitals.com</a> and if you have any query feel free to reach out to us at <a href="mailto:support@growcapitals.com" style="color:#004b87; font-weight:600; text-decoration:none;">support@growcapitals.com</a>.</p>

            <!-- Important Notes -->
            <div class="section-title" style="color:#b45309; border-bottom-color:#fef3c7;">Important Notes</div>
            <ul class="bullet-list">
                <li>I do not offer any refund on our services.</li>
                <li>You need to do the KYC before proceeding ahead with the service.</li>
                <li>I do not provide profit guaranteed or committed returns. In case any person is trying to sell you such services do inform us at <strong>+917999308418</strong>. Also, we do not sell any profit-sharing services.</li>
                <li>I do not engage in Managing of fund, Portfolio management or Investment Advisory services.</li>
                <li>Investment in securities market is subject to risk, trade carefully with proper Stop Loss; you may lose more than your actual Investment.</li>
                <li>Please pay our service charges only in the bank account mentioned on our website. If anyone asks for payment in a personal bank account, kindly inform us at <strong>+91 7999308418</strong>.</li>
            </ul>

            <!-- Disclaimer -->
            <div class="section-title">Disclaimer</div>
            <ul class="bullet-list" style="padding-left: 20px;">
                <li>Investments in securities market are subject to market risks. Read all the related documents carefully before investing.</li>
                <li>Registration granted by SEBI, Enlistment as RA with Exchange, and certification from NISM do not guarantee the performance of the intermediary or provide any assurance of returns to investors.</li>
                <li>I do not recommend any stock broker to the clients.</li>
                <li>I do not operate any trading or Demat account of any client.</li>
                <li>I do not offer any distribution or execution services.</li>
                <li>I do not share any information of our client with any third-party vendors and companies nor do we store details of customer debit and credit cards in our database.</li>
                <li>All the research recommendations are based on proper technical and fundamental analysis.</li>
                <li>Any profits or losses resulting from our recommendations are solely the responsibility of the client. Yogendra Singh Tomar Proprietor of Grow Capital Research shall not be liable for any trade losses incurred based on the research provided, and clients have no right to claim compensation for any losses under any circumstances.</li>
            </ul>

            <p style="background-color:#f8fafc; border-left:4px solid #004b87; padding:12px 16px; font-size:14px; font-style:italic; color:#475569; margin:24px 0;">
                Our scope of work is restricted to offering the trading/research recommendations. Investment in securities is subject to market risk, though sufficient research, attempts are made for predicting markets, but there is no surety of return or accuracy of any kind is guaranteed. Clients are recommended to consider all the research recommendation as just an opinion and make investment decision on their own. In case of any query feel free to contact us at support@growcapitals.com.
            </p>

            <p style="margin-top: 32px;">
                Regards,<br>
                <strong>Yogendra Singh Tomar Proprietor of Grow Capital Research</strong><br>
                <span style="font-size:13px; color:#64748b;">Sebi Registered Research Analyst<br>Reg. No. INH000029397</span>
            </p>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>&copy; {{ date('Y') }} Grow Capital Research. All rights reserved.</p>
        </div>
    </div>
</body>
</html>

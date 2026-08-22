<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Grow Capitals - Onboarding Portal</title>
    <meta name="description" content="Secure login and KYC compliance portal for Grow Capitals." />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet" />
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --primary: #004b87;
            --primary-dark: #003764;
            --accent: #98d102;
            --text-dark: #0b2d49;
            --bg: #fdfdfd;
            --border: #e2e8f0;
            --amber-bg: #fffbeb;
            --amber-border: #fef3c7;
            --amber-text: #b45309;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: var(--bg);
            color: var(--text-dark);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            padding: 60px 0;
        }

        .container {
            max-width: 1380px;
            width: 100%;
            margin: 0 auto;
            padding: 0 40px;
        }

        .top-nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 60px;
            width: 100%;
        }

        .main-title {
            font-size: 38px;
            font-weight: 700;
            color: var(--text-dark);
            line-height: 1.25;
            max-width: 600px;
            letter-spacing: -0.5px;
        }

        .main-title .green-text {
            color: var(--accent);
        }

        .contact-btn {
            background: var(--primary);
            color: #ffffff;
            padding: 12px 28px;
            border-radius: 50px;
            font-size: 13px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.2s ease;
            border: none;
            cursor: pointer;
        }

        .contact-btn:hover {
            background: var(--primary-dark);
            transform: translateY(-1px);
        }

        .portal-grid {
            display: grid;
            grid-template-columns: 1.3fr 1fr;
            gap: 40px;
            width: 100%;
        }

        .left-visual-card {
            background: linear-gradient(135deg, rgba(11,45,73,0.65) 0%, rgba(11,45,73,0.2) 100%), 
                        url('{{ asset('kyc.webp') }}');
            background-size: cover;
            background-position: center;
            border-radius: 28px;
            height: 520px;
            position: relative;
            padding: 50px;
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
            box-shadow: 0 4px 30px rgba(0,0,0,0.03);
            border: 1px solid var(--border);
        }

        .left-overlay-text h3 {
            font-size: 24px;
            font-weight: 700;
            color: #ffffff;
            letter-spacing: 1px;
            line-height: 1.3;
            margin-bottom: 24px;
            text-transform: uppercase;
            text-shadow: 0 2px 4px rgba(0,0,0,0.15);
        }

        .left-overlay-text p {
            font-size: 13px;
            color: rgba(255,255,255,0.85);
            line-height: 1.6;
            text-shadow: 0 1px 2px rgba(0,0,0,0.1);
        }

        /* Floating Logo Badge */
        .floating-logo-badge {
            position: absolute;
            bottom: 40px;
            right: 40px;
            background: #ffffff;
            border-radius: 20px;
            padding: 16px 24px;
            box-shadow: 0 10px 30px rgba(11,45,73,0.08);
            display: flex;
            align-items: center;
            justify-content: center;
            border: 1px solid #f1f5f9;
        }

        .floating-logo-badge img {
            height: 44px;
            width: auto;
            object-fit: contain;
        }

        /* RIGHT CARD (Lime Green Login Panel) */
        .right-login-card {
            background: var(--accent);
            border-radius: 28px;
            padding: 48px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            min-height: 520px;
            box-shadow: 0 10px 30px rgba(152,209,2,0.15);
            position: relative;
        }

        .card-top-badge {
            background: rgba(11,45,73,0.06);
            border: 1px solid rgba(11,45,73,0.12);
            color: var(--text-dark);
            padding: 6px 14px;
            border-radius: 50px;
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            width: fit-content;
            margin-bottom: 24px;
        }

        .card-title {
            font-size: 26px;
            font-weight: 700;
            color: var(--text-dark);
            line-height: 1.25;
            margin-bottom: 28px;
            letter-spacing: -0.5px;
        }

        /* Interactive login forms */
        .form-container {
            flex: 1;
            margin-bottom: 32px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .right-login-card label {
            display: block;
            font-size: 11px;
            font-weight: 700;
            color: var(--text-dark);
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .right-login-card input {
            width: 100%;
            padding: 15px 18px;
            background: #ffffff;
            border: 1px solid transparent;
            border-radius: 12px;
            color: var(--text-dark);
            font-size: 15px;
            font-family: inherit;
            outline: none;
            transition: all 0.2s;
        }

        .right-login-card input:focus {
            box-shadow: 0 0 0 3px rgba(11,45,73,0.15);
        }

        .right-login-card input::placeholder {
            color: #94a3b8;
        }

        /* OTP INPUTS */
        .otp-inputs {
            display: flex;
            gap: 8px;
            justify-content: space-between;
        }
        .otp-digit {
            width: 48px;
            height: 52px;
            text-align: center;
            font-size: 20px;
            font-weight: 700;
            border-radius: 10px;
            padding: 0 !important;
        }

        .btn-submit {
            width: 100%;
            padding: 16px;
            background: var(--primary);
            color: #ffffff;
            border: none;
            border-radius: 50px;
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            margin-top: 10px;
        }

        .btn-submit:hover {
            background: var(--primary-dark);
            transform: translateY(-1px);
            box-shadow: 0 6px 15px rgba(0,75,135,0.25);
        }

        .btn-submit:disabled {
            opacity: 0.7;
            cursor: not-allowed;
            transform: none;
        }

        .login-loader {
            width: 16px;
            height: 16px;
            border: 2px solid rgba(255,255,255,0.3);
            border-top-color: #ffffff;
            border-radius: 50%;
            animation: spin 0.7s linear infinite;
        }
        @keyframes spin { to { transform: rotate(360deg); } }

        .resend-link {
            text-align: center;
            margin-top: 14px;
            font-size: 12px;
            color: var(--text-dark);
        }

        .resend-link button {
            background: none;
            border: none;
            color: var(--primary);
            text-decoration: underline;
            font-weight: 700;
            cursor: pointer;
        }

        .resend-link button:disabled {
            text-decoration: none;
            opacity: 0.7;
            cursor: default;
        }

        /* Card bottom description */
        .card-bottom-desc {
            font-size: 11px;
            color: rgba(11,45,73,0.85);
            line-height: 1.5;
            border-top: 1px solid rgba(11,45,73,0.1);
            padding-top: 18px;
        }

        /* Alert Toast inside Green Card */
        .auth-alert {
            padding: 12px;
            border-radius: 8px;
            font-size: 12px;
            margin-bottom: 16px;
            display: none;
            align-items: center;
            gap: 8px;
            line-height: 1.4;
            background: rgba(255,255,255,0.2);
            color: var(--text-dark);
            border: 1px solid rgba(255,255,255,0.4);
            font-weight: 500;
        }

        #step-otp { display: none; }

        .timer-badge {
            background: rgba(11,45,73,0.08);
            color: var(--text-dark);
            padding: 2px 8px;
            border-radius: 20px;
            font-size: 10px;
            font-weight: 700;
        }

        .compliance-container {
            margin: 40px 0 60px 0;
            position: relative;
            z-index: 10;
            width: 100%;
        }

        .notice-card {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 20px;
            padding: 44px;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02);
            position: relative;
            overflow: hidden;
        }
        .notice-header {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 24px;
            border-bottom: 1px solid var(--border);
            padding-bottom: 14px;
            position: relative;
            z-index: 2;
        }
        .notice-icon {
            font-size: 20px;
            color: var(--primary);
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .notice-header h2 {
            font-size: 18px;
            font-weight: 700;
            color: var(--text-dark);
        }
        .notice-body {
            position: relative;
            z-index: 2;
        }
        .notice-body p {
            font-size: 16px;
            line-height: 1.8;
            margin-bottom: 18px;
            color: #475569;
            font-weight: 400;
        }
        .notice-body strong {
            color: var(--accent);
            font-weight: 600;
        }
        .notice-alert {
            background: var(--amber-bg);
            border: 1px solid var(--amber-border);
            border-radius: 12px;
            padding: 14px 18px;
            font-size: 13px;
            color: var(--amber-text);
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 10px;
            margin-top: 24px;
        }

        /* Responsive styling */
        @media (max-width: 992px) {
            body { padding: 40px 0; }
            .container { padding: 0 20px; }
            .top-nav { flex-direction: column-reverse; gap: 20px; align-items: flex-start; margin-bottom: 40px; }
            .main-title { font-size: 24px; line-height: 1.3; }
            .portal-grid { grid-template-columns: 1fr; }
            .left-visual-card { display: none; }
            .floating-logo-badge { bottom: 20px; right: 20px; }
            .compliance-container { margin-top: 30px; }
            .right-login-card { padding: 32px 24px; min-height: auto; border-radius: 24px; }
            .card-top-badge { margin-bottom: 16px; }
            .card-title { font-size: 20px; margin-bottom: 22px; }
            .right-login-card label { font-size: 10px; margin-bottom: 6px; }
            .right-login-card input { padding: 13px 15px; font-size: 14px; border-radius: 10px; }
            .otp-inputs { gap: 6px; }
            .otp-digit { width: 38px; height: 42px; font-size: 18px; border-radius: 8px; }
            .btn-submit { padding: 13px; font-size: 14px; margin-top: 6px; }
            .card-bottom-desc { font-size: 10px; margin-top: 24px; padding-top: 14px; }
            .notice-card { padding: 28px 20px; border-radius: 16px; }
            .notice-header { margin-bottom: 18px; padding-bottom: 12px; gap: 8px; }
            .notice-header h2 { font-size: 16px; }
            .notice-body p { font-size: 14px; line-height: 1.65; margin-bottom: 12px; }
            .notice-card hr { margin: 28px 0 !important; }
            .notice-alert { padding: 12px 14px; font-size: 12px; margin-top: 18px; gap: 8px; }
        }
    </style>
</head>
<body>

<div class="container">

<!-- TOP NAVIGATION -->
<div class="top-nav">
    <h1 class="main-title">Take the <span class="green-text">Next Step</span> in Your Investment Journey</h1>
    <div class="logo-wrap">
        <img src="{{ asset('grologo.png') }}" alt="Grow Capital Logo" style="height: 52px; width: auto; object-fit: contain; display: block;" />
    </div>
</div>

<!-- MAIN GRID -->
<div class="portal-grid">
    
    <!-- LEFT CARD (Visual Panel) -->
    <div class="left-visual-card">
        <div class="left-overlay-text">
            <h3>Research<br/>Insights<br/>Growth</h3>
            <p>Data. Research. Insights. Better Decisions.</p>
        </div>
        <!-- Floating Logo Badge -->
        <div class="floating-logo-badge">
            <img src="{{ asset('grologo.png') }}" alt="Grow Capital Logo" />
        </div>
    </div>

    <!-- RIGHT CARD (Lime Green Login Panel) -->
    <div class="right-login-card">
        <div>
            <div class="card-top-badge">Secure Portal</div>
            <h3 class="card-title">Start Your Onboarding & KYC</h3>

            <!-- Alert Toast -->
            <div id="authAlert" class="auth-alert"></div>

            <div class="form-container">
                <!-- Step 1: Email Input -->
                <div id="step-email">
                    <form id="emailForm">
                        <div class="form-group">
                            <label for="email">Enter Email Address</label>
                            <input type="email" id="email" placeholder="name@example.com" required autocomplete="email" />
                        </div>
                        <button type="submit" class="btn-submit" id="sendOtpBtn">
                            Begin Verification &nbsp;➔
                        </button>
                    </form>
                </div>

                <!-- Step 2: OTP Input -->
                <div id="step-otp">
                    <form id="otpForm">
                        <div class="form-group">
                            <label style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 12px;">
                                <span>Enter OTP Code</span>
                                <span class="timer-badge" id="otpTimer">10:00</span>
                            </label>
                            <div class="otp-inputs">
                                <input class="otp-digit" type="text" maxlength="1" inputmode="numeric" pattern="[0-9]" id="d1" />
                                <input class="otp-digit" type="text" maxlength="1" inputmode="numeric" pattern="[0-9]" id="d2" />
                                <input class="otp-digit" type="text" maxlength="1" inputmode="numeric" pattern="[0-9]" id="d3" />
                                <input class="otp-digit" type="text" maxlength="1" inputmode="numeric" pattern="[0-9]" id="d4" />
                                <input class="otp-digit" type="text" maxlength="1" inputmode="numeric" pattern="[0-9]" id="d5" />
                                <input class="otp-digit" type="text" maxlength="1" inputmode="numeric" pattern="[0-9]" id="d6" />
                            </div>
                        </div>
                        <button type="submit" class="btn-submit" id="verifyOtpBtn">
                            Verify & Login &nbsp;➔
                        </button>
                    </form>
                    <div class="resend-link">
                        <span id="resendText" style="color: rgba(11,45,73,0.85);">Didn't receive code? </span>
                        <button id="resendBtn" disabled>Resend OTP (<span id="resendTimer">30</span>s)</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card bottom description -->
        <div class="card-bottom-desc">
            At Grow Capital Research, we empower investors with reliable market research, actionable insights, and data-driven investment strategies. Our experienced analysts help you navigate market opportunities with confidence.
        </div>
    </div>
</div>

<!-- BILINGUAL COMPLIANCE NOTICES -->
<div class="compliance-container">
    <div class="notice-card">
        <!-- Hindi Notice Section -->
        <div class="notice-section">
            <div class="notice-header">
                <div class="notice-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="stroke: var(--primary);"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>
                </div>
                <h2>महत्वपूर्ण सूचना – KYC अनिवार्य</h2>
            </div>
            <div class="notice-body">
                <p>किसी भी वित्तीय (Financial) लेन-देन एवं सेवाओं का लाभ प्राप्त करने हेतु <strong>KYC (Know Your Customer) पूर्ण करना अनिवार्य है।</strong></p>
                <p>KYC आपकी सुरक्षा, पहचान सत्यापन तथा वित्तीय लेन-देन में पारदर्शिता सुनिश्चित करने के लिए की जाती है। सभी ग्राहकों से अनुरोध है कि वे अपनी KYC प्रक्रिया अवश्य पूर्ण करें। <strong>KYC पूर्ण होने के बाद ही सेवाएं प्रारंभ की जाएंगी।</strong></p>
                <p>यह प्रक्रिया भारत सरकार के नियामकीय दिशानिर्देशों तथा Anti-Money Laundering (AML) और संबंधित अनुपालन आवश्यकताओं के अनुरूप है। आप अपनी सुविधा अनुसार प्रदान किए गए ऑनलाइन लिंक के माध्यम से स्वयं KYC पूर्ण कर सकते हैं।</p>
                <div class="notice-alert">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="stroke: var(--amber-text); flex-shrink: 0;"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>
                    <span>नोट: KYC पूर्ण न होने की स्थिति में आपकी सेवाएं सक्रिय (Activate) नहीं की जाएंगी।</span>
                </div>
            </div>
        </div>

        <!-- Horizontal Divider Line -->
        <hr style="border: 0; border-top: 1px solid var(--border); margin: 40px 0;" />

        <!-- English Notice Section -->
        <div class="notice-section">
            <div class="notice-header">
                <div class="notice-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="stroke: var(--primary);"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>
                </div>
                <h2>Important Notice – KYC Mandatory</h2>
            </div>
            <div class="notice-body">
                <p>To avail of any financial transaction or service, <strong>completion of KYC (Know Your Customer) is mandatory.</strong></p>
                <p>KYC is conducted to ensure customer security, identity verification, and transparency in financial transactions. All customers are requested to complete their KYC process. <strong>Services will be activated only after successful completion of KYC.</strong></p>
                <p>This requirement is in accordance with the applicable regulatory guidelines of the Government of India, Anti-Money Laundering (AML) regulations, and related compliance obligations. Customers may complete their KYC conveniently through the online link provided by the Company.</p>
                <div class="notice-alert">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="stroke: var(--amber-text); flex-shrink: 0;"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>
                    <span>Note: Services will not be activated until the KYC process has been successfully completed.</span>
                </div>
            </div>
        </div>
    </div>
</div>

</div>

<script>
    const API_BASE = '/api';
    let userEmail = '';
    let resendCountdown;
    let otpCountdown;

    const urlParams = new URLSearchParams(window.location.search);

    function showAlert(msg, type = 'error') {
        const el = document.getElementById('authAlert');
        el.style.display = 'flex';
        el.innerText = msg;
        setTimeout(() => el.style.display = 'none', 5000);
    }

    function setLoading(btn, loading) {
        if (loading) {
            btn.disabled = true;
            btn.innerHTML = '<span class="login-loader"></span> Please wait...';
        } else {
            btn.disabled = false;
        }
    }

    // --- Email Form ---
    document.getElementById('emailForm').addEventListener('submit', async (e) => {
        e.preventDefault();
        const btn = document.getElementById('sendOtpBtn');
        userEmail = document.getElementById('email').value.trim();
        setLoading(btn, true);

        try {
            const res = await fetch(`${API_BASE}/user/send-otp`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                body: JSON.stringify({ email: userEmail }),
            });
            const data = await res.json();

            if (data.success) {
                document.getElementById('step-email').style.display = 'none';
                document.getElementById('step-otp').style.display = 'block';
                document.getElementById('d1').focus();
                startOtpTimer();
                startResendTimer();
                showAlert('OTP sent successfully.', 'success');
            } else {
                showAlert(data.message || 'Failed to send OTP.');
            }
        } catch (err) {
            showAlert('Network error. Please try again.');
        } finally {
            btn.disabled = false;
            btn.innerHTML = 'Begin Verification &nbsp;➔';
        }
    });

    // --- OTP Digit Auto-Tab ---
    document.querySelectorAll('.otp-digit').forEach((input, index, inputs) => {
        input.addEventListener('input', () => {
            input.value = input.value.replace(/\D/, '');
            if (input.value && index < inputs.length - 1) inputs[index + 1].focus();
        });
        input.addEventListener('keydown', (e) => {
            if (e.key === 'Backspace' && !input.value && index > 0) inputs[index - 1].focus();
        });
        input.addEventListener('paste', (e) => {
            const pasted = e.clipboardData.getData('text').replace(/\D/g, '').slice(0, 6);
            [...pasted].forEach((char, i) => { if (inputs[i]) inputs[i].value = char; });
            if (inputs[pasted.length - 1]) inputs[pasted.length - 1].focus();
            e.preventDefault();
        });
    });

    // --- OTP Form ---
    document.getElementById('otpForm').addEventListener('submit', async (e) => {
        e.preventDefault();
        const btn = document.getElementById('verifyOtpBtn');
        const otp = ['d1','d2','d3','d4','d5','d6'].map(id => document.getElementById(id).value).join('');

        if (otp.length < 6) { showAlert('Please enter all 6 digits.'); return; }
        setLoading(btn, true);

        try {
            const res = await fetch(`${API_BASE}/user/verify-otp`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                body: JSON.stringify({ email: userEmail, otp }),
            });
            const data = await res.json();

            if (data.success) {
                localStorage.setItem('user_token', data.token);
                localStorage.setItem('user_info', JSON.stringify(data.user));
                showAlert('Login successful! Redirecting...', 'success');
                setTimeout(() => window.location.href = '/dashboard', 1200);
            } else {
                showAlert(data.message || 'Invalid OTP.');
                btn.disabled = false;
                btn.innerHTML = 'Verify & Login &nbsp;➔';
            }
        } catch (err) {
            showAlert('Network error. Please try again.');
            btn.disabled = false;
            btn.innerHTML = 'Verify & Login &nbsp;➔';
        }
    });

    // --- Resend OTP Timer ---
    function startResendTimer() {
        let seconds = 30;
        const btn = document.getElementById('resendBtn');
        const timerEl = document.getElementById('resendTimer');
        btn.disabled = true;
        clearInterval(resendCountdown);
        resendCountdown = setInterval(() => {
            seconds--;
            timerEl.textContent = seconds;
            if (seconds <= 0) {
                clearInterval(resendCountdown);
                btn.disabled = false;
                btn.innerHTML = 'Resend OTP';
            }
        }, 1000);
    }

    document.getElementById('resendBtn').addEventListener('click', () => {
        document.getElementById('emailForm').dispatchEvent(new Event('submit'));
    });

    // --- OTP Expiry Timer ---
    function startOtpTimer() {
        let total = 600; // 10 minutes
        const el = document.getElementById('otpTimer');
        clearInterval(otpCountdown);
        otpCountdown = setInterval(() => {
            total--;
            const m = String(Math.floor(total / 60)).padStart(2, '0');
            const s = String(total % 60).padStart(2, '0');
            el.textContent = `${m}:${s}`;
            if (total <= 0) {
                clearInterval(otpCountdown);
                el.textContent = 'Expired';
                el.style.background = 'rgba(255,255,255,0.15)';
                el.style.color = '#fff';
            }
        }, 1000);
    }
</script>
</body>
</html>

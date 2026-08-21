<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Grow Capitals - Login</title>
    <meta name="description" content="Secure login to Grow Capitals Research portal using Email OTP." />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet" />
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --primary: #6C63FF;
            --primary-dark: #574fd6;
            --accent: #00D4AA;
            --bg: #0d0f1a;
            --card: rgba(255,255,255,0.05);
            --border: rgba(255,255,255,0.1);
            --text: #e8eaf6;
            --text-muted: #9e9eb0;
            --error: #ff6b6b;
            --success: #00D4AA;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--bg);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
            color: var(--text);
        }

        /* Animated background blobs */
        .bg-blob {
            position: absolute;
            border-radius: 50%;
            filter: blur(80px);
            opacity: 0.15;
            animation: float 8s ease-in-out infinite;
        }
        .bg-blob-1 { width: 400px; height: 400px; background: var(--primary); top: -100px; left: -100px; animation-delay: 0s; }
        .bg-blob-2 { width: 350px; height: 350px; background: var(--accent); bottom: -80px; right: -80px; animation-delay: 3s; }
        .bg-blob-3 { width: 250px; height: 250px; background: #ff6bcb; top: 50%; left: 60%; animation-delay: 5s; }

        @keyframes float {
            0%, 100% { transform: translateY(0) scale(1); }
            50% { transform: translateY(-30px) scale(1.05); }
        }

        .card {
            background: var(--card);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid var(--border);
            border-radius: 24px;
            padding: 48px 40px;
            width: 100%;
            max-width: 420px;
            position: relative;
            z-index: 10;
            box-shadow: 0 25px 50px rgba(0,0,0,0.5);
        }

        .logo {
            text-align: center;
            margin-bottom: 32px;
        }

        .logo-icon {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, var(--primary), var(--accent));
            border-radius: 18px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            margin-bottom: 16px;
            box-shadow: 0 8px 24px rgba(108,99,255,0.4);
        }

        .logo h1 {
            font-size: 22px;
            font-weight: 700;
            background: linear-gradient(135deg, var(--primary), var(--accent));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .logo p {
            font-size: 13px;
            color: var(--text-muted);
            margin-top: 4px;
        }

        h2 {
            font-size: 20px;
            font-weight: 600;
            margin-bottom: 8px;
        }

        .subtitle {
            font-size: 14px;
            color: var(--text-muted);
            margin-bottom: 28px;
            line-height: 1.5;
        }

        .form-group { margin-bottom: 20px; }

        label {
            display: block;
            font-size: 13px;
            font-weight: 500;
            color: var(--text-muted);
            margin-bottom: 8px;
            letter-spacing: 0.3px;
        }

        input {
            width: 100%;
            padding: 14px 16px;
            background: rgba(255,255,255,0.07);
            border: 1px solid var(--border);
            border-radius: 12px;
            color: var(--text);
            font-size: 15px;
            font-family: 'Inter', sans-serif;
            outline: none;
            transition: all 0.2s ease;
        }

        input:focus {
            border-color: var(--primary);
            background: rgba(108,99,255,0.1);
            box-shadow: 0 0 0 3px rgba(108,99,255,0.15);
        }

        input::placeholder { color: var(--text-muted); }

        /* OTP digit inputs */
        .otp-inputs {
            display: flex;
            gap: 10px;
            justify-content: center;
        }

        .otp-digit {
            width: 52px;
            height: 56px;
            text-align: center;
            font-size: 22px;
            font-weight: 600;
            border-radius: 12px;
            padding: 0;
        }

        .btn {
            width: 100%;
            padding: 15px;
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: #fff;
            border: none;
            border-radius: 12px;
            font-size: 15px;
            font-weight: 600;
            font-family: 'Inter', sans-serif;
            cursor: pointer;
            transition: all 0.25s ease;
            position: relative;
            overflow: hidden;
            letter-spacing: 0.3px;
        }

        .btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 8px 24px rgba(108,99,255,0.45);
        }

        .btn:active { transform: translateY(0); }

        .btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }

        .btn-loader {
            display: inline-block;
            width: 18px;
            height: 18px;
            border: 2px solid rgba(255,255,255,0.3);
            border-top-color: #fff;
            border-radius: 50%;
            animation: spin 0.7s linear infinite;
            vertical-align: middle;
            margin-right: 8px;
        }

        @keyframes spin { to { transform: rotate(360deg); } }

        .resend-link {
            text-align: center;
            margin-top: 16px;
            font-size: 13px;
            color: var(--text-muted);
        }

        .resend-link button {
            background: none;
            border: none;
            color: var(--primary);
            cursor: pointer;
            font-family: 'Inter', sans-serif;
            font-size: 13px;
            font-weight: 500;
            text-decoration: underline;
        }

        .resend-link button:disabled {
            color: var(--text-muted);
            text-decoration: none;
            cursor: default;
        }

        .alert {
            padding: 12px 16px;
            border-radius: 10px;
            font-size: 13px;
            margin-bottom: 18px;
            display: none;
            align-items: center;
            gap: 8px;
        }

        .alert-error   { background: rgba(255,107,107,0.15); border: 1px solid rgba(255,107,107,0.3); color: var(--error); }
        .alert-success { background: rgba(0,212,170,0.12); border: 1px solid rgba(0,212,170,0.3); color: var(--success); }

        .admin-link {
            text-align: center;
            margin-top: 24px;
            font-size: 12px;
            color: var(--text-muted);
        }

        .admin-link a {
            color: var(--text-muted);
            text-decoration: none;
            transition: color 0.2s;
        }

        .admin-link a:hover { color: var(--primary); }

        #step-otp { display: none; }

        .timer-badge {
            display: inline-block;
            background: rgba(108,99,255,0.15);
            border: 1px solid rgba(108,99,255,0.3);
            color: var(--primary);
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
        }
    </style>
</head>
<body>
    <div class="bg-blob bg-blob-1"></div>
    <div class="bg-blob bg-blob-2"></div>
    <div class="bg-blob bg-blob-3"></div>

    <div class="card">
        <div class="logo">
            <div class="logo-icon">📈</div>
            <h1>Grow Capitals</h1>
            <p>Research Platform</p>
        </div>

        <!-- Alert Box -->
        <div id="alert" class="alert"></div>

        <!-- Step 1: Enter Email -->
        <div id="step-email">
            <h2>Welcome Back 👋</h2>
            <p class="subtitle">Enter your email to receive a one-time password (OTP) to login or create your account.</p>

            <form id="emailForm">
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" placeholder="you@example.com" required autocomplete="email" />
                </div>
                <button type="submit" class="btn" id="sendOtpBtn">Send OTP</button>
            </form>
        </div>

        <!-- Step 2: Enter OTP -->
        <div id="step-otp">
            <h2>Check your inbox ✉️</h2>
            <p class="subtitle">We sent a 6-digit OTP to <strong id="displayEmail"></strong>. Enter it below.</p>

            <form id="otpForm">
                <div class="form-group">
                    <label>Enter OTP &nbsp;<span class="timer-badge" id="otpTimer">10:00</span></label>
                    <div class="otp-inputs">
                        <input class="otp-digit" type="text" maxlength="1" inputmode="numeric" pattern="[0-9]" id="d1" />
                        <input class="otp-digit" type="text" maxlength="1" inputmode="numeric" pattern="[0-9]" id="d2" />
                        <input class="otp-digit" type="text" maxlength="1" inputmode="numeric" pattern="[0-9]" id="d3" />
                        <input class="otp-digit" type="text" maxlength="1" inputmode="numeric" pattern="[0-9]" id="d4" />
                        <input class="otp-digit" type="text" maxlength="1" inputmode="numeric" pattern="[0-9]" id="d5" />
                        <input class="otp-digit" type="text" maxlength="1" inputmode="numeric" pattern="[0-9]" id="d6" />
                    </div>
                </div>
                <button type="submit" class="btn" id="verifyOtpBtn">Verify & Login</button>
            </form>

            <div class="resend-link">
                <span id="resendText">Didn't receive OTP? </span>
                <button id="resendBtn" disabled>Resend OTP (<span id="resendTimer">30</span>s)</button>
            </div>
        </div>

        <div class="admin-link">
            <a href="/admin/login">Admin Portal →</a>
        </div>
    </div>

<script>
    const API_BASE = '/api';
    let userEmail = '';
    let resendCountdown;
    let otpCountdown;

    function showAlert(msg, type = 'error') {
        const el = document.getElementById('alert');
        el.className = `alert alert-${type}`;
        el.innerHTML = `<span>${type === 'error' ? '❌' : '✅'}</span> ${msg}`;
        el.style.display = 'flex';
        setTimeout(() => el.style.display = 'none', 5000);
    }

    function setLoading(btn, loading) {
        if (loading) {
            btn.disabled = true;
            btn.innerHTML = '<span class="btn-loader"></span> Please wait...';
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
                document.getElementById('displayEmail').textContent = userEmail;
                document.getElementById('d1').focus();
                startOtpTimer();
                startResendTimer();
                showAlert('OTP sent to ' + userEmail, 'success');
            } else {
                showAlert(data.message || 'Failed to send OTP.');
            }
        } catch (err) {
            showAlert('Network error. Please try again.');
        } finally {
            btn.disabled = false;
            btn.innerHTML = 'Send OTP';
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
                btn.innerHTML = 'Verify & Login';
            }
        } catch (err) {
            showAlert('Network error. Please try again.');
            btn.disabled = false;
            btn.innerHTML = 'Verify & Login';
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
                el.style.background = 'rgba(255,107,107,0.15)';
                el.style.color = 'var(--error)';
            }
        }, 1000);
    }
</script>
</body>
</html>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Admin Login - Grow Capitals</title>
    <meta name="description" content="Admin portal login for Grow Capitals Research." />
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

        .main-title .green-text { color: var(--accent); }

        /* MAIN GRID */
        .portal-grid {
            display: grid;
            grid-template-columns: 1.3fr 1fr;
            gap: 40px;
            width: 100%;
        }

        /* LEFT VISUAL CARD */
        .left-visual-card {
            background: linear-gradient(135deg, rgba(11,45,73,0.7) 0%, rgba(11,45,73,0.25) 100%),
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

        /* RIGHT CARD — Admin Login (Brand Accent) */
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

        /* Form */
        .form-container { flex: 1; margin-bottom: 32px; }
        .form-group { margin-bottom: 20px; }

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

        .right-login-card input::placeholder { color: #94a3b8; }

        .password-wrap { position: relative; }
        .toggle-pass {
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #94a3b8;
            cursor: pointer;
            font-size: 18px;
            padding: 0;
            display: flex;
            transition: color 0.2s;
        }
        .toggle-pass:hover { color: var(--text-dark); }

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
            font-family: inherit;
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

        .btn-loader {
            display: inline-block;
            width: 16px; height: 16px;
            border: 2px solid rgba(255,255,255,0.3);
            border-top-color: #fff;
            border-radius: 50%;
            animation: spin 0.7s linear infinite;
            vertical-align: middle;
        }
        @keyframes spin { to { transform: rotate(360deg); } }

        /* Alert */
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

        .auth-alert.error {
            background: rgba(248,113,113,0.15);
            border-color: rgba(248,113,113,0.35);
        }

        .auth-alert.success {
            background: rgba(52,211,153,0.15);
            border-color: rgba(52,211,153,0.35);
        }

        /* Card bottom */
        .card-bottom-desc {
            font-size: 11px;
            color: rgba(11,45,73,0.85);
            line-height: 1.5;
            border-top: 1px solid rgba(11,45,73,0.1);
            padding-top: 18px;
        }

        .card-bottom-desc a {
            color: var(--primary);
            font-weight: 700;
            text-decoration: none;
            transition: opacity 0.2s;
        }

        .card-bottom-desc a:hover { opacity: 0.7; }

        /* Responsive */
        @media (max-width: 992px) {
            body { padding: 40px 0; }
            .container { padding: 0 20px; }
            .top-nav { flex-direction: column-reverse; gap: 20px; align-items: flex-start; margin-bottom: 40px; }
            .main-title { font-size: 24px; line-height: 1.3; }
            .portal-grid { grid-template-columns: 1fr; }
            .left-visual-card { display: none; }
            .right-login-card { padding: 32px 24px; min-height: auto; border-radius: 24px; }
            .card-top-badge { margin-bottom: 16px; }
            .card-title { font-size: 20px; margin-bottom: 22px; }
            .right-login-card label { font-size: 10px; margin-bottom: 6px; }
            .right-login-card input { padding: 13px 15px; font-size: 14px; border-radius: 10px; }
            .btn-submit { padding: 13px; font-size: 14px; margin-top: 6px; }
            .card-bottom-desc { font-size: 10px; margin-top: 24px; padding-top: 14px; }
        }
    </style>
</head>
<body>

<div class="container">

<!-- TOP NAVIGATION -->
<div class="top-nav">
    <h1 class="main-title">Administration <span class="green-text">Control Panel</span></h1>
    <div class="logo-wrap">
        <img src="{{ asset('grologo.png') }}" alt="Grow Capital Logo" style="height: 52px; width: auto; object-fit: contain; display: block;" />
    </div>
</div>

<!-- MAIN GRID -->
<div class="portal-grid">

    <!-- LEFT CARD (Visual Panel) -->
    <div class="left-visual-card">
        <div class="left-overlay-text">
            <h3>Manage<br/>Configure<br/>Monitor</h3>
            <p>Secure admin access to manage users, KYC approvals, API integrations, and KRA compliance settings.</p>
        </div>
        <div class="floating-logo-badge">
            <img src="{{ asset('grologo.png') }}" alt="Grow Capital Logo" />
        </div>
    </div>

    <!-- RIGHT CARD (Lime Green Login Panel) -->
    <div class="right-login-card">
        <div>
            <div class="card-top-badge">🔒 Secure Admin Access</div>
            <h3 class="card-title">Admin Sign In</h3>

            <!-- Alert -->
            <div id="authAlert" class="auth-alert"></div>

            <div class="form-container">
                <form id="adminLoginForm">
                    <div class="form-group">
                        <label for="admin-email">Email Address</label>
                        <input type="email" id="admin-email" name="email" placeholder="admin@growcapitals.com" required autocomplete="email" />
                    </div>

                    <div class="form-group">
                        <label for="admin-password">Password</label>
                        <div class="password-wrap">
                            <input type="password" id="admin-password" name="password" placeholder="Enter your password" required autocomplete="current-password" />
                            <button type="button" class="toggle-pass" id="togglePass" title="Show/Hide Password">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                            </button>
                        </div>
                    </div>

                    <button type="submit" class="btn-submit" id="loginBtn">
                        Sign In &nbsp;➔
                    </button>
                </form>

                <div style="text-align:center; margin-top:16px;">
                    <a href="#" id="forgotPasswordLink" style="font-size:13px; color:var(--primary); font-weight:600; text-decoration:none;">Forgot Password?</a>
                </div>

                <!-- Forgot Password Form (hidden by default) -->
                <div id="forgotPasswordSection" style="display:none; margin-top:20px; padding-top:20px; border-top:1px solid rgba(11,45,73,0.1);">
                    <h4 style="font-size:15px; font-weight:700; color:var(--text-dark); margin-bottom:12px;">Reset Your Password</h4>
                    <p style="font-size:12px; color:#64748b; margin-bottom:16px;">Enter your admin email. We'll send you a link to reset your password.</p>
                    <div id="forgotAlert" class="auth-alert"></div>
                    <form id="forgotPasswordForm">
                        <div class="form-group">
                            <label for="forgot-email">Email Address</label>
                            <input type="email" id="forgot-email" placeholder="admin@growcapitals.com" required autocomplete="email" />
                        </div>
                        <button type="submit" class="btn-submit" id="forgotBtn" style="background:#0b2d49;">
                            Send Reset Link
                        </button>
                    </form>
                    <div style="text-align:center; margin-top:12px;">
                        <a href="#" id="backToLogin" style="font-size:12px; color:var(--primary); font-weight:600; text-decoration:none;">← Back to Sign In</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="card-bottom-desc">
            🔒 This is a restricted area for authorized administrators only. All access attempts are logged and monitored.
            <br/><br/>
            <a href="/login">← Back to User Portal</a>
        </div>
    </div>
</div>

</div>

<script>
    // If already logged in, skip to dashboard
    if (localStorage.getItem('admin_token')) {
        window.location.href = '/admin/dashboard';
    }

    const API_BASE = '/api';

    function showAlert(msg, type = 'error') {
        const el = document.getElementById('authAlert');
        el.className = `auth-alert ${type}`;
        el.innerHTML = `<span>${type === 'error' ? '❌' : '✅'}</span> ${msg}`;
        el.style.display = 'flex';
    }

    document.getElementById('togglePass').addEventListener('click', () => {
        const inp = document.getElementById('admin-password');
        inp.type = inp.type === 'password' ? 'text' : 'password';
    });

    document.getElementById('adminLoginForm').addEventListener('submit', async (e) => {
        e.preventDefault();
        const btn  = document.getElementById('loginBtn');
        const email = document.getElementById('admin-email').value.trim();
        const password = document.getElementById('admin-password').value;

        btn.disabled = true;
        btn.innerHTML = '<span class="btn-loader"></span> Signing in...';

        try {
            const res = await fetch(`${API_BASE}/admin/login`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                body: JSON.stringify({ email, password }),
            });
            const data = await res.json();

            if (data.success) {
                localStorage.setItem('admin_token', data.token);
                localStorage.setItem('admin_info', JSON.stringify(data.admin));
                showAlert('Login successful! Redirecting...', 'success');
                setTimeout(() => window.location.href = '/admin/dashboard', 1000);
            } else {
                showAlert(data.message || 'Invalid credentials.');
            }
        } catch (err) {
            showAlert('Network error. Please try again.');
        } finally {
            btn.disabled = false;
            btn.innerHTML = 'Sign In &nbsp;➔';
        }
    });

    // Forgot password toggle
    document.getElementById('forgotPasswordLink').addEventListener('click', (e) => {
        e.preventDefault();
        document.getElementById('adminLoginForm').style.display = 'none';
        document.getElementById('forgotPasswordLink').style.display = 'none';
        document.getElementById('forgotPasswordSection').style.display = 'block';
    });
    document.getElementById('backToLogin').addEventListener('click', (e) => {
        e.preventDefault();
        document.getElementById('adminLoginForm').style.display = 'block';
        document.getElementById('forgotPasswordLink').style.display = 'inline';
        document.getElementById('forgotPasswordSection').style.display = 'none';
    });

    // Forgot password submit
    document.getElementById('forgotPasswordForm').addEventListener('submit', async (e) => {
        e.preventDefault();
        const btn = document.getElementById('forgotBtn');
        const email = document.getElementById('forgot-email').value.trim();
        const alertEl = document.getElementById('forgotAlert');

        btn.disabled = true;
        btn.innerHTML = '<span class="btn-loader"></span> Sending...';

        try {
            const res = await fetch(`${API_BASE}/admin/forgot-password`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                body: JSON.stringify({ email }),
            });
            const data = await res.json();

            if (data.success) {
                alertEl.className = 'auth-alert success';
                alertEl.innerHTML = '✅ ' + data.message;
                alertEl.style.display = 'flex';
                document.getElementById('forgotPasswordForm').style.display = 'none';
            } else {
                alertEl.className = 'auth-alert error';
                alertEl.innerHTML = '❌ ' + (data.message || 'Failed to send reset email.');
                alertEl.style.display = 'flex';
            }
        } catch (err) {
            alertEl.className = 'auth-alert error';
            alertEl.innerHTML = '❌ Network error. Please try again.';
            alertEl.style.display = 'flex';
        } finally {
            btn.disabled = false;
            btn.innerHTML = 'Send Reset Link';
        }
    });
</script>
</body>
</html>

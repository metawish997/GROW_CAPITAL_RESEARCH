<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Admin Login - Grow Capitals</title>
    <meta name="description" content="Admin portal login for Grow Capitals Research." />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet" />
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --primary: #f59e0b;
            --primary-dark: #d97706;
            --accent: #ef4444;
            --bg: #0a0c14;
            --card: rgba(255,255,255,0.04);
            --border: rgba(255,255,255,0.08);
            --text: #f1f5f9;
            --text-muted: #94a3b8;
            --error: #f87171;
            --success: #34d399;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--bg);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            color: var(--text);
            position: relative;
        }

        .bg-grid {
            position: absolute;
            inset: 0;
            background-image:
                linear-gradient(rgba(245,158,11,0.04) 1px, transparent 1px),
                linear-gradient(90deg, rgba(245,158,11,0.04) 1px, transparent 1px);
            background-size: 40px 40px;
        }

        .bg-blob { position: absolute; border-radius: 50%; filter: blur(90px); opacity: 0.12; animation: float 10s ease-in-out infinite; }
        .bg-blob-1 { width: 500px; height: 500px; background: var(--primary); top: -150px; right: -100px; animation-delay: 0s; }
        .bg-blob-2 { width: 400px; height: 400px; background: #7c3aed; bottom: -100px; left: -80px; animation-delay: 4s; }

        @keyframes float { 0%, 100% { transform: translateY(0); } 50% { transform: translateY(-25px); } }

        .card {
            background: var(--card);
            backdrop-filter: blur(24px);
            -webkit-backdrop-filter: blur(24px);
            border: 1px solid var(--border);
            border-radius: 24px;
            padding: 48px 44px;
            width: 100%;
            max-width: 420px;
            position: relative;
            z-index: 10;
            box-shadow: 0 30px 60px rgba(0,0,0,0.6);
        }

        .admin-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: rgba(245,158,11,0.12);
            border: 1px solid rgba(245,158,11,0.25);
            color: var(--primary);
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
            letter-spacing: 1px;
            text-transform: uppercase;
            margin-bottom: 24px;
        }

        .logo { margin-bottom: 32px; }
        .logo-icon {
            width: 56px;
            height: 56px;
            background: linear-gradient(135deg, var(--primary), var(--accent));
            border-radius: 16px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 26px;
            margin-bottom: 16px;
            box-shadow: 0 8px 20px rgba(245,158,11,0.3);
        }

        h1 {
            font-size: 22px;
            font-weight: 700;
            background: linear-gradient(135deg, var(--primary), #fcd34d);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        h2 { font-size: 20px; font-weight: 600; margin-bottom: 6px; }

        .subtitle {
            font-size: 14px;
            color: var(--text-muted);
            margin-bottom: 28px;
            line-height: 1.5;
        }

        .form-group { margin-bottom: 20px; }

        label {
            display: block;
            font-size: 12px;
            font-weight: 600;
            color: var(--text-muted);
            margin-bottom: 8px;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }

        input {
            width: 100%;
            padding: 14px 16px;
            background: rgba(255,255,255,0.06);
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
            background: rgba(245,158,11,0.08);
            box-shadow: 0 0 0 3px rgba(245,158,11,0.12);
        }

        input::placeholder { color: var(--text-muted); }

        .password-wrap { position: relative; }
        .toggle-pass {
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: var(--text-muted);
            cursor: pointer;
            font-size: 18px;
            padding: 0;
            display: flex;
        }

        .btn {
            width: 100%;
            padding: 15px;
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: #000;
            border: none;
            border-radius: 12px;
            font-size: 15px;
            font-weight: 700;
            font-family: 'Inter', sans-serif;
            cursor: pointer;
            transition: all 0.25s ease;
            letter-spacing: 0.3px;
        }

        .btn:hover { transform: translateY(-1px); box-shadow: 0 8px 24px rgba(245,158,11,0.4); }
        .btn:active { transform: translateY(0); }
        .btn:disabled { opacity: 0.6; cursor: not-allowed; transform: none; }

        .btn-loader {
            display: inline-block;
            width: 16px; height: 16px;
            border: 2px solid rgba(0,0,0,0.3);
            border-top-color: #000;
            border-radius: 50%;
            animation: spin 0.7s linear infinite;
            vertical-align: middle;
            margin-right: 8px;
        }

        @keyframes spin { to { transform: rotate(360deg); } }

        .alert {
            padding: 12px 16px;
            border-radius: 10px;
            font-size: 13px;
            margin-bottom: 18px;
            display: none;
            align-items: center;
            gap: 8px;
        }

        .alert-error   { background: rgba(248,113,113,0.12); border: 1px solid rgba(248,113,113,0.3); color: var(--error); }
        .alert-success { background: rgba(52,211,153,0.12); border: 1px solid rgba(52,211,153,0.3); color: var(--success); }

        .user-link {
            text-align: center;
            margin-top: 24px;
            font-size: 12px;
            color: var(--text-muted);
        }

        .user-link a {
            color: var(--text-muted);
            text-decoration: none;
            transition: color 0.2s;
        }

        .user-link a:hover { color: var(--primary); }

        .security-note {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-top: 20px;
            padding: 10px 14px;
            background: rgba(255,255,255,0.03);
            border-radius: 10px;
            font-size: 11px;
            color: var(--text-muted);
        }
    </style>
</head>
<body>
    <div class="bg-grid"></div>
    <div class="bg-blob bg-blob-1"></div>
    <div class="bg-blob bg-blob-2"></div>

    <div class="card">
        <div class="admin-badge">⚡ Admin Portal</div>

        <div class="logo">
            <div class="logo-icon">📊</div>
            <h1>Grow Capitals</h1>
        </div>

        <h2>Admin Sign In</h2>
        <p class="subtitle">Access the administration panel with your credentials.</p>

        <div id="alert" class="alert"></div>

        <form id="adminLoginForm">
            <div class="form-group">
                <label for="admin-email">Email Address</label>
                <input type="email" id="admin-email" name="email" placeholder="admin@example.com" required autocomplete="email" />
            </div>

            <div class="form-group">
                <label for="admin-password">Password</label>
                <div class="password-wrap">
                    <input type="password" id="admin-password" name="password" placeholder="Enter password" required autocomplete="current-password" />
                    <button type="button" class="toggle-pass" id="togglePass" title="Show/Hide Password">👁</button>
                </div>
            </div>

            <button type="submit" class="btn" id="loginBtn">Sign In to Admin</button>
        </form>

        <div class="security-note">
            🔒 This is a secure admin-only area. Unauthorized access is prohibited.
        </div>

        <div class="user-link">
            <a href="/login">← User Login</a>
        </div>
    </div>

<script>
    const API_BASE = '/api';

    function showAlert(msg, type = 'error') {
        const el = document.getElementById('alert');
        el.className = `alert alert-${type}`;
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
            btn.innerHTML = 'Sign In to Admin';
        }
    });
</script>
</body>
</html>

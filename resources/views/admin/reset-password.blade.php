<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Reset Password - Grow Capitals</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet" />
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        :root { --primary: #004b87; --primary-dark: #003764; --text-dark: #0b2d49; --border: #e2e8f0; }
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: #f8fafc;
            color: var(--text-dark);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
        }
        .card {
            background: #ffffff;
            border: 1px solid var(--border);
            border-radius: 20px;
            padding: 40px;
            max-width: 440px;
            width: 100%;
            box-shadow: 0 4px 24px rgba(0,0,0,0.06);
        }
        .card-logo { text-align: center; margin-bottom: 24px; }
        .card-logo img { height: 48px; }
        .card h2 { font-size: 22px; font-weight: 700; margin-bottom: 6px; text-align: center; color: var(--text-dark); }
        .card p { font-size: 13px; color: #64748b; text-align: center; margin-bottom: 28px; }
        .form-group { display: flex; flex-direction: column; gap: 7px; margin-bottom: 20px; }
        .form-group label { font-size: 12px; font-weight: 600; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; }
        .form-group input {
            padding: 13px 16px;
            background: #ffffff;
            border: 1px solid var(--border);
            border-radius: 10px;
            color: var(--text-dark);
            font-size: 14px;
            font-family: inherit;
            outline: none;
            transition: all 0.2s;
        }
        .form-group input:focus { border-color: var(--primary); box-shadow: 0 0 0 3px rgba(0,75,135,0.08); }
        .btn-submit {
            width: 100%;
            padding: 14px;
            background: var(--primary);
            color: #ffffff;
            border: none;
            border-radius: 50px;
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s;
            font-family: inherit;
            margin-top: 8px;
        }
        .btn-submit:hover { background: var(--primary-dark); transform: translateY(-1px); }
        .btn-submit:disabled { opacity: 0.6; cursor: not-allowed; transform: none; }
        .alert {
            padding: 12px;
            border-radius: 8px;
            font-size: 13px;
            margin-bottom: 16px;
            display: none;
            text-align: center;
            font-weight: 500;
        }
        .alert.error { background: rgba(248,113,113,0.15); border: 1px solid rgba(248,113,113,0.35); color: #dc2626; display: block; }
        .alert.success { background: rgba(52,211,153,0.15); border: 1px solid rgba(52,211,153,0.35); color: #059669; display: block; }
        .back-link { display: block; text-align: center; margin-top: 20px; font-size: 13px; color: var(--primary); text-decoration: none; font-weight: 600; }
        .back-link:hover { opacity: 0.7; }

        @media (max-width: 480px) {
            .card { padding: 24px 20px; border-radius: 16px; }
            .card h2 { font-size: 18px; }
            .card p { font-size: 12px; }
            .form-group label { font-size: 10px; }
            .form-group input { font-size: 13px; padding: 11px 14px; }
            .btn-submit { font-size: 14px; padding: 12px; }
        }
    </style>
</head>
<body>

<div class="card">
    <div class="card-logo">
        <img src="{{ asset('grologo.png') }}" alt="Grow Capitals" />
    </div>
    <h2>Set New Password</h2>
    <p>Enter and confirm your new password below.</p>

    <div id="alertBox" class="alert"></div>

    <form id="resetForm">
        <input type="hidden" id="resetToken" />
        <input type="hidden" id="resetEmail" />

        <div class="form-group">
            <label for="new_password">New Password</label>
            <input type="password" id="new_password" placeholder="Enter new password (min 6 chars)" required minlength="6" autocomplete="new-password" />
        </div>
        <div class="form-group">
            <label for="password_confirmation">Confirm Password</label>
            <input type="password" id="password_confirmation" placeholder="Re-enter new password" required minlength="6" autocomplete="new-password" />
        </div>
        <button type="submit" class="btn-submit" id="submitBtn">Reset Password</button>
    </form>
    <a href="/admin/login" class="back-link">← Back to Sign In</a>
</div>

<script>
    // Extract token & email from URL
    const params = new URLSearchParams(window.location.search);
    const token = params.get('token');
    const email = params.get('email');

    if (!token || !email) {
        document.getElementById('alertBox').className = 'alert error';
        document.getElementById('alertBox').textContent = 'Invalid reset link. Please request a new one.';
        document.getElementById('resetForm').style.display = 'none';
    } else {
        document.getElementById('resetToken').value = token;
        document.getElementById('resetEmail').value = email;
    }

    document.getElementById('resetForm').addEventListener('submit', async (e) => {
        e.preventDefault();
        const btn = document.getElementById('submitBtn');
        const alertBox = document.getElementById('alertBox');
        const password = document.getElementById('new_password').value;
        const confirmation = document.getElementById('password_confirmation').value;

        if (password !== confirmation) {
            alertBox.className = 'alert error';
            alertBox.textContent = 'Passwords do not match.';
            return;
        }

        btn.disabled = true;
        btn.textContent = 'Resetting...';

        try {
            const res = await fetch('/api/admin/reset-password', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                body: JSON.stringify({
                    email: document.getElementById('resetEmail').value,
                    token: document.getElementById('resetToken').value,
                    password: password,
                    password_confirmation: confirmation
                })
            });
            const data = await res.json();

            if (data.success) {
                alertBox.className = 'alert success';
                alertBox.textContent = data.message;
                document.getElementById('resetForm').style.display = 'none';
            } else {
                alertBox.className = 'alert error';
                alertBox.textContent = data.message || 'Failed to reset password.';
            }
        } catch (err) {
            alertBox.className = 'alert error';
            alertBox.textContent = 'Network error. Please try again.';
        } finally {
            btn.disabled = false;
            btn.textContent = 'Reset Password';
        }
    });
</script>
</body>
</html>

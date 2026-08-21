<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>API Settings - Admin | Grow Capitals</title>
    <meta name="description" content="Manage SMTP, SMS, Digio, and Razorpay API credentials from the Admin panel." />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet" />
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --primary: #6C63FF;
            --amber: #f59e0b;
            --green: #10b981;
            --red: #ef4444;
            --bg: #080a12;
            --sidebar-bg: #0d0f1c;
            --card: rgba(255,255,255,0.04);
            --card-hover: rgba(255,255,255,0.06);
            --border: rgba(255,255,255,0.08);
            --text: #e8eaf6;
            --text-muted: #8890a6;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--bg);
            color: var(--text);
            min-height: 100vh;
            display: flex;
        }

        /* --- SIDEBAR --- */
        .sidebar {
            width: 260px;
            min-height: 100vh;
            background: var(--sidebar-bg);
            border-right: 1px solid var(--border);
            display: flex;
            flex-direction: column;
            position: fixed;
            top: 0;
            left: 0;
            bottom: 0;
            z-index: 100;
        }

        .sidebar-logo {
            padding: 24px 20px;
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .sidebar-logo-icon {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, var(--primary), #00D4AA);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
        }

        .sidebar-logo-text h3 { font-size: 16px; font-weight: 700; }
        .sidebar-logo-text span { font-size: 11px; color: var(--text-muted); }

        .sidebar-nav { flex: 1; padding: 16px 12px; }

        .nav-section-label {
            font-size: 10px;
            font-weight: 600;
            letter-spacing: 1px;
            text-transform: uppercase;
            color: var(--text-muted);
            padding: 12px 10px 6px;
        }

        .nav-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 12px;
            border-radius: 10px;
            margin-bottom: 2px;
            cursor: pointer;
            transition: all 0.2s;
            text-decoration: none;
            color: var(--text-muted);
            font-size: 14px;
        }

        .nav-item:hover { background: var(--card); color: var(--text); }
        .nav-item.active { background: rgba(108,99,255,0.15); color: var(--primary); font-weight: 600; }

        .nav-item .icon { font-size: 18px; width: 22px; text-align: center; }

        .sidebar-footer {
            padding: 16px 12px;
            border-top: 1px solid var(--border);
        }

        .admin-user-badge {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 12px;
            background: var(--card);
            border-radius: 12px;
        }

        .admin-avatar {
            width: 34px;
            height: 34px;
            background: linear-gradient(135deg, var(--amber), var(--primary));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            font-weight: 600;
            color: #000;
        }

        .admin-info { flex: 1; }
        .admin-info .name { font-size: 13px; font-weight: 600; }
        .admin-info .role { font-size: 11px; color: var(--amber); }

        .logout-btn {
            background: none;
            border: none;
            color: var(--text-muted);
            cursor: pointer;
            font-size: 18px;
            padding: 4px;
            transition: color 0.2s;
        }
        .logout-btn:hover { color: var(--red); }

        /* --- MAIN CONTENT --- */
        .main {
            margin-left: 260px;
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        /* --- HEADER --- */
        .header {
            height: 64px;
            background: rgba(8,10,18,0.95);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 32px;
            position: sticky;
            top: 0;
            z-index: 50;
        }

        .header-left h2 { font-size: 18px; font-weight: 600; }
        .header-left p  { font-size: 12px; color: var(--text-muted); margin-top: 2px; }

        .header-right { display: flex; align-items: center; gap: 12px; }

        .header-badge {
            padding: 5px 12px;
            background: rgba(108,99,255,0.12);
            border: 1px solid rgba(108,99,255,0.25);
            border-radius: 20px;
            font-size: 12px;
            color: var(--primary);
            font-weight: 500;
        }

        /* --- PAGE CONTENT --- */
        .content { padding: 32px; }

        .page-header { margin-bottom: 28px; }
        .page-header h1 { font-size: 24px; font-weight: 700; }
        .page-header p  { font-size: 14px; color: var(--text-muted); margin-top: 6px; }

        /* --- TABS --- */
        .tabs {
            display: flex;
            gap: 6px;
            margin-bottom: 28px;
            background: var(--card);
            border: 1px solid var(--border);
            padding: 6px;
            border-radius: 14px;
            width: fit-content;
        }

        .tab-btn {
            padding: 9px 20px;
            border-radius: 10px;
            border: none;
            background: transparent;
            color: var(--text-muted);
            font-size: 13px;
            font-weight: 500;
            font-family: 'Inter', sans-serif;
            cursor: pointer;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            gap: 7px;
        }

        .tab-btn:hover { color: var(--text); background: rgba(255,255,255,0.05); }
        .tab-btn.active { background: var(--primary); color: #fff; font-weight: 600; }

        .status-dot {
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: var(--text-muted);
        }

        .status-dot.configured { background: var(--green); }

        /* --- SETTING PANELS --- */
        .settings-panel { display: none; }
        .settings-panel.active { display: block; }

        .panel-card {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 20px;
            padding: 32px;
            max-width: 700px;
        }

        .panel-title {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 24px;
        }

        .panel-icon {
            width: 48px;
            height: 48px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
        }

        .panel-icon.smtp     { background: rgba(108,99,255,0.15); }
        .panel-icon.sms      { background: rgba(16,185,129,0.15); }
        .panel-icon.digio    { background: rgba(59,130,246,0.15); }
        .panel-icon.razorpay { background: rgba(245,158,11,0.15); }

        .panel-title-text h3 { font-size: 18px; font-weight: 600; }
        .panel-title-text p  { font-size: 13px; color: var(--text-muted); margin-top: 2px; }

        .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
        .form-grid .full { grid-column: 1 / -1; }

        .form-group { display: flex; flex-direction: column; gap: 7px; }

        .form-group label {
            font-size: 12px;
            font-weight: 600;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .form-group input,
        .form-group select {
            padding: 12px 14px;
            background: rgba(255,255,255,0.06);
            border: 1px solid var(--border);
            border-radius: 10px;
            color: var(--text);
            font-size: 14px;
            font-family: 'Inter', sans-serif;
            outline: none;
            transition: all 0.2s;
        }

        .form-group select option { background: #1a1d2e; }

        .form-group input:focus,
        .form-group select:focus {
            border-color: var(--primary);
            background: rgba(108,99,255,0.08);
            box-shadow: 0 0 0 3px rgba(108,99,255,0.12);
        }

        .form-group input::placeholder { color: var(--text-muted); }

        .btn-save {
            margin-top: 24px;
            padding: 13px 32px;
            background: linear-gradient(135deg, var(--primary), #574fd6);
            color: #fff;
            border: none;
            border-radius: 12px;
            font-size: 14px;
            font-weight: 600;
            font-family: 'Inter', sans-serif;
            cursor: pointer;
            transition: all 0.25s;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-save:hover { transform: translateY(-1px); box-shadow: 0 6px 20px rgba(108,99,255,0.4); }
        .btn-save:disabled { opacity: 0.6; cursor: not-allowed; transform: none; }

        .toast {
            position: fixed;
            bottom: 28px;
            right: 28px;
            padding: 14px 20px;
            border-radius: 12px;
            font-size: 14px;
            font-weight: 500;
            display: none;
            align-items: center;
            gap: 10px;
            z-index: 9999;
            box-shadow: 0 8px 24px rgba(0,0,0,0.5);
            animation: slideUp 0.3s ease;
        }

        @keyframes slideUp { from { transform: translateY(20px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }

        .toast-success { background: rgba(16,185,129,0.15); border: 1px solid rgba(16,185,129,0.3); color: #34d399; display: flex; }
        .toast-error   { background: rgba(239,68,68,0.15);  border: 1px solid rgba(239,68,68,0.3);  color: #f87171; display: flex; }

        .divider { border: none; border-top: 1px solid var(--border); margin: 24px 0; }

        .info-note {
            display: flex;
            gap: 10px;
            padding: 12px 16px;
            background: rgba(108,99,255,0.08);
            border: 1px solid rgba(108,99,255,0.2);
            border-radius: 10px;
            font-size: 12px;
            color: var(--text-muted);
            margin-bottom: 20px;
        }

        /* --- EYE TOGGLE WRAPPER --- */
        .pw-wrap {
            position: relative;
            display: flex;
            align-items: center;
        }

        .pw-wrap input {
            padding-right: 44px !important;
            width: 100%;
        }

        .eye-btn {
            position: absolute;
            right: 12px;
            background: none;
            border: none;
            cursor: pointer;
            color: var(--text-muted);
            padding: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: color 0.2s;
            border-radius: 6px;
            flex-shrink: 0;
        }

        .eye-btn:hover { color: var(--primary); }

        .eye-btn svg { width: 18px; height: 18px; stroke: currentColor; fill: none; stroke-width: 2; stroke-linecap: round; stroke-linejoin: round; }
    </style>
</head>
<body>

<!-- SIDEBAR -->
<aside class="sidebar">
    <div class="sidebar-logo">
        <div class="sidebar-logo-icon">📈</div>
        <div class="sidebar-logo-text">
            <h3>Grow Capitals</h3>
            <span>Admin Panel</span>
        </div>
    </div>

    <nav class="sidebar-nav">
        <div class="nav-section-label">Main</div>
        <a href="/admin/dashboard" class="nav-item">
            <span class="icon">🏠</span> Dashboard
        </a>
        <a href="/admin/users" class="nav-item">
            <span class="icon">👥</span> Users
        </a>

        <div class="nav-section-label">Configuration</div>
        <a href="/admin/settings" class="nav-item active">
            <span class="icon">⚙️</span> API Settings
        </a>
    </nav>

    <div class="sidebar-footer">
        <div class="admin-user-badge">
            <div class="admin-avatar" id="sidebarAvatar">A</div>
            <div class="admin-info">
                <div class="name" id="sidebarName">Admin</div>
                <div class="role">Super Admin</div>
            </div>
            <button class="logout-btn" id="sidebarLogout" title="Logout">⏻</button>
        </div>
    </div>
</aside>

<!-- MAIN CONTENT -->
<div class="main">

    <!-- HEADER -->
    <header class="header">
        <div class="header-left">
            <h2>API Settings</h2>
            <p>Manage integration credentials stored in database</p>
        </div>
        <div class="header-right">
            <span class="header-badge">🔒 Secure DB Storage</span>
        </div>
    </header>

    <!-- CONTENT -->
    <div class="content">
        <div class="page-header">
            <h1>Integration Settings</h1>
            <p>All credentials are encrypted and stored in the database — not in environment files.</p>
        </div>

        <!-- TABS -->
        <div class="tabs" role="tablist">
            <button class="tab-btn active" id="tab-smtp" onclick="switchTab('smtp')" role="tab">
                <span class="status-dot" id="dot-smtp"></span> 📧 SMTP
            </button>
            <button class="tab-btn" id="tab-sms" onclick="switchTab('sms')" role="tab">
                <span class="status-dot" id="dot-sms"></span> 📱 SMS Panel
            </button>
            <button class="tab-btn" id="tab-digio" onclick="switchTab('digio')" role="tab">
                <span class="status-dot" id="dot-digio"></span> 📝 Digio
            </button>
            <button class="tab-btn" id="tab-razorpay" onclick="switchTab('razorpay')" role="tab">
                <span class="status-dot" id="dot-razorpay"></span> 💳 Razorpay
            </button>
        </div>

        <!-- SMTP PANEL -->
        <div id="panel-smtp" class="settings-panel active">
            <div class="panel-card">
                <div class="panel-title">
                    <div class="panel-icon smtp">📧</div>
                    <div class="panel-title-text">
                        <h3>SMTP Email Configuration</h3>
                        <p>Used to send OTP emails, transactional notifications, and alerts.</p>
                    </div>
                </div>
                <div class="info-note">ℹ️ These credentials will be used to dynamically configure the mailer at runtime — no .env changes needed.</div>
                <form id="form-smtp" onsubmit="saveSettings(event, 'smtp')">
                    <div class="form-grid">
                        <div class="form-group">
                            <label>SMTP Host</label>
                            <input type="text" name="host" id="smtp-host" placeholder="smtp.gmail.com" />
                        </div>
                        <div class="form-group">
                            <label>Port</label>
                            <input type="number" name="port" id="smtp-port" placeholder="587" />
                        </div>
                        <div class="form-group">
                            <label>Username</label>
                            <input type="email" name="username" id="smtp-username" placeholder="yourmail@gmail.com" />
                        </div>
                        <div class="form-group">
                            <label>Password / App Password</label>
                            <div class="pw-wrap">
                                <input type="password" name="password" id="smtp-password" placeholder="••••••••" />
                                <button type="button" class="eye-btn" onclick="togglePw('smtp-password', this)" title="Show/Hide">
                                    <svg id="eye-smtp-password" viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                </button>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Encryption</label>
                            <select name="encryption" id="smtp-encryption">
                                <option value="tls">TLS</option>
                                <option value="ssl">SSL</option>
                                <option value="">None</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>From Email</label>
                            <input type="email" name="from_address" id="smtp-from_address" placeholder="noreply@growcapitals.com" />
                        </div>
                        <div class="form-group full">
                            <label>From Name</label>
                            <input type="text" name="from_name" id="smtp-from_name" placeholder="Grow Capitals Research" />
                        </div>
                    </div>
                    <button type="submit" class="btn-save" id="save-smtp">💾 Save SMTP Settings</button>
                </form>
            </div>
        </div>

        <!-- SMS PANEL -->
        <div id="panel-sms" class="settings-panel">
            <div class="panel-card">
                <div class="panel-title">
                    <div class="panel-icon sms">📱</div>
                    <div class="panel-title-text">
                        <h3>SMS Panel Configuration</h3>
                        <p>Configure your SMS provider for OTP messages and notifications.</p>
                    </div>
                </div>
                <div class="info-note">ℹ️ Supports any SMS provider (Fast2SMS, TextLocal, MSG91, Twilio, etc.).</div>
                <form id="form-sms" onsubmit="saveSettings(event, 'sms')">
                    <div class="form-grid">
                        <div class="form-group full">
                            <label>Provider Name</label>
                            <input type="text" name="provider" id="sms-provider" placeholder="e.g. Fast2SMS, MSG91, Twilio" />
                        </div>
                        <div class="form-group full">
                            <label>API Key</label>
                            <div class="pw-wrap">
                                <input type="password" name="api_key" id="sms-api_key" placeholder="••••••••••••••••" />
                                <button type="button" class="eye-btn" onclick="togglePw('sms-api_key', this)" title="Show/Hide">
                                    <svg viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                </button>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Sender ID / DLT ID</label>
                            <input type="text" name="sender_id" id="sms-sender_id" placeholder="GRWCAP" />
                        </div>
                        <div class="form-group">
                            <label>Base URL (API Endpoint)</label>
                            <input type="url" name="base_url" id="sms-base_url" placeholder="https://api.provider.com/v1/send" />
                        </div>
                    </div>
                    <button type="submit" class="btn-save" id="save-sms">💾 Save SMS Settings</button>
                </form>
            </div>
        </div>

        <!-- DIGIO PANEL -->
        <div id="panel-digio" class="settings-panel">
            <div class="panel-card">
                <div class="panel-title">
                    <div class="panel-icon digio">📝</div>
                    <div class="panel-title-text">
                        <h3>Digio API Configuration</h3>
                        <p>KYC verification, e-signing, and document management services.</p>
                    </div>
                </div>
                <div class="info-note">ℹ️ Get credentials from your Digio dashboard at app.digio.in</div>
                <form id="form-digio" onsubmit="saveSettings(event, 'digio')">
                    <div class="form-grid">
                        <div class="form-group">
                            <label>Client ID</label>
                            <input type="text" name="client_id" id="digio-client_id" placeholder="DID••••••••••" />
                        </div>
                        <div class="form-group">
                            <label>Client Secret</label>
                            <div class="pw-wrap">
                                <input type="password" name="client_secret" id="digio-client_secret" placeholder="••••••••••••" />
                                <button type="button" class="eye-btn" onclick="togglePw('digio-client_secret', this)" title="Show/Hide">
                                    <svg viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                </button>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Base URL</label>
                            <input type="url" name="base_url" id="digio-base_url" placeholder="https://api.digio.in" />
                        </div>
                        <div class="form-group">
                            <label>Environment</label>
                            <select name="environment" id="digio-environment">
                                <option value="production">Production</option>
                                <option value="sandbox">Sandbox / Testing</option>
                            </select>
                        </div>
                        <div class="form-group full">
                            <label>KYC Workflow / Template Name</label>
                            <input type="text" name="workflow" id="digio-workflow" placeholder="e.g. kyc_with_aadhaar" />
                            <span style="font-size:11px; color:var(--text-muted); margin-top:5px; display:block; line-height:1.6;">
                                💡 Digio dashboard → <strong>Templates</strong> me jaake workflow name copy karo. Example: <code style="background:rgba(255,255,255,0.07); padding:2px 7px; border-radius:4px; color:#a5b4fc;">kyc_with_aadhaar</code>
                            </span>
                        </div>
                    </div>
                    <button type="submit" class="btn-save" id="save-digio">💾 Save Digio Settings</button>
                </form>
            </div>
        </div>

        <!-- RAZORPAY PANEL -->
        <div id="panel-razorpay" class="settings-panel">
            <div class="panel-card">
                <div class="panel-title">
                    <div class="panel-icon razorpay">💳</div>
                    <div class="panel-title-text">
                        <h3>Razorpay Payment Gateway</h3>
                        <p>Configure Razorpay for payment processing and subscription management.</p>
                    </div>
                </div>
                <div class="info-note">ℹ️ Get credentials from your Razorpay dashboard at dashboard.razorpay.com</div>
                <form id="form-razorpay" onsubmit="saveSettings(event, 'razorpay')">
                    <div class="form-grid">
                        <div class="form-group">
                            <label>Key ID</label>
                            <input type="text" name="key_id" id="razorpay-key_id" placeholder="rzp_live_••••••••" />
                        </div>
                        <div class="form-group">
                            <label>Key Secret</label>
                            <div class="pw-wrap">
                                <input type="password" name="key_secret" id="razorpay-key_secret" placeholder="••••••••••••••••" />
                                <button type="button" class="eye-btn" onclick="togglePw('razorpay-key_secret', this)" title="Show/Hide">
                                    <svg viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                </button>
                            </div>
                        </div>
                        <div class="form-group full">
                            <label>Webhook Secret</label>
                            <div class="pw-wrap">
                                <input type="password" name="webhook_secret" id="razorpay-webhook_secret" placeholder="Webhook signing secret" />
                                <button type="button" class="eye-btn" onclick="togglePw('razorpay-webhook_secret', this)" title="Show/Hide">
                                    <svg viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                </button>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Environment</label>
                            <select name="environment" id="razorpay-environment">
                                <option value="live">Live</option>
                                <option value="test">Test Mode</option>
                            </select>
                        </div>
                    </div>
                    <button type="submit" class="btn-save" id="save-razorpay">💾 Save Razorpay Settings</button>
                </form>
            </div>
        </div>

    </div><!-- /content -->
</div><!-- /main -->

<!-- TOAST -->
<div class="toast" id="toast"></div>

<script>
    const API_BASE = '/api';
    const groups   = ['smtp', 'sms', 'digio', 'razorpay'];
    const adminToken = localStorage.getItem('admin_token');

    // Redirect if not logged in
    if (!adminToken) window.location.href = '/admin/login';

    function getHeaders() {
        return {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'Authorization': `Bearer ${adminToken}`,
        };
    }

    // ---- Admin info ----
    const adminInfo = JSON.parse(localStorage.getItem('admin_info') || '{}');
    if (adminInfo.name) {
        document.getElementById('sidebarName').textContent = adminInfo.name;
        document.getElementById('sidebarAvatar').textContent = adminInfo.name.charAt(0).toUpperCase();
    }

    document.getElementById('sidebarLogout').addEventListener('click', async () => {
        await fetch(`${API_BASE}/admin/logout`, { method: 'POST', headers: getHeaders() });
        localStorage.removeItem('admin_token');
        localStorage.removeItem('admin_info');
        window.location.href = '/admin/login';
    });

    // ---- TAB SWITCHING ----
    function switchTab(group) {
        groups.forEach(g => {
            document.getElementById(`tab-${g}`).classList.remove('active');
            document.getElementById(`panel-${g}`).classList.remove('active');
        });
        document.getElementById(`tab-${group}`).classList.add('active');
        document.getElementById(`panel-${group}`).classList.add('active');
        loadSettings(group);
    }

    // ---- LOAD SETTINGS ----
    async function loadSettings(group) {
        try {
            const res  = await fetch(`${API_BASE}/admin/settings/${group}`, { headers: getHeaders() });
            const data = await res.json();
            if (!data.success) return;

            const settings = data.settings;
            Object.entries(settings).forEach(([key, value]) => {
                const el = document.getElementById(`${group}-${key}`);
                if (el) el.value = value || '';
            });
        } catch (err) {
            console.warn('Could not load', group, 'settings:', err);
        }
    }

    // ---- SAVE SETTINGS ----
    async function saveSettings(e, group) {
        e.preventDefault();
        const form = document.getElementById(`form-${group}`);
        const btn  = document.getElementById(`save-${group}`);
        const formData = new FormData(form);
        const payload  = Object.fromEntries(formData.entries());

        btn.disabled = true;
        btn.innerHTML = '⏳ Saving...';

        try {
            const res  = await fetch(`${API_BASE}/admin/settings/${group}`, {
                method: 'POST',
                headers: getHeaders(),
                body: JSON.stringify(payload),
            });
            const data = await res.json();

            if (data.success) {
                showToast(data.message, 'success');
                document.getElementById(`dot-${group}`).classList.add('configured');
            } else {
                showToast(data.message || 'Save failed.', 'error');
            }
        } catch (err) {
            showToast('Network error. Please try again.', 'error');
        } finally {
            btn.disabled = false;
            btn.innerHTML = '💾 Save ' + group.charAt(0).toUpperCase() + group.slice(1) + ' Settings';
        }
    }

    // ---- TOAST ----
    function showToast(msg, type = 'success') {
        const el = document.getElementById('toast');
        el.className = `toast toast-${type}`;
        el.innerHTML = `<span>${type === 'success' ? '✅' : '❌'}</span> ${msg}`;
        el.style.display = 'flex';
        setTimeout(() => el.style.display = 'none', 4000);
    }

    // ---- Load all statuses on mount ----
    async function loadSummary() {
        try {
            const res  = await fetch(`${API_BASE}/admin/settings`, { headers: getHeaders() });
            const data = await res.json();
            if (!data.success) return;
            Object.entries(data.settings).forEach(([group, info]) => {
                if (info.configured) {
                    document.getElementById(`dot-${group}`)?.classList.add('configured');
                }
            });
        } catch {}
    }

    // ---- EYE TOGGLE ----
    function togglePw(inputId, btn) {
        const inp = document.getElementById(inputId);
        const isHidden = inp.type === 'password';
        inp.type = isHidden ? 'text' : 'password';

        // Swap icon: eye-off when visible, eye when hidden
        btn.querySelector('svg').innerHTML = isHidden
            ? '<path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94"/><path d="M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19"/><line x1="1" y1="1" x2="23" y2="23"/>'
            : '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>';

        btn.style.color = isHidden ? 'var(--primary)' : '';
    }

    // INIT
    loadSummary();
    loadSettings('smtp'); // Load active tab on page load
</script>
</body>
</html>

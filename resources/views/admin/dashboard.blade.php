<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Admin Dashboard - Grow Capitals</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet" />
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        :root {
            --primary: #6C63FF; --amber: #f59e0b; --green: #10b981;
            --bg: #080a12; --sidebar-bg: #0d0f1c; --card: rgba(255,255,255,0.04);
            --border: rgba(255,255,255,0.08); --text: #e8eaf6; --text-muted: #8890a6;
        }
        body { font-family: 'Inter', sans-serif; background: var(--bg); color: var(--text); min-height: 100vh; display: flex; }

        /* Sidebar */
        .sidebar { width: 260px; min-height: 100vh; background: var(--sidebar-bg); border-right: 1px solid var(--border); display: flex; flex-direction: column; position: fixed; top: 0; left: 0; bottom: 0; z-index: 100; }
        .sidebar-logo { padding: 24px 20px; border-bottom: 1px solid var(--border); display: flex; align-items: center; gap: 12px; }
        .sidebar-logo-icon { width: 40px; height: 40px; background: linear-gradient(135deg, var(--primary), #00D4AA); border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 20px; }
        .sidebar-logo-text h3 { font-size: 16px; font-weight: 700; }
        .sidebar-logo-text span { font-size: 11px; color: var(--text-muted); }
        .sidebar-nav { flex: 1; padding: 16px 12px; }
        .nav-section-label { font-size: 10px; font-weight: 600; letter-spacing: 1px; text-transform: uppercase; color: var(--text-muted); padding: 12px 10px 6px; }
        .nav-item { display: flex; align-items: center; gap: 10px; padding: 10px 12px; border-radius: 10px; margin-bottom: 2px; text-decoration: none; color: var(--text-muted); font-size: 14px; transition: all 0.2s; }
        .nav-item:hover { background: var(--card); color: var(--text); }
        .nav-item.active { background: rgba(108,99,255,0.15); color: var(--primary); font-weight: 600; }
        .nav-item .icon { font-size: 18px; width: 22px; text-align: center; }
        .sidebar-footer { padding: 16px 12px; border-top: 1px solid var(--border); }
        .admin-user-badge { display: flex; align-items: center; gap: 10px; padding: 10px 12px; background: var(--card); border-radius: 12px; }
        .admin-avatar { width: 34px; height: 34px; background: linear-gradient(135deg, var(--amber), var(--primary)); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 16px; font-weight: 600; color: #000; }
        .admin-info .name { font-size: 13px; font-weight: 600; }
        .admin-info .role { font-size: 11px; color: var(--amber); }
        .logout-btn { background: none; border: none; color: var(--text-muted); cursor: pointer; font-size: 18px; }
        .logout-btn:hover { color: #ef4444; }

        /* Main */
        .main { margin-left: 260px; flex: 1; display: flex; flex-direction: column; }
        .header { height: 64px; background: rgba(8,10,18,0.95); backdrop-filter: blur(12px); border-bottom: 1px solid var(--border); display: flex; align-items: center; justify-content: space-between; padding: 0 32px; position: sticky; top: 0; z-index: 50; }
        .header h2 { font-size: 18px; font-weight: 600; }

        .content { padding: 32px; }

        .welcome-banner {
            background: linear-gradient(135deg, rgba(108,99,255,0.15), rgba(0,212,170,0.1));
            border: 1px solid rgba(108,99,255,0.2);
            border-radius: 20px;
            padding: 32px;
            margin-bottom: 28px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .welcome-banner h1 { font-size: 24px; font-weight: 700; }
        .welcome-banner p  { font-size: 14px; color: var(--text-muted); margin-top: 6px; }
        .welcome-banner .big-icon { font-size: 64px; opacity: 0.8; }

        .stats-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; margin-bottom: 28px; }

        .stat-card {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 24px;
            transition: all 0.2s;
        }

        .stat-card:hover { border-color: rgba(108,99,255,0.3); transform: translateY(-2px); }

        .stat-label { font-size: 12px; color: var(--text-muted); font-weight: 500; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 10px; }
        .stat-value { font-size: 28px; font-weight: 700; }
        .stat-sub   { font-size: 12px; color: var(--text-muted); margin-top: 4px; }

        .quick-links { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }

        .quick-card {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 24px;
            display: flex;
            align-items: center;
            gap: 16px;
            text-decoration: none;
            color: var(--text);
            transition: all 0.2s;
        }

        .quick-card:hover { border-color: rgba(108,99,255,0.3); background: rgba(108,99,255,0.06); }

        .quick-card-icon { font-size: 32px; }
        .quick-card h3 { font-size: 15px; font-weight: 600; }
        .quick-card p  { font-size: 13px; color: var(--text-muted); margin-top: 4px; }
    </style>
</head>
<body>

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
        <a href="/admin/dashboard" class="nav-item active"><span class="icon">🏠</span> Dashboard</a>
        <a href="/admin/users" class="nav-item"><span class="icon">👥</span> Users</a>
        <div class="nav-section-label">Configuration</div>
        <a href="/admin/settings" class="nav-item"><span class="icon">⚙️</span> API Settings</a>
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

<div class="main">
    <header class="header">
        <h2>Dashboard</h2>
    </header>

    <div class="content">
        <div class="welcome-banner">
            <div>
                <h1>Welcome back, <span id="adminName">Admin</span> 👋</h1>
                <p>Here's what's happening with Grow Capitals Research today.</p>
            </div>
            <div class="big-icon">📊</div>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-label">Total Users</div>
                <div class="stat-value" id="statUsers">—</div>
                <div class="stat-sub">Registered on platform</div>
            </div>
            <div class="stat-card">
                <div class="stat-label">SMTP</div>
                <div class="stat-value" id="statSmtp">—</div>
                <div class="stat-sub">Email gateway status</div>
            </div>
            <div class="stat-card">
                <div class="stat-label">SMS</div>
                <div class="stat-value" id="statSms">—</div>
                <div class="stat-sub">SMS provider status</div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Payments</div>
                <div class="stat-value" id="statPay">—</div>
                <div class="stat-sub">Razorpay status</div>
            </div>
        </div>

        <div class="quick-links">
            <a href="/admin/settings" class="quick-card">
                <div class="quick-card-icon">⚙️</div>
                <div>
                    <h3>API Settings</h3>
                    <p>Configure SMTP, SMS, Digio & Razorpay</p>
                </div>
            </a>
            <a href="/admin/users" class="quick-card">
                <div class="quick-card-icon">👥</div>
                <div>
                    <h3>User Management</h3>
                    <p>View and manage registered users</p>
                </div>
            </a>
        </div>
    </div>
</div>

<script>
    const adminToken = localStorage.getItem('admin_token');
    if (!adminToken) window.location.href = '/admin/login';

    const adminInfo = JSON.parse(localStorage.getItem('admin_info') || '{}');
    if (adminInfo.name) {
        document.getElementById('sidebarName').textContent = adminInfo.name;
        document.getElementById('adminName').textContent  = adminInfo.name;
        document.getElementById('sidebarAvatar').textContent = adminInfo.name.charAt(0).toUpperCase();
    }

    document.getElementById('sidebarLogout').addEventListener('click', async () => {
        await fetch('/api/admin/logout', { method: 'POST', headers: { Authorization: `Bearer ${adminToken}`, Accept: 'application/json' } });
        localStorage.clear();
        window.location.href = '/admin/login';
    });

    // Load setting statuses
    fetch('/api/admin/settings', { headers: { Authorization: `Bearer ${adminToken}`, Accept: 'application/json' } })
        .then(r => r.json()).then(data => {
            if (!data.success) return;
            const s = data.settings;
            document.getElementById('statSmtp').textContent  = s.smtp?.configured   ? '✅ Set' : '❌ Not Set';
            document.getElementById('statSms').textContent   = s.sms?.configured    ? '✅ Set' : '❌ Not Set';
            document.getElementById('statPay').textContent   = s.razorpay?.configured ? '✅ Set' : '❌ Not Set';
        }).catch(() => {});
</script>
</body>
</html>

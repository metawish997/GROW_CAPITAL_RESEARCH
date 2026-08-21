<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Dashboard - Grow Capitals Research</title>
    <meta name="description" content="Your personalized investment research dashboard on Grow Capitals." />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet" />
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        :root {
            --primary: #6C63FF; --primary-dark: #574fd6; --accent: #00D4AA;
            --amber: #f59e0b; --bg: #080b15; --surface: #0d1020;
            --card: rgba(255,255,255,0.04); --border: rgba(255,255,255,0.08);
            --text: #e8eaf6; --text-muted: #8890a6; --red: #ef4444; --green: #10b981;
        }
        body { font-family: 'Inter', sans-serif; background: var(--bg); color: var(--text); min-height: 100vh; display: flex; flex-direction: column; }

        /* HEADER */
        .header { background: rgba(13,16,32,0.97); backdrop-filter: blur(16px); border-bottom: 1px solid var(--border); height: 68px; display: flex; align-items: center; justify-content: space-between; padding: 0 32px; position: sticky; top: 0; z-index: 100; }
        .header-logo { display: flex; align-items: center; gap: 10px; text-decoration: none; }
        .header-logo-icon { width: 38px; height: 38px; background: linear-gradient(135deg, var(--primary), var(--accent)); border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 18px; }
        .header-logo-text { font-size: 17px; font-weight: 700; background: linear-gradient(135deg, var(--primary), var(--accent)); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; }
        .header-nav { display: flex; align-items: center; gap: 4px; }
        .nav-link { padding: 8px 14px; border-radius: 10px; font-size: 14px; color: var(--text-muted); text-decoration: none; transition: all 0.2s; font-weight: 500; }
        .nav-link:hover { color: var(--text); background: var(--card); }
        .nav-link.active { color: var(--primary); background: rgba(108,99,255,0.1); }
        .header-right { display: flex; align-items: center; gap: 12px; }
        .user-avatar-btn { width: 38px; height: 38px; border-radius: 50%; background: linear-gradient(135deg, var(--primary), var(--accent)); border: none; cursor: pointer; display: flex; align-items: center; justify-content: center; font-size: 15px; font-weight: 700; color: #fff; position: relative; }
        .user-dropdown { position: absolute; top: calc(100% + 10px); right: 0; background: var(--surface); border: 1px solid var(--border); border-radius: 14px; padding: 8px; min-width: 200px; box-shadow: 0 20px 40px rgba(0,0,0,0.5); display: none; z-index: 200; }
        .user-dropdown.open { display: block; }
        .dropdown-info { padding: 10px 12px 12px; border-bottom: 1px solid var(--border); margin-bottom: 8px; }
        .dropdown-info .uname { font-size: 14px; font-weight: 600; }
        .dropdown-info .uemail { font-size: 12px; color: var(--text-muted); margin-top: 2px; }
        .dropdown-item { display: flex; align-items: center; gap: 8px; padding: 9px 12px; border-radius: 8px; font-size: 13px; color: var(--text-muted); cursor: pointer; transition: all 0.15s; text-decoration: none; }
        .dropdown-item:hover { background: var(--card); color: var(--text); }
        .dropdown-item.danger { color: var(--red); }
        .dropdown-item.danger:hover { background: rgba(239,68,68,0.1); }

        /* MAIN */
        .main { flex: 1; padding: 32px; }

        /* WELCOME */
        .welcome-banner { background: linear-gradient(135deg, rgba(108,99,255,0.15) 0%, rgba(0,212,170,0.08) 100%); border: 1px solid rgba(108,99,255,0.2); border-radius: 24px; padding: 32px 40px; margin-bottom: 28px; display: flex; align-items: center; justify-content: space-between; overflow: hidden; position: relative; }
        .welcome-banner::before { content: ''; position: absolute; width: 300px; height: 300px; background: radial-gradient(circle, rgba(108,99,255,0.15) 0%, transparent 70%); right: -60px; top: -60px; border-radius: 50%; }
        .welcome-text h1 { font-size: 24px; font-weight: 800; }
        .welcome-text h1 span { background: linear-gradient(135deg, var(--primary), var(--accent)); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; }
        .welcome-text p { font-size: 14px; color: var(--text-muted); margin-top: 6px; }

        /* KYC BANNER */
        .kyc-banner { border-radius: 20px; padding: 24px 28px; margin-bottom: 28px; display: flex; align-items: center; justify-content: space-between; gap: 20px; }
        .kyc-banner.not-started { background: linear-gradient(135deg, rgba(245,158,11,0.12), rgba(239,68,68,0.08)); border: 1px solid rgba(245,158,11,0.3); }
        .kyc-banner.pending    { background: linear-gradient(135deg, rgba(108,99,255,0.12), rgba(0,212,170,0.06)); border: 1px solid rgba(108,99,255,0.25); }
        .kyc-banner.approved   { background: linear-gradient(135deg, rgba(16,185,129,0.12), rgba(0,212,170,0.06)); border: 1px solid rgba(16,185,129,0.25); }
        .kyc-banner.failed     { background: linear-gradient(135deg, rgba(239,68,68,0.10), rgba(245,158,11,0.06)); border: 1px solid rgba(239,68,68,0.25); }
        .kyc-left { display: flex; align-items: center; gap: 16px; }
        .kyc-icon { width: 52px; height: 52px; border-radius: 14px; display: flex; align-items: center; justify-content: center; font-size: 24px; flex-shrink: 0; }
        .kyc-icon.amber  { background: rgba(245,158,11,0.15); }
        .kyc-icon.purple { background: rgba(108,99,255,0.15); }
        .kyc-icon.green  { background: rgba(16,185,129,0.15); }
        .kyc-icon.red    { background: rgba(239,68,68,0.15); }
        .kyc-text h3 { font-size: 16px; font-weight: 700; }
        .kyc-text p  { font-size: 13px; color: var(--text-muted); margin-top: 4px; }
        .kyc-badge { display: inline-flex; align-items: center; gap: 6px; padding: 4px 10px; border-radius: 20px; font-size: 11px; font-weight: 600; margin-top: 6px; width: fit-content; }
        .kyc-badge.amber  { background: rgba(245,158,11,0.12); color: #fbbf24; border: 1px solid rgba(245,158,11,0.25); }
        .kyc-badge.green  { background: rgba(16,185,129,0.12); color: #34d399; border: 1px solid rgba(16,185,129,0.25); }
        .kyc-badge.purple { background: rgba(108,99,255,0.12); color: #a5b4fc; border: 1px solid rgba(108,99,255,0.25); }
        .kyc-badge.red    { background: rgba(239,68,68,0.12); color: #f87171; border: 1px solid rgba(239,68,68,0.25); }
        .kyc-btn { padding: 11px 24px; border-radius: 12px; font-size: 13px; font-weight: 600; font-family: 'Inter', sans-serif; cursor: pointer; border: none; transition: all 0.2s; white-space: nowrap; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; flex-shrink: 0; }
        .kyc-btn.amber  { background: linear-gradient(135deg, #f59e0b, #d97706); color: #000; }
        .kyc-btn.amber:hover  { transform: translateY(-1px); box-shadow: 0 6px 20px rgba(245,158,11,0.4); }
        .kyc-btn.green  { background: linear-gradient(135deg, var(--green), #059669); color: #fff; }
        .kyc-btn.purple { background: linear-gradient(135deg, var(--primary), var(--primary-dark)); color: #fff; }
        .kyc-btn.purple:hover { transform: translateY(-1px); box-shadow: 0 6px 20px rgba(108,99,255,0.4); }
        .kyc-btn.outline { background: transparent; border: 1px solid var(--border); color: var(--text-muted); }
        .kyc-btn.outline:hover { background: var(--card); color: var(--text); }

        /* STATS */
        .stats-row { display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; margin-bottom: 28px; }
        .stat-card { background: var(--card); border: 1px solid var(--border); border-radius: 18px; padding: 22px; transition: all 0.2s; }
        .stat-card:hover { border-color: rgba(108,99,255,0.3); transform: translateY(-2px); box-shadow: 0 8px 24px rgba(0,0,0,0.3); }
        .stat-icon { font-size: 22px; margin-bottom: 12px; }
        .stat-label { font-size: 11px; color: var(--text-muted); font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; }
        .stat-value { font-size: 24px; font-weight: 700; margin-top: 5px; }
        .stat-change { font-size: 12px; margin-top: 4px; color: var(--text-muted); }

        /* CONTENT GRID */
        .content-grid { display: grid; grid-template-columns: 1fr 320px; gap: 20px; }
        .section-card { background: var(--card); border: 1px solid var(--border); border-radius: 20px; padding: 24px; }
        .section-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 18px; }
        .section-header h3 { font-size: 15px; font-weight: 600; }
        .section-header span { font-size: 12px; color: var(--text-muted); }
        .activity-item { display: flex; align-items: center; gap: 12px; padding: 11px 0; border-bottom: 1px solid var(--border); }
        .activity-item:last-child { border-bottom: none; }
        .activity-dot { width: 34px; height: 34px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 15px; flex-shrink: 0; }
        .activity-dot.green  { background: rgba(16,185,129,0.15); }
        .activity-dot.purple { background: rgba(108,99,255,0.15); }
        .activity-dot.amber  { background: rgba(245,158,11,0.15); }
        .activity-text .title { font-size: 13px; font-weight: 500; }
        .activity-text .sub   { font-size: 12px; color: var(--text-muted); margin-top: 2px; }
        .activity-time { margin-left: auto; font-size: 11px; color: var(--text-muted); white-space: nowrap; }
        .quick-actions { display: flex; flex-direction: column; gap: 8px; }
        .quick-action-btn { display: flex; align-items: center; gap: 12px; padding: 13px 16px; background: rgba(255,255,255,0.03); border: 1px solid var(--border); border-radius: 12px; cursor: pointer; transition: all 0.2s; text-decoration: none; color: var(--text); }
        .quick-action-btn:hover { background: rgba(108,99,255,0.08); border-color: rgba(108,99,255,0.3); transform: translateX(2px); }
        .quick-action-btn .qicon { font-size: 18px; }
        .quick-action-btn .qtext .qtitle { font-size: 13px; font-weight: 600; }
        .quick-action-btn .qtext .qsub   { font-size: 11px; color: var(--text-muted); margin-top: 2px; }
        .quick-action-btn .qarrow { margin-left: auto; color: var(--text-muted); font-size: 14px; }

        /* FOOTER */
        footer { background: var(--surface); border-top: 1px solid var(--border); padding: 24px 32px; margin-top: auto; }
        .footer-inner { display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 16px; }
        .footer-logo { display: flex; align-items: center; gap: 8px; text-decoration: none; }
        .footer-logo-icon { width: 28px; height: 28px; background: linear-gradient(135deg, var(--primary), var(--accent)); border-radius: 7px; display: flex; align-items: center; justify-content: center; font-size: 13px; }
        .footer-logo-text { font-size: 13px; font-weight: 700; color: var(--text-muted); }
        .footer-links { display: flex; gap: 20px; }
        .footer-links a { font-size: 12px; color: var(--text-muted); text-decoration: none; transition: color 0.2s; }
        .footer-links a:hover { color: var(--primary); }
        .footer-copy { font-size: 12px; color: var(--text-muted); }

        /* SKELETON */
        .skeleton { background: linear-gradient(90deg, rgba(255,255,255,0.04) 25%, rgba(255,255,255,0.08) 50%, rgba(255,255,255,0.04) 75%); background-size: 200% 100%; animation: shimmer 1.5s infinite; border-radius: 8px; height: 20px; }
        @keyframes shimmer { 0% { background-position: 200% 0; } 100% { background-position: -200% 0; } }

        /* ALERT FLASH */
        .flash { position: fixed; top: 80px; right: 24px; padding: 14px 20px; border-radius: 12px; font-size: 14px; font-weight: 500; z-index: 9999; display: flex; align-items: center; gap: 10px; box-shadow: 0 8px 24px rgba(0,0,0,0.4); animation: slideIn 0.3s ease; }
        .flash-success { background: rgba(16,185,129,0.15); border: 1px solid rgba(16,185,129,0.3); color: #34d399; }
        .flash-error   { background: rgba(239,68,68,0.15); border: 1px solid rgba(239,68,68,0.3); color: #f87171; }
        .flash-info    { background: rgba(108,99,255,0.15); border: 1px solid rgba(108,99,255,0.3); color: #a5b4fc; }
        @keyframes slideIn { from { transform: translateX(30px); opacity: 0; } to { transform: translateX(0); opacity: 1; } }
    </style>
</head>
<body>

<!-- HEADER -->
<header class="header">
    <a href="/dashboard" class="header-logo">
        <div class="header-logo-icon">📈</div>
        <span class="header-logo-text">Grow Capitals</span>
    </a>
    <nav class="header-nav">
        <a href="/dashboard" class="nav-link active">Dashboard</a>
        <a href="#" class="nav-link">Research</a>
        <a href="#" class="nav-link">Portfolio</a>
        <a href="/kyc" class="nav-link">KYC</a>
    </nav>
    <div class="header-right">
        <div style="position:relative">
            <button class="user-avatar-btn" id="avatarBtn" onclick="toggleDropdown()">
                <span id="avatarLetter">U</span>
            </button>
            <div class="user-dropdown" id="userDropdown">
                <div class="dropdown-info">
                    <div class="uname" id="dropName">User</div>
                    <div class="uemail" id="dropEmail">user@example.com</div>
                </div>
                <a href="/dashboard" class="dropdown-item">🏠 Dashboard</a>
                <a href="/kyc" class="dropdown-item">🪪 KYC Verification</a>
                <div class="dropdown-item danger" id="logoutBtn">⏻ Logout</div>
            </div>
        </div>
    </div>
</header>

<!-- MAIN -->
<main class="main">

    <!-- Welcome Banner -->
    <div class="welcome-banner">
        <div class="welcome-text">
            <h1>Welcome back, <span id="welcomeName">Investor</span> 👋</h1>
            <p>Here's your personalized research summary for today.</p>
        </div>
        <div style="font-size: 64px; opacity: 0.7; position: relative; z-index: 1;">📊</div>
    </div>

    <!-- KYC STATUS BANNER (dynamic) -->
    <div id="kycBannerWrap">
        <!-- Skeleton while loading -->
        <div style="height:100px; background: var(--card); border: 1px solid var(--border); border-radius: 20px; margin-bottom: 28px; display: flex; align-items: center; padding: 0 28px; gap: 16px;" id="kycSkeleton">
            <div class="skeleton" style="width:52px; height:52px; border-radius:14px; flex-shrink:0;"></div>
            <div style="flex:1;">
                <div class="skeleton" style="width: 40%; height: 16px; margin-bottom:8px;"></div>
                <div class="skeleton" style="width: 60%; height: 12px;"></div>
            </div>
        </div>
    </div>

    <!-- E-SIGN STATUS BANNER (dynamic) -->
    <div id="esignBannerWrap"></div>


    <!-- Stats -->
    <div class="stats-row">
        <div class="stat-card">
            <div class="stat-icon">📈</div>
            <div class="stat-label">Portfolio Value</div>
            <div class="stat-value">₹0.00</div>
            <div class="stat-change">Updated today</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">📋</div>
            <div class="stat-label">Research Reports</div>
            <div class="stat-value">0</div>
            <div class="stat-change">Available reports</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">🔔</div>
            <div class="stat-label">Alerts Active</div>
            <div class="stat-value">0</div>
            <div class="stat-change">Price alerts</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" id="kycStatIcon">🪪</div>
            <div class="stat-label">KYC Status</div>
            <div class="stat-value" id="kycStatValue" style="font-size:15px; margin-top:8px;">Loading...</div>
            <div class="stat-change" id="kycStatSub">Checking...</div>
        </div>
    </div>

    <!-- Content Grid -->
    <div class="content-grid">
        <div class="section-card">
            <div class="section-header">
                <h3>Recent Activity</h3>
                <span>Today</span>
            </div>
            <div class="activity-item">
                <div class="activity-dot green">✅</div>
                <div class="activity-text">
                    <div class="title">Account Verified</div>
                    <div class="sub">Email OTP login successful</div>
                </div>
                <div class="activity-time" id="loginTime">just now</div>
            </div>
            <div class="activity-item">
                <div class="activity-dot purple">🔐</div>
                <div class="activity-text">
                    <div class="title">Successful Login</div>
                    <div class="sub">Logged in using Email OTP</div>
                </div>
                <div class="activity-time">just now</div>
            </div>
            <div class="activity-item">
                <div class="activity-dot amber">📊</div>
                <div class="activity-text">
                    <div class="title">Dashboard Opened</div>
                    <div class="sub">Grow Capitals Research platform</div>
                </div>
                <div class="activity-time">just now</div>
            </div>
        </div>

        <div>
            <div class="section-card">
                <div class="section-header"><h3>Quick Actions</h3></div>
                <div class="quick-actions">
                    <a href="/kyc" class="quick-action-btn">
                        <span class="qicon">🪪</span>
                        <div class="qtext">
                            <div class="qtitle">Complete KYC</div>
                            <div class="qsub">Verify your identity</div>
                        </div>
                        <span class="qarrow">→</span>
                    </a>
                    <a href="#" class="quick-action-btn">
                        <span class="qicon">📋</span>
                        <div class="qtext">
                            <div class="qtitle">View Research Reports</div>
                            <div class="qsub">Latest market insights</div>
                        </div>
                        <span class="qarrow">→</span>
                    </a>
                    <a href="#" class="quick-action-btn">
                        <span class="qicon">🔔</span>
                        <div class="qtext">
                            <div class="qtitle">Set Price Alerts</div>
                            <div class="qsub">Get notified on price movement</div>
                        </div>
                        <span class="qarrow">→</span>
                    </a>
                    <a href="#" class="quick-action-btn">
                        <span class="qicon">💳</span>
                        <div class="qtext">
                            <div class="qtitle">Upgrade Plan</div>
                            <div class="qsub">Unlock premium features</div>
                        </div>
                        <span class="qarrow">→</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</main>

<!-- FOOTER -->
<footer>
    <div class="footer-inner">
        <a href="/dashboard" class="footer-logo">
            <div class="footer-logo-icon">📈</div>
            <span class="footer-logo-text">Grow Capitals Research</span>
        </a>
        <div class="footer-links">
            <a href="#">About</a>
            <a href="#">Privacy Policy</a>
            <a href="#">Terms of Use</a>
            <a href="#">Support</a>
        </div>
        <div class="footer-copy">© 2026 Grow Capitals Research. All rights reserved.</div>
    </div>
</footer>

<script>
    const userToken = localStorage.getItem('user_token');
    if (!userToken) window.location.href = '/login';

    const userInfo = JSON.parse(localStorage.getItem('user_info') || '{}');
    if (userInfo.name) {
        document.getElementById('avatarLetter').textContent = userInfo.name.charAt(0).toUpperCase();
        document.getElementById('welcomeName').textContent  = userInfo.name;
        document.getElementById('dropName').textContent     = userInfo.name;
    }
    if (userInfo.email) document.getElementById('dropEmail').textContent = userInfo.email;

    function toggleDropdown() {
        document.getElementById('userDropdown').classList.toggle('open');
    }
    document.addEventListener('click', (e) => {
        const btn = document.getElementById('avatarBtn');
        const dd  = document.getElementById('userDropdown');
        if (!btn.contains(e.target) && !dd.contains(e.target)) dd.classList.remove('open');
    });

    document.getElementById('logoutBtn').addEventListener('click', async () => {
        try {
            await fetch('/api/user/logout', {
                method: 'POST',
                headers: { 'Authorization': 'Bearer ' + userToken, 'Accept': 'application/json' }
            });
        } catch (e) {}
        localStorage.removeItem('user_token');
        localStorage.removeItem('user_info');
        window.location.href = '/login';
    });

    document.getElementById('loginTime').textContent = new Date().toLocaleTimeString('en-IN', { hour: '2-digit', minute: '2-digit' });

    // ─── KYC STATUS LOAD ──────────────────────────────────────────
    async function loadKycStatus() {
        try {
            const res  = await fetch('/api/user/kyc/status', {
                headers: { 'Authorization': 'Bearer ' + userToken, 'Accept': 'application/json' }
            });
            const data = await res.json();

            const status = data.kyc_status; // not_started | initiated | pending | approval_pending | approved | rejected | failed | expired
            renderKycBanner(status, data.kyc);
            renderKycStat(status);

            if (status === 'approved') {
                loadEsignStatus();
            }

        } catch (e) {
            renderKycBanner('error', null);
        }
    }

    function renderKycBanner(status, kyc) {
        const wrap = document.getElementById('kycBannerWrap');

        const configs = {
            not_started: {
                cls: 'not-started', iconCls: 'amber', icon: '🪪',
                title: 'Complete Your KYC Verification',
                desc: 'Your identity is not verified yet. Complete KYC to unlock all features of the platform.',
                badge: null,
                btnLabel: '🚀 Start KYC Now', btnCls: 'amber', btnHref: '/kyc'
            },
            initiated: {
                cls: 'pending', iconCls: 'purple', icon: '⏳',
                title: 'KYC In Progress',
                desc: 'Your KYC request has been created. Please complete the Digio verification.',
                badge: { text: 'Initiated', cls: 'purple' },
                btnLabel: '↻ Continue KYC', btnCls: 'purple', btnHref: '/kyc'
            },
            pending: {
                cls: 'pending', iconCls: 'purple', icon: '⏳',
                title: 'KYC Under Review',
                desc: 'Your KYC is submitted and being reviewed. This usually takes a few minutes.',
                badge: { text: 'Pending Review', cls: 'purple' },
                btnLabel: '↻ Check Status', btnCls: 'outline', btnHref: '/kyc'
            },
            approval_pending: {
                cls: 'pending', iconCls: 'purple', icon: '⏳',
                title: 'KYC Awaiting Approval',
                desc: 'Your KYC is complete and awaiting final approval from our team.',
                badge: { text: 'Approval Pending', cls: 'purple' },
                btnLabel: '↻ Check Status', btnCls: 'outline', btnHref: '/kyc'
            },
            approved: {
                cls: 'approved', iconCls: 'green', icon: '✅',
                title: 'KYC Verified Successfully',
                desc: 'Your identity is fully verified. You have access to all platform features.',
                badge: { text: '✅ KYC Approved', cls: 'green' },
                btnLabel: '👁 View Details', btnCls: 'green', btnHref: '/kyc'
            },
            rejected: {
                cls: 'failed', iconCls: 'red', icon: '❌',
                title: 'KYC Rejected',
                desc: 'Your KYC was rejected. Please retry with correct documents.',
                badge: { text: 'Rejected', cls: 'red' },
                btnLabel: '🔁 Retry KYC', btnCls: 'amber', btnHref: '/kyc'
            },
            failed: {
                cls: 'failed', iconCls: 'red', icon: '❌',
                title: 'KYC Verification Failed',
                desc: 'Something went wrong during verification. Please try again.',
                badge: { text: 'Failed', cls: 'red' },
                btnLabel: '🔁 Retry KYC', btnCls: 'amber', btnHref: '/kyc'
            },
            expired: {
                cls: 'not-started', iconCls: 'amber', icon: '⏰',
                title: 'KYC Session Expired',
                desc: 'Your previous KYC session expired. Please start a fresh verification.',
                badge: { text: 'Expired', cls: 'amber' },
                btnLabel: '🔁 Restart KYC', btnCls: 'amber', btnHref: '/kyc'
            },
            error: {
                cls: 'not-started', iconCls: 'amber', icon: '⚠️',
                title: 'Could not load KYC status',
                desc: 'Please refresh the page or try again.',
                badge: null,
                btnLabel: '↻ Refresh', btnCls: 'outline', btnHref: '/dashboard'
            }
        };

        const cfg = configs[status] || configs['error'];

        wrap.innerHTML = `
            <div class="kyc-banner ${cfg.cls}">
                <div class="kyc-left">
                    <div class="kyc-icon ${cfg.iconCls}">${cfg.icon}</div>
                    <div class="kyc-text">
                        <h3>${cfg.title}</h3>
                        <p>${cfg.desc}</p>
                        ${cfg.badge ? `<span class="kyc-badge ${cfg.badge.cls}">${cfg.badge.text}</span>` : ''}
                    </div>
                </div>
                <a href="${cfg.btnHref}" class="kyc-btn ${cfg.btnCls}">${cfg.btnLabel}</a>
            </div>`;
    }

    function renderKycStat(status) {
        const map = {
            not_started:      { icon: '🪪', value: 'Not Started', sub: 'Complete KYC', color: '#f59e0b' },
            initiated:        { icon: '⏳', value: 'In Progress', sub: 'Complete on Digio', color: '#a5b4fc' },
            pending:          { icon: '⏳', value: 'Under Review', sub: 'Checking documents', color: '#a5b4fc' },
            approval_pending: { icon: '⏳', value: 'Pending', sub: 'Awaiting approval', color: '#a5b4fc' },
            approved:         { icon: '✅', value: 'Verified', sub: 'KYC approved ✅', color: '#34d399' },
            rejected:         { icon: '❌', value: 'Rejected', sub: 'Retry required', color: '#f87171' },
            failed:           { icon: '❌', value: 'Failed', sub: 'Retry required', color: '#f87171' },
            expired:          { icon: '⏰', value: 'Expired', sub: 'Restart KYC', color: '#f59e0b' },
        };

        const s = map[status] || map['not_started'];
        document.getElementById('kycStatIcon').textContent  = s.icon;
        document.getElementById('kycStatValue').textContent = s.value;
        document.getElementById('kycStatValue').style.color = s.color;
        document.getElementById('kycStatSub').textContent   = s.sub;
    }

    // ─── E-SIGN STATUS LOAD ─────────────────────────────────────────
    async function loadEsignStatus() {
        try {
            const res = await fetch('/api/user/esign/status', {
                headers: { 'Authorization': 'Bearer ' + userToken, 'Accept': 'application/json' }
            });
            const data = await res.json();
            
            const wrap = document.getElementById('esignBannerWrap');
            if (data.is_signed) {
                wrap.innerHTML = `
                <div class="kyc-banner approved">
                    <div class="kyc-left">
                        <div class="kyc-icon green">✍️</div>
                        <div class="kyc-text">
                            <h3>Agreement Signed</h3>
                            <p>You have successfully signed the service agreement.</p>
                        </div>
                    </div>
                    <div style="display:flex; gap:10px;">
                        <button onclick="fetchSignedPdf(false)" class="kyc-btn purple">👁️ Preview PDF</button>
                        <button onclick="fetchSignedPdf(true)" class="kyc-btn outline">📥 Download PDF</button>
                    </div>
                </div>`;
            } else {
                wrap.innerHTML = `
                <div class="kyc-banner pending">
                    <div class="kyc-left">
                        <div class="kyc-icon purple">📄</div>
                        <div class="kyc-text">
                            <h3>Service Agreement Pending</h3>
                            <p>Please review and sign the service agreement to proceed.</p>
                        </div>
                    </div>
                    <button onclick="previewAgreement()" class="kyc-btn purple" id="signBtn">✍️ Sign Agreement</button>
                </div>`;
            }
        } catch (e) {
            console.error('Error loading esign status', e);
        }
    }

    async function previewAgreement() {
        const btn = document.getElementById('signBtn');
        btn.innerHTML = 'Loading Preview...';
        btn.disabled = true;
        try {
            const res = await fetch('/api/user/esign/preview', {
                headers: { 'Authorization': 'Bearer ' + userToken, 'Accept': 'application/json' }
            });
            const data = await res.json();
            if (data.status === 'success' && data.pdf_base64) {
                document.getElementById('pdfIframe').src = 'data:application/pdf;base64,' + data.pdf_base64;
                document.getElementById('proceedSignBtn').style.display = 'inline-block';
                document.getElementById('pdfModalTitle').innerText = 'Agreement Preview';
                document.getElementById('pdfModal').style.display = 'block';
            } else {
                alert(data.message || 'Error generating preview');
            }
        } catch (e) {
            console.error('Error generating preview', e);
        }
        btn.innerHTML = '✍️ Sign Agreement';
        btn.disabled = false;
    }

    function closePdfModal() {
        document.getElementById('pdfModal').style.display = 'none';
        document.getElementById('pdfIframe').src = '';
    }

    async function fetchSignedPdf(isDownload) {
        try {
            const url = isDownload ? '/api/user/esign/download?download=1' : '/api/user/esign/download';
            const res = await fetch(url, {
                headers: { 'Authorization': 'Bearer ' + userToken }
            });
            if (!res.ok) throw new Error('Failed to load PDF');
            const blob = await res.blob();
            const blobUrl = URL.createObjectURL(blob);
            
            if (isDownload) {
                const a = document.createElement('a');
                a.href = blobUrl;
                a.download = 'GROW_CAPITAL_RESEARCH_Agreement.pdf';
                document.body.appendChild(a);
                a.click();
                a.remove();
            } else {
                document.getElementById('pdfIframe').src = blobUrl;
                document.getElementById('proceedSignBtn').style.display = 'none';
                document.getElementById('pdfModalTitle').innerText = 'Signed Agreement';
                document.getElementById('pdfModal').style.display = 'block';
            }
        } catch (e) {
            console.error('Error fetching PDF', e);
            alert('Failed to load the signed agreement. Please make sure you are logged in.');
        }
    }

    async function signAgreement() {
        const btn = document.getElementById('proceedSignBtn');
        const originalText = btn.innerHTML;
        btn.innerHTML = 'Processing...';
        btn.disabled = true;
        try {
            const res = await fetch('/api/user/esign/sign', {
                method: 'POST',
                headers: { 'Authorization': 'Bearer ' + userToken, 'Accept': 'application/json' }
            });
            const data = await res.json();
            if (data.status === 'success') {
                if (data.esign_url) {
                    window.location.href = data.esign_url;
                } else {
                    closePdfModal();
                    loadEsignStatus();
                }
            } else {
                alert(data.message || 'Error signing agreement');
                btn.innerHTML = originalText;
                btn.disabled = false;
            }
        } catch (e) {
            console.error('Error signing agreement', e);
            btn.innerHTML = originalText;
            btn.disabled = false;
        }
    }

    loadKycStatus();
</script>

<!-- Modal for PDF Preview -->
<div id="pdfModal" style="display:none; position:fixed; z-index:9999; left:0; top:0; width:100%; height:100%; overflow:auto; background-color:rgba(0,0,0,0.7);">
    <div style="background-color:#fff; margin:5% auto; padding:20px; border:1px solid #888; width:80%; max-width:900px; border-radius:8px;">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:15px;">
            <h2 id="pdfModalTitle" style="margin:0; font-size:20px;">Agreement Preview</h2>
            <span onclick="closePdfModal()" style="cursor:pointer; font-size:28px; font-weight:bold;">&times;</span>
        </div>
        <iframe id="pdfIframe" style="width:100%; height:60vh; border:1px solid #ccc; margin-bottom:15px;"></iframe>
        <div style="text-align:right;">
            <button onclick="closePdfModal()" style="padding:10px 15px; border-radius:4px; border:1px solid #ccc; cursor:pointer; background:#f9f9f9; margin-right:10px;">Close</button>
            <button onclick="signAgreement()" id="proceedSignBtn" style="padding:10px 15px; border-radius:4px; border:none; cursor:pointer; background:#6366f1; color:white;">Proceed to E-Sign</button>
        </div>
    </div>
</div>

</body>
</html>

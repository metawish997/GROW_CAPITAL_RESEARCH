<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>@yield('title', 'Admin Portal') - Grow Capitals</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        :root {
            --primary: #004b87;
            --primary-dark: #003764;
            --accent: #98d102;
            --text-dark: #0b2d49;
            --bg: #f4f6fa;
            --card: #ffffff;
            --border: #e2e8f0;
            --text: #0f172a;
            --text-muted: #64748b;
            --green: #10b981;
            --amber: #f59e0b;
            --red: #ef4444;
        }
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: var(--bg); color: var(--text); min-height: 100vh; display: flex; }

        /* Sidebar */
        .sidebar { width: 260px; min-height: 100vh; background: var(--primary); display: flex; flex-direction: column; position: fixed; top: 0; left: 0; bottom: 0; z-index: 100; }
        .sidebar-logo { padding: 12px 20px; border-bottom: 1px solid rgba(255,255,255,0.1); }
        .sidebar-logo img { height: 48px; width: auto; max-width: 100%; object-fit: contain; filter: brightness(0) invert(1); display: block; }
        .sidebar-nav { flex: 1; padding: 16px 12px; }
        .nav-section-label { font-size: 10px; font-weight: 600; letter-spacing: 1px; text-transform: uppercase; color: rgba(255,255,255,0.4); padding: 12px 10px 6px; }
        .nav-item { display: flex; align-items: center; gap: 10px; padding: 10px 12px; border-radius: 10px; margin-bottom: 2px; text-decoration: none; color: rgba(255,255,255,0.65); font-size: 14px; transition: all 0.2s; }
        .nav-item:hover { background: rgba(255,255,255,0.08); color: #ffffff; }
        .nav-item.active { background: var(--accent); color: var(--primary); font-weight: 700; }
        .nav-item .icon { font-size: 18px; width: 22px; text-align: center; }
        .sidebar-footer { padding: 16px 12px; border-top: 1px solid rgba(255,255,255,0.1); }
        .admin-user-badge { display: flex; align-items: center; gap: 10px; padding: 10px 12px; background: rgba(255,255,255,0.08); border-radius: 12px; }
        .admin-avatar { width: 34px; height: 34px; background: var(--accent); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 14px; font-weight: 700; color: var(--primary); }
        .admin-info { flex: 1; min-width: 0; }
        .admin-info .name { font-size: 13px; font-weight: 600; color: #ffffff; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .admin-info .role { font-size: 11px; color: var(--accent); }
        .logout-btn { background: none; border: none; color: rgba(255,255,255,0.5); cursor: pointer; font-size: 18px; transition: color 0.2s; }
        .logout-btn:hover { color: #f87171; }

        /* Main Container */
        .main { margin-left: 260px; flex: 1; display: flex; flex-direction: column; min-width: 0; }
        .header { height: 70px; background: rgba(255,255,255,0.95); backdrop-filter: blur(12px); border-bottom: 1px solid var(--border); display: flex; align-items: center; justify-content: space-between; padding: 0 32px; position: sticky; top: 0; z-index: 50; }
        .header h2 { font-size: 20px; font-weight: 700; color: var(--text-dark); }
        .content { padding: 32px; }

        /* Mobile Responsiveness */
        @media (max-width: 768px) {
            body {
                flex-direction: column;
            }
            .sidebar {
                width: 100%;
                min-height: auto;
                height: auto;
                position: sticky;
                top: 0;
                flex-direction: row;
                flex-wrap: wrap;
                align-items: center;
                justify-content: space-between;
                padding: 6px 16px;
                border-bottom: 1px solid rgba(255,255,255,0.1);
                z-index: 100;
            }
            .sidebar-logo {
                padding: 4px 0;
                border-bottom: none;
            }
            .sidebar-logo img {
                height: 36px;
            }
            .sidebar-nav {
                width: 100%;
                order: 3;
                display: flex;
                flex-direction: row;
                gap: 4px;
                padding: 6px 0;
                overflow-x: auto;
                white-space: nowrap;
                scrollbar-width: none;
            }
            .sidebar-nav::-webkit-scrollbar {
                display: none;
            }
            .nav-section-label {
                display: none !important;
            }
            .nav-item {
                margin-bottom: 0;
                padding: 8px 12px;
                font-size: 13px;
                flex-shrink: 0;
            }
            .sidebar-footer {
                padding: 0;
                border-top: none;
            }
            .admin-info {
                display: none !important;
            }
            .admin-user-badge {
                padding: 4px 8px;
                gap: 6px;
                background: transparent;
            }
            .admin-avatar {
                width: 28px;
                height: 28px;
                font-size: 12px;
            }
            .logout-btn {
                font-size: 16px;
            }
            .main {
                margin-left: 0;
                width: 100%;
            }
            .header {
                padding: 0 16px;
                height: 60px;
            }
            .header h2 {
                font-size: 16px;
            }
            .content {
                padding: 16px;
            }

            /* Responsive mobile tables (stacked cards) */
            .table-card {
                width: 100%;
                overflow: hidden;
                border: none;
                background: transparent;
                box-shadow: none;
                padding: 0;
            }
            .data-table, .data-table thead, .data-table tbody, .data-table th, .data-table td, .data-table tr {
                display: block;
            }
            .data-table thead tr {
                position: absolute;
                top: -9999px;
                left: -9999px;
            }
            .data-table tr {
                border: 1px solid var(--border);
                border-radius: 12px;
                margin-bottom: 16px;
                background: var(--card);
                overflow: hidden;
            }
            .data-table td {
                border: none;
                position: relative;
                padding: 10px 12px 10px 40% !important;
                text-align: right !important;
                min-height: 40px;
                border-bottom: 1px solid rgba(0,0,0,0.04);
                font-size: 13px !important;
            }
            .data-table td:last-child {
                border-bottom: none;
            }
            .data-table td::before {
                content: attr(data-label);
                position: absolute;
                left: 12px;
                top: 10px;
                width: 35%;
                padding-right: 8px;
                white-space: normal;
                text-align: left;
                font-weight: 600;
                color: var(--text-muted);
                font-size: 10px;
                text-transform: uppercase;
            }

            /* Inline CSS Grid overrides for mobile */
            div[style*="display: grid"],
            div[style*="display:grid"] {
                grid-template-columns: 1fr !important;
                gap: 16px !important;
            }
            
            /* Split grids on dashboard and details page */
            div[style*="grid-template-columns: 1fr 1.5fr"],
            div[style*="grid-template-columns: 2fr 1fr"],
            div[style*="grid-template-columns: repeat(2, 1fr)"],
            div[style*="grid-template-columns: repeat(3, 1fr)"],
            div[style*="grid-template-columns: repeat(4, 1fr)"] {
                grid-template-columns: 1fr !important;
                gap: 16px !important;
            }

            /* Form columns on API settings */
            .form-grid {
                grid-template-columns: 1fr !important;
            }
        }

        @yield('styles')
    </style>
</head>
<body>

<script>
    // Client-side authentication check before rendering anything
    const adminToken = localStorage.getItem('admin_token');
    if (!adminToken) {
        window.location.href = '/admin/login';
    }
</script>

<aside class="sidebar">
    <div class="sidebar-logo">
        <img src="{{ asset('grologo.png') }}" alt="Grow Capital Research" />
    </div>
    <nav class="sidebar-nav">
        <div class="nav-section-label">Main</div>
        <a href="/admin/dashboard" class="nav-item {{ Request::is('admin/dashboard') ? 'active' : '' }}">
            <span class="icon">
                <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>
            </span>
            Dashboard
        </a>
        <a href="/admin/users" class="nav-item {{ Request::is('admin/users') || Request::is('admin/users/*') ? 'active' : '' }}">
            <span class="icon">
                <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
            </span>
            Customers
        </a>
        <div class="nav-section-label" id="configSectionLabel">Configuration</div>
        <a href="/admin/settings" id="navSettings" class="nav-item {{ Request::is('admin/settings') ? 'active' : '' }}">
            <span class="icon">
                <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 1 1-2.83-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 1 1 2.83-2.83l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 1 1 2.83 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"></path></svg>
            </span>
            API Settings
        </a>
        <a href="/admin/team" id="navTeam" class="nav-item {{ Request::is('admin/team') ? 'active' : '' }}">
            <span class="icon">
                <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
            </span>
            Team Members
        </a>
        <a href="/admin/change-password" class="nav-item {{ Request::is('admin/change-password') ? 'active' : '' }}">
            <span class="icon">
                <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
            </span>
            Change Password
        </a>
    </nav>
    <div class="sidebar-footer">
        <div class="admin-user-badge">
            <div class="admin-avatar" id="sidebarAvatar">A</div>
            <div class="admin-info">
                <div class="name" id="sidebarName">Admin</div>
                <div class="role" id="sidebarRole">Super Admin</div>
            </div>
            <button class="logout-btn" id="sidebarLogout" title="Logout">⏻</button>
        </div>
    </div>
</aside>

<div class="main">
    <header class="header">
        <h2>@yield('header_title', 'Dashboard')</h2>
        @yield('header_actions')
    </header>

    <div class="content">
        @yield('content')
    </div>
</div>

<script>
    // Populate Admin User details
    const adminInfo = JSON.parse(localStorage.getItem('admin_info') || '{}');
    if (adminInfo.name) {
        document.getElementById('sidebarName').textContent = adminInfo.name;
        document.getElementById('sidebarAvatar').textContent = adminInfo.name.charAt(0).toUpperCase();
    }
    if (adminInfo.role) {
        document.getElementById('sidebarRole').textContent = adminInfo.role === 'admin' ? 'Super Admin' : 'Staff Admin';
        
        // Hide settings and team links for staff members
        if (adminInfo.role === 'staff') {
            document.getElementById('configSectionLabel').style.display = 'none';
            document.getElementById('navSettings').style.display = 'none';
            document.getElementById('navKraSettings').style.display = 'none';
            document.getElementById('navTeam').style.display = 'none';

            // Route guard for views
            const path = window.location.pathname;
            if (path.startsWith('/admin/settings') || path.startsWith('/admin/kra-settings') || path.startsWith('/admin/team')) {
                window.location.href = '/admin/dashboard';
            }
        }
    }

    // Handle logout
    document.getElementById('sidebarLogout').addEventListener('click', async () => {
        try {
            await fetch('/api/admin/logout', { 
                method: 'POST', 
                headers: { 
                    Authorization: `Bearer ${adminToken}`, 
                    Accept: 'application/json' 
                } 
            });
        } catch(e) {}
        localStorage.clear();
        window.location.href = '/admin/login';
    });

    // Global SweetAlert Override for all admin pages
    window.alert = function (message) {
        const lower = String(message).toLowerCase();
        let icon = 'info';
        let title = 'Notification';
        
        if (lower.includes('success') || lower.includes('completed') || lower.includes('verified') || lower.includes('saved')) {
            icon = 'success';
            title = 'Success';
        } else if (lower.includes('fail') || lower.includes('error') || lower.includes('unauthorized') || lower.includes('failed')) {
            icon = 'error';
            title = 'Error';
        }
        
        Swal.fire({
            title: title,
            text: message,
            icon: icon,
            confirmButtonColor: '#004b87',
            customClass: {
                popup: 'premium-swal-popup'
            }
        });
    };
</script>

@yield('scripts')

</body>
</html>

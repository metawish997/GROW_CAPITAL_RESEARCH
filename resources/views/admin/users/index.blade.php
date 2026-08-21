<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Users - Admin Dashboard</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet" />
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        :root {
            --primary: #6C63FF; --amber: #f59e0b; --green: #10b981; --red: #ef4444;
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

        /* Main */
        .main { margin-left: 260px; flex: 1; display: flex; flex-direction: column; }
        .header { height: 64px; background: rgba(8,10,18,0.95); backdrop-filter: blur(12px); border-bottom: 1px solid var(--border); display: flex; align-items: center; justify-content: space-between; padding: 0 32px; position: sticky; top: 0; z-index: 50; }
        .header h2 { font-size: 18px; font-weight: 600; }

        .content { padding: 32px; }

        /* Table Card */
        .table-card { background: var(--card); border: 1px solid var(--border); border-radius: 16px; overflow: hidden; }
        .table-header { padding: 20px 24px; border-bottom: 1px solid var(--border); display: flex; justify-content: space-between; align-items: center; }
        .table-title { font-size: 16px; font-weight: 600; }
        .search-box { background: rgba(255,255,255,0.06); border: 1px solid var(--border); padding: 8px 14px; border-radius: 8px; font-size: 13px; color: var(--text); outline: none; width: 240px; }
        .search-box:focus { border-color: var(--primary); }

        table { width: 100%; border-collapse: collapse; text-align: left; }
        th { padding: 14px 24px; font-size: 11px; font-weight: 600; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 1px solid var(--border); background: rgba(0,0,0,0.1); }
        td { padding: 16px 24px; font-size: 14px; border-bottom: 1px solid var(--border); }
        tr:hover td { background: rgba(255,255,255,0.02); }
        tr:last-child td { border-bottom: none; }

        .user-name { font-weight: 600; }
        .user-contact { font-size: 12px; color: var(--text-muted); margin-top: 4px; }
        
        .badge { display: inline-flex; align-items: center; padding: 4px 10px; border-radius: 20px; font-size: 11px; font-weight: 600; text-transform: capitalize; }
        .badge.approved { background: rgba(16,185,129,0.15); color: #34d399; border: 1px solid rgba(16,185,129,0.3); }
        .badge.pending, .badge.initiated, .badge.approval_pending { background: rgba(108,99,255,0.15); color: #a5b4fc; border: 1px solid rgba(108,99,255,0.3); }
        .badge.failed, .badge.rejected { background: rgba(239,68,68,0.15); color: #f87171; border: 1px solid rgba(239,68,68,0.3); }
        .badge.not-started { background: rgba(255,255,255,0.08); color: var(--text-muted); border: 1px solid var(--border); }

        .btn-view { padding: 6px 12px; background: rgba(255,255,255,0.08); color: var(--text); border: 1px solid var(--border); border-radius: 8px; font-size: 12px; font-weight: 600; text-decoration: none; transition: all 0.2s; }
        .btn-view:hover { background: rgba(108,99,255,0.2); color: var(--primary); border-color: rgba(108,99,255,0.4); }

        .pagination { display: flex; justify-content: center; padding: 20px; gap: 8px; }
        .page-btn { padding: 6px 12px; background: var(--card); border: 1px solid var(--border); border-radius: 6px; color: var(--text); cursor: pointer; font-size: 12px; }
        .page-btn:hover:not(:disabled) { background: rgba(255,255,255,0.1); }
        .page-btn:disabled { opacity: 0.5; cursor: not-allowed; }
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
        <a href="/admin/dashboard" class="nav-item"><span class="icon">🏠</span> Dashboard</a>
        <a href="/admin/users" class="nav-item active"><span class="icon">👥</span> Users</a>
        <div class="nav-section-label">Configuration</div>
        <a href="/admin/settings" class="nav-item"><span class="icon">⚙️</span> API Settings</a>
    </nav>
</aside>

<div class="main">
    <header class="header">
        <h2>Users Management</h2>
    </header>

    <div class="content">
        <div class="table-card">
            <div class="table-header">
                <div class="table-title">All Registered Users</div>
                <input type="text" class="search-box" id="searchInput" placeholder="Search name, mobile or email..." oninput="debounceSearch(event)" />
            </div>
            
            <table id="usersTable">
                <thead>
                    <tr>
                        <th>User Info</th>
                        <th>Role</th>
                        <th>Joined Date</th>
                        <th>KYC Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody id="usersBody">
                    <tr><td colspan="5" style="text-align:center; padding: 40px; color: var(--text-muted);">Loading users...</td></tr>
                </tbody>
            </table>

            <div class="pagination" id="pagination">
                <!-- Pagination buttons generated via JS -->
            </div>
        </div>
    </div>
</div>

<script>
    const adminToken = localStorage.getItem('admin_token');
    if (!adminToken) window.location.href = '/admin/login';

    let currentPage = 1;
    let currentSearch = '';

    function fetchUsers(page = 1, search = '') {
        const url = `/api/admin/users?page=${page}&search=${encodeURIComponent(search)}`;
        
        fetch(url, { headers: { Authorization: `Bearer ${adminToken}`, Accept: 'application/json' } })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    renderUsers(data.users.data);
                    renderPagination(data.users);
                }
            })
            .catch(() => {
                document.getElementById('usersBody').innerHTML = `<tr><td colspan="5" style="text-align:center; padding: 40px; color: #ef4444;">Failed to load users.</td></tr>`;
            });
    }

    function renderUsers(users) {
        const tbody = document.getElementById('usersBody');
        if (users.length === 0) {
            tbody.innerHTML = `<tr><td colspan="5" style="text-align:center; padding: 40px; color: var(--text-muted);">No users found.</td></tr>`;
            return;
        }

        tbody.innerHTML = users.map(u => {
            const date = new Date(u.created_at).toLocaleDateString('en-IN', { year: 'numeric', month: 'short', day: 'numeric' });
            
            let kycBadge = '<span class="badge not-started">Not Started</span>';
            if (u.kyc) {
                const s = u.kyc.status;
                let c = 'pending';
                if(s === 'approved') c = 'approved';
                if(s === 'failed' || s === 'rejected' || s === 'expired') c = 'failed';
                kycBadge = `<span class="badge ${c}">${s.replace('_', ' ')}</span>`;
            }

            return `
                <tr>
                    <td>
                        <div class="user-name">${u.name || '—'}</div>
                        <div class="user-contact">${u.mobile} ${u.email ? '• '+u.email : ''}</div>
                    </td>
                    <td><span style="font-size:12px; color:var(--text-muted); text-transform:uppercase;">${u.role}</span></td>
                    <td>${date}</td>
                    <td>${kycBadge}</td>
                    <td><a href="/admin/users/${u.id}" class="btn-view">View Details</a></td>
                </tr>
            `;
        }).join('');
    }

    function renderPagination(meta) {
        const pag = document.getElementById('pagination');
        if (meta.last_page <= 1) { pag.innerHTML = ''; return; }

        let html = `<button class="page-btn" ${meta.current_page === 1 ? 'disabled' : ''} onclick="fetchUsers(${meta.current_page - 1}, currentSearch)">← Prev</button>`;
        html += `<span style="font-size:13px; color:var(--text-muted); display:flex; align-items:center; margin: 0 10px;">Page ${meta.current_page} of ${meta.last_page}</span>`;
        html += `<button class="page-btn" ${meta.current_page === meta.last_page ? 'disabled' : ''} onclick="fetchUsers(${meta.current_page + 1}, currentSearch)">Next →</button>`;
        
        pag.innerHTML = html;
        currentPage = meta.current_page;
    }

    let searchTimeout;
    function debounceSearch(e) {
        clearTimeout(searchTimeout);
        currentSearch = e.target.value;
        searchTimeout = setTimeout(() => { fetchUsers(1, currentSearch); }, 400);
    }

    // Init
    fetchUsers();
</script>
</body>
</html>

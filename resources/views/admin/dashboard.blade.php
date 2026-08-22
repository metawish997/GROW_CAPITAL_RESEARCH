@extends('layouts.admin')

@section('title', 'Admin Dashboard')
@section('header_title', 'Dashboard')

@section('styles')
    .dashboard-layout {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 24px;
    }
    
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 16px;
        margin-bottom: 24px;
        grid-column: span 2;
    }

    .stat-card {
        background: var(--card);
        border: 1px solid var(--border);
        border-radius: 16px;
        padding: 20px;
        transition: all 0.2s;
        display: flex;
        align-items: center;
        gap: 16px;
    }

    .stat-card:hover {
        border-color: rgba(0,75,135,0.2);
        transform: translateY(-2px);
    }

    .stat-icon {
        width: 44px;
        height: 44px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .stat-details {
        flex: 1;
        min-width: 0;
    }

    .stat-label {
        font-size: 11px;
        color: var(--text-muted);
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .stat-value {
        font-size: 24px;
        font-weight: 700;
        margin-top: 4px;
        color: var(--text-dark);
    }

    /* Columns */
    .dashboard-main {
        display: flex;
        flex-direction: column;
        gap: 24px;
    }

    .dashboard-sidebar {
        display: flex;
        flex-direction: column;
        gap: 24px;
    }

    .card-panel {
        background: var(--card);
        border: 1px solid var(--border);
        border-radius: 16px;
        padding: 24px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.01);
    }

    .card-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 18px;
        border-bottom: 1px solid var(--border);
        padding-bottom: 14px;
    }

    .card-header h3 {
        font-size: 15px;
        font-weight: 700;
        color: var(--text-dark);
    }

    /* Table styles */
    .data-table-wrap {
        overflow-x: auto;
    }

    .data-table {
        width: 100%;
        border-collapse: collapse;
        text-align: left;
    }

    .data-table th {
        font-size: 11px;
        font-weight: 700;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        padding: 12px 16px;
        border-bottom: 1px solid var(--border);
    }

    .data-table td {
        padding: 14px 16px;
        border-bottom: 1px solid var(--border);
        font-size: 13px;
        color: var(--text);
        white-space: nowrap;
    }

    .data-table tr:last-child td {
        border-bottom: none;
    }

    /* Badges */
    .badge {
        display: inline-flex;
        align-items: center;
        padding: 4px 8px;
        border-radius: 6px;
        font-size: 11px;
        font-weight: 600;
    }

    .badge-approved { background: rgba(16,185,129,0.12); color: #047857; }
    .badge-pending { background: rgba(245,158,11,0.12); color: #b45309; }
    .badge-rejected { background: rgba(239,68,68,0.12); color: #b91c1c; }
    .badge-default { background: rgba(100,116,139,0.12); color: #475569; }

    /* Configuration list */
    .config-list {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .config-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 12px 14px;
        background: var(--bg);
        border: 1px solid var(--border);
        border-radius: 10px;
        text-decoration: none;
        color: var(--text);
        transition: all 0.2s;
    }

    .config-item:hover {
        border-color: rgba(0,75,135,0.15);
        background: rgba(0,75,135,0.02);
    }

    .config-info {
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 13px;
        font-weight: 600;
    }

    .config-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: var(--red);
    }

    .config-dot.active {
        background: var(--green);
        box-shadow: 0 0 8px var(--green);
    }

    /* Progress bar */
    .progress-bar-wrap {
        height: 8px;
        background: var(--border);
        border-radius: 4px;
        overflow: hidden;
        margin-top: 12px;
        margin-bottom: 8px;
    }

    .progress-bar-fill {
        height: 100%;
        background: #6366f1;
        width: 0%;
        transition: width 0.6s ease;
        border-radius: 4px;
    }

    .action-link {
        color: var(--primary);
        font-weight: 700;
        text-decoration: none;
        font-size: 12px;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }

    .action-link:hover {
        text-decoration: underline;
    }

    @media (max-width: 992px) {
        .dashboard-layout {
            grid-template-columns: 1fr;
        }
        .stats-grid {
            grid-template-columns: repeat(2, 1fr);
            grid-column: span 1;
        }
    }

    @media (max-width: 576px) {
        .stats-grid {
            grid-template-columns: repeat(2, 1fr);
            gap: 12px;
        }
        .stat-card {
            padding: 12px 10px;
            gap: 10px;
            flex-direction: column;
            text-align: center;
            align-items: center;
        }
        .stat-icon {
            width: 36px;
            height: 36px;
        }
        .stat-icon svg {
            width: 18px;
            height: 18px;
        }
        .stat-label {
            font-size: 10px;
            white-space: normal;
        }
        .stat-value {
            font-size: 18px;
        }
    }
@endsection

@section('content')
    <div class="dashboard-layout">
        <!-- Stats cards header span -->
        <div class="stats-grid">
            <!-- Card 1: Total Users -->
            <div class="stat-card">
                <div class="stat-icon" style="background: rgba(0,75,135,0.08); color: var(--primary);">
                    <svg viewBox="0 0 24 24" width="22" height="22" stroke="currentColor" stroke-width="2.5" fill="none" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                </div>
                <div class="stat-details">
                    <div class="stat-label">Total Customers</div>
                    <div class="stat-value" id="statTotalUsers">—</div>
                </div>
            </div>

            <!-- Card 2: Approved KYC -->
            <div class="stat-card">
                <div class="stat-icon" style="background: rgba(16,185,129,0.08); color: var(--green);">
                    <svg viewBox="0 0 24 24" width="22" height="22" stroke="currentColor" stroke-width="2.5" fill="none" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
                </div>
                <div class="stat-details">
                    <div class="stat-label">Approved KYC</div>
                    <div class="stat-value" id="statKycApproved">—</div>
                </div>
            </div>

            <!-- Card 3: Pending KYC -->
            <div class="stat-card">
                <div class="stat-icon" style="background: rgba(245,158,11,0.08); color: var(--amber);">
                    <svg viewBox="0 0 24 24" width="22" height="22" stroke="currentColor" stroke-width="2.5" fill="none" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                </div>
                <div class="stat-details">
                    <div class="stat-label">Pending Reviews</div>
                    <div class="stat-value" id="statKycPending">—</div>
                </div>
            </div>

            <!-- Card 4: Synced KRA -->
            <div class="stat-card">
                <div class="stat-icon" style="background: rgba(99,102,241,0.08); color: #6366f1;">
                    <svg viewBox="0 0 24 24" width="22" height="22" stroke="currentColor" stroke-width="2.5" fill="none" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="17 8 12 3 7 8"></polyline><line x1="12" y1="3" x2="12" y2="15"></line></svg>
                </div>
                <div class="stat-details">
                    <div class="stat-label">Synced to KRA</div>
                    <div class="stat-value" id="statKraSynced">—</div>
                </div>
            </div>
        </div>

        <!-- Main Column (Left) -->
        <div class="dashboard-main">
            <!-- Recent Submissions -->
            <div class="card-panel">
                <div class="card-header">
                    <h3>Recent Customer Registrations</h3>
                    <a href="/admin/users" class="action-link">
                        View All 
                        <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2.5" fill="none" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
                    </a>
                </div>
                <div class="data-table-wrap">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>KYC Status</th>
                                <th>KRA Status</th>
                                <th>Registered On</th>
                                <th style="text-align: right;">Action</th>
                            </tr>
                        </thead>
                        <tbody id="recentUsersTableBody">
                            <tr>
                                <td colspan="6" style="text-align: center; color: var(--text-muted); padding: 30px;">
                                    ⏳ Loading recent registrations...
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Sidebar Column (Right) -->
        <div class="dashboard-sidebar">
            <!-- KRA Synchronization Progress -->
            <div class="card-panel">
                <div class="card-header">
                    <h3>KRA Upload Progress</h3>
                </div>
                <div style="font-size: 13px; color: var(--text-muted); line-height: 1.5;">
                    Overview of approved customer records successfully uploaded to NDML KRA.
                </div>
                <div class="progress-container">
                    <div class="progress-bar-wrap">
                        <div class="progress-bar-fill" id="kraProgressBar"></div>
                    </div>
                    <div style="display: flex; justify-content: space-between; align-items: center; font-size: 12px; font-weight: 700; color: var(--text-dark);">
                        <span id="kraProgressRatio">0 / 0 Synced</span>
                        <span id="kraProgressPercent">0%</span>
                    </div>
                </div>
            </div>

            <!-- Configuration Check -->
            <div class="card-panel">
                <div class="card-header">
                    <h3>Gateway Configurations</h3>
                </div>
                <div class="config-list">
                    <a href="/admin/settings?tab=smtp" class="config-item">
                        <div class="config-info">
                            <span class="config-dot" id="dot-smtp"></span>
                            <span>SMTP Mail Server</span>
                        </div>
                        <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2.5" fill="none" stroke-linecap="round" stroke-linejoin="round" style="color: var(--text-muted);"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
                    </a>
                    <a href="/admin/settings?tab=digio" class="config-item">
                        <div class="config-info">
                            <span class="config-dot" id="dot-digio"></span>
                            <span>Digio KYC API</span>
                        </div>
                        <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2.5" fill="none" stroke-linecap="round" stroke-linejoin="round" style="color: var(--text-muted);"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
                    </a>
                    <a href="/admin/settings?tab=kra" class="config-item">
                        <div class="config-info">
                            <span class="config-dot" id="dot-kra"></span>
                            <span>NDML KRA API</span>
                        </div>
                        <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2.5" fill="none" stroke-linecap="round" stroke-linejoin="round" style="color: var(--text-muted);"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    // Fetch stats and populate UI
    async function loadDashboardStats() {
        try {
            const res = await fetch('/api/admin/users/dashboard-stats', {
                headers: {
                    'Authorization': `Bearer ${adminToken}`,
                    'Accept': 'application/json'
                }
            });
            const data = await res.json();
            if (!data.success) return;

            const stats = data.stats;

            // Update stats cards
            document.getElementById('statTotalUsers').textContent = stats.total_users;
            document.getElementById('statKycApproved').textContent = stats.kyc_approved;
            document.getElementById('statKycPending').textContent = stats.kyc_pending;
            document.getElementById('statKraSynced').textContent = stats.kra_synced;

            // Update KRA Progress bar
            const totalApproved = stats.kyc_approved;
            const synced = stats.kra_synced;
            const percent = stats.sync_percentage;
            
            document.getElementById('kraProgressBar').style.width = `${percent}%`;
            document.getElementById('kraProgressRatio').textContent = `${synced} / ${totalApproved} Synced`;
            document.getElementById('kraProgressPercent').textContent = `${percent}%`;

            // Populate recent users table
            const tbody = document.getElementById('recentUsersTableBody');
            tbody.innerHTML = '';

            if (stats.recent_users.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="6" style="text-align: center; color: var(--text-muted); padding: 30px;">
                            No customers registered yet.
                        </td>
                    </tr>
                `;
            } else {
                stats.recent_users.forEach(user => {
                    const tr = document.createElement('tr');
                    
                    // Format KYC Status badge
                    let kycBadge = '';
                    if (user.kyc_status === 'approved') {
                        kycBadge = '<span class="badge badge-approved">Approved</span>';
                    } else if (['pending', 'initiated', 'approval_pending'].includes(user.kyc_status)) {
                        kycBadge = '<span class="badge badge-pending">Pending</span>';
                    } else if (['failed', 'rejected', 'expired'].includes(user.kyc_status)) {
                        kycBadge = '<span class="badge badge-rejected">Rejected</span>';
                    } else {
                        kycBadge = '<span class="badge badge-default">Not Started</span>';
                    }

                    // Format KRA Status badge
                    let kraBadge = '';
                    if (user.kra_status === 'synced_to_kra') {
                        kraBadge = '<span class="badge badge-approved">Synced</span>';
                    } else if (user.kyc_status === 'approved') {
                        kraBadge = '<span class="badge badge-pending">Pending</span>';
                    } else {
                        kraBadge = '<span class="badge badge-default">—</span>';
                    }

                    // Format Date
                    const regDate = new Date(user.created_at).toLocaleDateString('en-IN', {
                        day: 'numeric',
                        month: 'short',
                        year: 'numeric'
                    });

                    tr.innerHTML = `
                        <td data-label="Name" style="font-weight: 700; color: var(--text-dark);">${user.name || '—'}</td>
                        <td data-label="Email">${user.email}</td>
                        <td data-label="KYC Status">${kycBadge}</td>
                        <td data-label="KRA Status">${kraBadge}</td>
                        <td data-label="Registered On">${regDate}</td>
                        <td data-label="Action" style="text-align: right;">
                            <a href="/admin/users/${user.id}" class="action-link">Review KYC</a>
                        </td>
                    `;
                    tbody.appendChild(tr);
                });
            }
        } catch (err) {
            console.error('Failed to load dashboard stats:', err);
        }
    }

    // Fetch configuration statuses and populate dot indicators
    async function loadGatewayConfigStatus() {
        try {
            const res = await fetch('/api/admin/settings', {
                headers: {
                    'Authorization': `Bearer ${adminToken}`,
                    'Accept': 'application/json'
                }
            });
            const data = await res.json();
            if (!data.success) return;

            const settings = data.settings;
            
            // SMTP
            if (settings.smtp?.configured) {
                document.getElementById('dot-smtp').classList.add('active');
            }
            // Digio
            if (settings.digio?.configured) {
                document.getElementById('dot-digio').classList.add('active');
            }
            // KRA
            if (settings.kra?.configured) {
                document.getElementById('dot-kra').classList.add('active');
            }
        } catch (err) {
            console.error('Failed to load gateway configs:', err);
        }
    }

    // Initialize
    loadDashboardStats();
    loadGatewayConfigStatus();
</script>
@endsection

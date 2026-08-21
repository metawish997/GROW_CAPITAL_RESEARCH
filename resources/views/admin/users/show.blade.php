<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>User Details - Admin Dashboard</title>
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
        .header h2 { font-size: 18px; font-weight: 600; display: flex; align-items: center; gap: 10px; }
        .back-link { color: var(--text-muted); text-decoration: none; transition: color 0.2s; }
        .back-link:hover { color: var(--text); }

        .content { padding: 32px; display: flex; flex-direction: column; gap: 24px; max-width: 1200px; margin: 0 auto; width: 100%; }

        .info-card { background: var(--card); border: 1px solid var(--border); border-radius: 16px; overflow: hidden; }
        .card-header { padding: 16px 24px; border-bottom: 1px solid var(--border); font-size: 15px; font-weight: 600; background: rgba(0,0,0,0.1); display: flex; justify-content: space-between; align-items: center; gap: 10px; }
        
        .grid-list { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 24px; padding: 24px; }
        .grid-item { display: flex; flex-direction: column; gap: 6px; }
        .grid-label { font-size: 11px; color: var(--text-muted); text-transform: uppercase; font-weight: 600; letter-spacing: 0.5px; }
        .grid-value { font-size: 14px; color: var(--text); font-weight: 500; word-break: break-all; }

        .badge { display: inline-flex; align-items: center; padding: 4px 10px; border-radius: 20px; font-size: 11px; font-weight: 600; text-transform: capitalize; }
        .badge.approved { background: rgba(16,185,129,0.15); color: #34d399; border: 1px solid rgba(16,185,129,0.3); }
        .badge.pending, .badge.initiated, .badge.approval_pending { background: rgba(108,99,255,0.15); color: #a5b4fc; border: 1px solid rgba(108,99,255,0.3); }
        .badge.failed, .badge.rejected { background: rgba(239,68,68,0.15); color: #f87171; border: 1px solid rgba(239,68,68,0.3); }
        .badge.not-started { background: rgba(255,255,255,0.08); color: var(--text-muted); border: 1px solid var(--border); }

        .loader { padding: 40px; text-align: center; color: var(--text-muted); }

        .btn { padding: 8px 16px; border-radius: 8px; font-size: 13px; font-weight: 600; cursor: pointer; border: none; font-family: 'Inter', sans-serif; transition: all 0.2s; display: inline-flex; align-items: center; gap: 6px; }
        .btn-sync { background: rgba(108,99,255,0.15); border: 1px solid rgba(108,99,255,0.3); color: #a5b4fc; }
        .btn-sync:hover { background: rgba(108,99,255,0.25); }

        pre { background: rgba(0,0,0,0.3); padding: 16px; border-radius: 8px; overflow-x: auto; font-size: 12px; color: var(--text-muted); margin-top: 10px; white-space: pre-wrap; word-break: break-all; }
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
        <h2>
            <a href="/admin/users" class="back-link">← Users</a> / <span id="headerName" style="margin-left:8px;">Loading...</span>
        </h2>
    </header>

    <div class="content" id="contentArea">
        <div class="loader">Fetching user details...</div>
    </div>
</div>

<script>
    const adminToken = localStorage.getItem('admin_token');
    if (!adminToken) window.location.href = '/admin/login';
    
    // Inject userId from Laravel blade
    const userId = {{ $id }};

    async function loadUserDetails() {
        try {
            const res = await fetch(`/api/admin/users/${userId}`, {
                headers: { Authorization: `Bearer ${adminToken}`, Accept: 'application/json' }
            });
            const data = await res.json();
            
            if (data.success) {
                renderUser(data.user);
            } else {
                document.getElementById('contentArea').innerHTML = `<div class="loader" style="color:#ef4444;">${data.message || 'Error loading user.'}</div>`;
            }
        } catch (e) {
            document.getElementById('contentArea').innerHTML = `<div class="loader" style="color:#ef4444;">Network error.</div>`;
        }
    }

    function renderUser(u) {
        document.getElementById('headerName').textContent = u.name || 'User Details';

        const joinDate = new Date(u.created_at).toLocaleString('en-IN');
        
        let kycHtml = '';
        if (u.kyc) {
            const k = u.kyc;
            const s = k.status;
            let c = 'pending';
            if(s === 'approved') c = 'approved';
            if(s === 'failed' || s === 'rejected' || s === 'expired') c = 'failed';
            
            let detailsHtml = '';
            if (k.kyc_details && Object.keys(k.kyc_details).length > 0) {
                detailsHtml = `
                    <div style="margin-top:20px; padding-top:20px; border-top: 1px solid var(--border);">
                        <h4 style="font-size:13px; font-weight:600; margin-bottom: 12px; color: var(--text-muted);">Extracted KYC Details</h4>
                        <pre>${JSON.stringify(k.kyc_details, null, 2)}</pre>
                    </div>
                `;
            }

            kycHtml = `
                <div class="info-card">
                    <div class="card-header">
                        KYC Verification Data
                        <button class="btn btn-sync" onclick="syncKyc(${k.id})" id="syncBtn">↻ Sync from Digio</button>
                    </div>
                    <div class="grid-list">
                        <div class="grid-item"><span class="grid-label">Status</span><span class="badge ${c}">${s.replace('_', ' ')}</span></div>
                        <div class="grid-item"><span class="grid-label">Digio Document ID</span><span class="grid-value">${k.digio_document_id || '—'}</span></div>
                        <div class="grid-item"><span class="grid-label">Customer Name</span><span class="grid-value">${k.customer_name || '—'}</span></div>
                        <div class="grid-item"><span class="grid-label">Customer Mobile</span><span class="grid-value">${k.customer_mobile || '—'}</span></div>
                        <div class="grid-item"><span class="grid-label">Initiated At</span><span class="grid-value">${new Date(k.created_at).toLocaleString('en-IN')}</span></div>
                        <div class="grid-item"><span class="grid-label">Completed At</span><span class="grid-value">${k.kyc_completed_at ? new Date(k.kyc_completed_at).toLocaleString('en-IN') : '—'}</span></div>
                    </div>
                    <div style="padding: 0 24px 24px;">
                        ${detailsHtml}
                    </div>
                </div>
            `;
        } else {
            kycHtml = `
                <div class="info-card">
                    <div class="card-header">KYC Verification Data</div>
                    <div class="grid-list">
                        <div class="grid-item" style="grid-column: span 2;">
                            <span class="badge not-started" style="font-size:13px; padding:6px 12px;">KYC Not Started</span>
                            <p style="color:var(--text-muted); font-size:13px; margin-top:8px;">This user has not initiated the KYC process yet.</p>
                        </div>
                    </div>
                </div>
            `;
        }

        let esignHtml = '';
        if (u.esign_agreement) {
            const e = u.esign_agreement;
            const e_status = e.status || 'pending';
            let c_badge = 'pending';
            if(e_status === 'signed') c_badge = 'approved';
            if(e_status === 'failed') c_badge = 'failed';
            
            let viewPdfBtn = e.is_signed ? `<button class="btn btn-sync" style="background: rgba(16,185,129,0.15); color: #34d399; border-color: rgba(16,185,129,0.3);" onclick="fetchAdminSignedPdf(${u.id})">👁️ View PDF</button>` : '';

            esignHtml = `
            <div class="info-card">
                <div class="card-header">
                    E-Sign Agreement Data
                    ${viewPdfBtn}
                </div>
                <div class="grid-list">
                    <div class="grid-item"><span class="grid-label">Status</span><span class="badge ${c_badge}">${e_status.toUpperCase()}</span></div>
                    <div class="grid-item"><span class="grid-label">Digio Document ID</span><span class="grid-value">${e.digio_document_id || '—'}</span></div>
                    <div class="grid-item"><span class="grid-label">IP Address</span><span class="grid-value">${e.ip_address || '—'}</span></div>
                    <div class="grid-item"><span class="grid-label">Signed At</span><span class="grid-value">${e.signed_at ? new Date(e.signed_at).toLocaleString('en-IN') : '—'}</span></div>
                </div>
            </div>
            `;
        } else {
            esignHtml = `
            <div class="info-card">
                <div class="card-header">E-Sign Agreement Data</div>
                <div class="grid-list">
                    <div class="grid-item" style="grid-column: span 2;">
                        <span class="badge not-started" style="font-size:13px; padding:6px 12px;">Not Started</span>
                        <p style="color:var(--text-muted); font-size:13px; margin-top:8px;">This user has not generated or signed the agreement.</p>
                    </div>
                </div>
            </div>
            `;
        }

        document.getElementById('contentArea').innerHTML = `
            <div style="display: grid; grid-template-columns: 1fr 1.5fr; gap: 24px;">
                <!-- Left Column -->
                <div style="display: flex; flex-direction: column; gap: 24px;">
                    <!-- Basic Info -->
                    <div class="info-card">
                        <div class="card-header">Basic Information</div>
                        <div class="grid-list">
                            <div class="grid-item"><span class="grid-label">User ID</span><span class="grid-value">#${u.id}</span></div>
                            <div class="grid-item"><span class="grid-label">Role</span><span class="grid-value" style="text-transform:uppercase;">${u.role}</span></div>
                            <div class="grid-item"><span class="grid-label">Full Name</span><span class="grid-value">${u.name || '—'}</span></div>
                            <div class="grid-item"><span class="grid-label">Email Address</span><span class="grid-value">${u.email || '—'}</span></div>
                            <div class="grid-item"><span class="grid-label">Mobile Number</span><span class="grid-value">${u.mobile}</span></div>
                            <div class="grid-item"><span class="grid-label">Joined On</span><span class="grid-value">${joinDate}</span></div>
                        </div>
                    </div>

                    <!-- E-Sign Info -->
                    ${esignHtml}
                </div>

                <!-- Right Column (Wider for KYC) -->
                <div>
                    ${kycHtml}
                </div>
            </div>
        `;
    }

    async function syncKyc(kycId) {
        const btn = document.getElementById('syncBtn');
        btn.textContent = '⏳ Syncing...';
        btn.disabled = true;

        try {
            const res = await fetch(`/api/admin/kyc/${kycId}/sync`, {
                method: 'POST',
                headers: { Authorization: `Bearer ${adminToken}`, Accept: 'application/json' }
            });
            const data = await res.json();
            
            if(data.success) {
                alert('KYC Sync Successful!');
                loadUserDetails();
            } else {
                alert('Sync failed: ' + (data.message || 'Unknown error'));
                btn.textContent = '↻ Sync from Digio';
                btn.disabled = false;
            }
        } catch (e) {
            alert('Network error during sync');
            btn.textContent = '↻ Sync from Digio';
            btn.disabled = false;
        }
    }

    async function fetchAdminSignedPdf(uid) {
        try {
            const res = await fetch(`/api/admin/users/${uid}/esign`, {
                headers: { 'Authorization': `Bearer ${adminToken}` }
            });
            if (!res.ok) throw new Error('Failed to load PDF');
            const blob = await res.blob();
            const blobUrl = URL.createObjectURL(blob);
            window.open(blobUrl, '_blank');
        } catch (e) {
            console.error(e);
            alert('Failed to load the signed agreement.');
        }
    }

    // Init
    loadUserDetails();
</script>
</body>
</html>

@extends('layouts.admin')

@section('title', 'Customer Management')
@section('header_title', 'Customer Management')

@section('styles')
    /* Table Card */
    .table-card { background: var(--card); border: 1px solid var(--border); border-radius: 16px; overflow: hidden; }
    .table-header { padding: 20px 24px; border-bottom: 1px solid var(--border); display: flex; justify-content: space-between; align-items: center; }
    .table-title { font-size: 16px; font-weight: 600; }
    .search-box { background: #f8fafc; border: 1px solid var(--border); padding: 10px 16px; border-radius: 10px; font-size: 13px; color: var(--text); outline: none; width: 260px; font-family: inherit; transition: all 0.2s; }
    .search-box:focus { border-color: var(--primary); box-shadow: 0 0 0 3px rgba(0,75,135,0.08); }

    table { width: 100%; border-collapse: collapse; text-align: left; }
    th { padding: 14px 24px; font-size: 11px; font-weight: 600; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 1px solid var(--border); background: #f8fafc; }
    td { padding: 16px 24px; font-size: 14px; border-bottom: 1px solid var(--border); }
    tr:hover td { background: #f8fafc; }
    tr:last-child td { border-bottom: none; }

    .user-link-name {
        font-weight: 600;
        color: var(--primary);
        text-decoration: none;
        transition: color 0.2s;
        display: inline-block;
    }
    .user-link-name:hover {
        color: var(--primary-dark);
        text-decoration: underline;
    }
    .user-contact { font-size: 12px; color: var(--text-muted); margin-top: 4px; }
    
    .badge { display: inline-flex; align-items: center; padding: 4px 10px; border-radius: 20px; font-size: 11px; font-weight: 600; text-transform: capitalize; }
    .badge.approved { background: rgba(16,185,129,0.12); color: var(--green); border: 1px solid rgba(16,185,129,0.25); }
    .badge.pending, .badge.initiated, .badge.approval_pending { background: rgba(0,75,135,0.08); color: var(--primary); border: 1px solid rgba(0,75,135,0.15); }
    .badge.failed, .badge.rejected { background: rgba(239,68,68,0.1); color: var(--red); border: 1px solid rgba(239,68,68,0.2); }
    .badge.not-started { background: rgba(0,0,0,0.04); color: var(--text-muted); border: 1px solid var(--border); }

    .btn-view { padding: 8px 16px; background: #ffffff; color: var(--text); border: 1px solid var(--border); border-radius: 8px; font-size: 12px; font-weight: 600; text-decoration: none; transition: all 0.2s; display: inline-block; }
    .btn-view:hover { background: rgba(0,75,135,0.06); color: var(--primary); border-color: rgba(0,75,135,0.2); }

    .pagination { display: flex; justify-content: center; padding: 20px; gap: 8px; }
    .page-btn { padding: 6px 12px; background: var(--card); border: 1px solid var(--border); border-radius: 6px; color: var(--text); cursor: pointer; font-size: 12px; font-family: inherit; transition: all 0.2s; }
    .page-btn:hover:not(:disabled) { background: #f8fafc; }
    .page-btn:disabled { opacity: 0.5; cursor: not-allowed; }

    .sync-action-btn {
        background: none;
        border: none;
        cursor: pointer;
        color: var(--primary);
        padding: 4px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        transition: all 0.2s;
        vertical-align: middle;
    }
    .sync-action-btn:hover {
        background: rgba(0,75,135,0.08);
        transform: rotate(45deg);
    }
    .sync-action-btn.spinning svg {
        animation: spin 1s linear infinite;
    }
    @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }

    .status-badge-container { display:flex; flex-direction:column; align-items:flex-start; }
    
    @media (max-width: 768px) {
        .table-header { flex-direction: row; align-items: center; justify-content: space-between; gap: 10px; padding: 12px 16px; }
        .table-title { font-size: 14px; white-space: nowrap; }
        .table-header > div:last-child { flex: 1; }
        .search-box { width: 100%; min-width: 0; padding: 8px 12px; font-size: 12px; }
        .status-badge-container { align-items: flex-end; }
    }
@endsection

@section('content')
    <div class="table-card">
        <div class="table-header">
            <div class="table-title">All Customers</div>
            <div style="display: flex; gap: 12px; align-items: center;">
                <input type="text" class="search-box" id="searchInput" placeholder="Search name, mobile or email..." oninput="debounceSearch(event)" />
                {{--
                <button class="btn" onclick="openCreateModal()" style="background: var(--primary); color: #ffffff; border-color: var(--primary); font-size: 13px; padding: 10px 20px; font-weight:600; display: inline-flex; align-items: center; gap: 6px;">
                    <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2.5" fill="none" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                    Create Customer
                </button>
                --}}
            </div>
        </div>
        
        <table id="usersTable" class="data-table">
            <thead>
                <tr>
                    <th>User Info</th>
                    <th>Joined Date</th>
                    <th>KYC Status</th>
                    <th>Agreement Status</th>
                    <th>KRA Status</th>
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

    <!-- Create Customer Modal -->
    <div id="createModal" class="modal-overlay" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(15,23,42,0.4); backdrop-filter: blur(4px); z-index: 1000; justify-content: center; align-items: center; padding: 20px;">
        <div style="background: #ffffff; border-radius: 16px; width: 100%; max-width: 480px; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04); overflow: hidden;">
            <div style="padding: 24px; border-bottom: 1px solid var(--border); display: flex; justify-content: space-between; align-items: center;">
                <h3 style="font-size: 16px; font-weight: 700; color: var(--text-dark); margin:0;">Create Customer Profile</h3>
                <button onclick="closeCreateModal()" style="background: none; border: none; font-size: 20px; color: var(--text-muted); cursor: pointer;">&times;</button>
            </div>
            
            <form id="createForm" onsubmit="submitCreateCustomer(event)" style="padding: 24px; display: flex; flex-direction: column; gap: 16px;">
                <div>
                    <label style="display: block; font-size: 11px; font-weight: 600; text-transform: uppercase; color: var(--text-muted); margin-bottom: 6px; letter-spacing: 0.5px;">Customer Name</label>
                    <input type="text" name="name" required style="width: 100%; padding: 10px 14px; border: 1px solid var(--border); border-radius: 8px; font-size: 14px; outline: none; font-family:inherit;" placeholder="e.g. Narendra Singh">
                </div>
                
                <div>
                    <label style="display: block; font-size: 11px; font-weight: 600; text-transform: uppercase; color: var(--text-muted); margin-bottom: 6px; letter-spacing: 0.5px;">Email Address</label>
                    <input type="email" name="email" required style="width: 100%; padding: 10px 14px; border: 1px solid var(--border); border-radius: 8px; font-size: 14px; outline: none; font-family:inherit;" placeholder="e.g. client@example.com">
                </div>
                
                <div>
                    <label style="display: block; font-size: 11px; font-weight: 600; text-transform: uppercase; color: var(--text-muted); margin-bottom: 6px; letter-spacing: 0.5px;">Mobile Number</label>
                    <input type="text" name="mobile" required style="width: 100%; padding: 10px 14px; border: 1px solid var(--border); border-radius: 8px; font-size: 14px; outline: none; font-family:inherit;" placeholder="e.g. 7999308418">
                </div>
                
                <div>
                    <label style="display: block; font-size: 11px; font-weight: 600; text-transform: uppercase; color: var(--text-muted); margin-bottom: 6px; letter-spacing: 0.5px;">PAN Card (Optional)</label>
                    <input type="text" name="pan_card" style="width: 100%; padding: 10px 14px; border: 1px solid var(--border); border-radius: 8px; font-size: 14px; outline: none; font-family:inherit;" placeholder="e.g. ABCDE1234F">
                </div>
                
                <div style="display: flex; justify-content: flex-end; gap: 12px; margin-top: 10px; border-top: 1px solid var(--border); padding-top: 16px;">
                    <button type="button" class="btn" onclick="closeCreateModal()" style="padding: 10px 16px; border-color: var(--border); background: #ffffff; font-family:inherit; font-size:13px;">Cancel</button>
                    <button type="submit" class="btn" style="padding: 10px 16px; background: var(--primary); color: #ffffff; border-color: var(--primary); font-family:inherit; font-size:13px;">Create & Start KYC</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
<script>
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
                document.getElementById('usersBody').innerHTML = `<tr><td colspan="6" style="text-align:center; padding: 40px; color: var(--red);">Failed to load users.</td></tr>`;
            });
    }

    function formatStatusTime(dateTimeStr) {
        if (!dateTimeStr) return '';
        const d = new Date(dateTimeStr);
        if (isNaN(d.getTime())) return '';
        return d.toLocaleDateString('en-IN', { 
            day: 'numeric', 
            month: 'short', 
            hour: '2-digit', 
            minute: '2-digit',
            hour12: true
        });
    }

    function renderUsers(users) {
        const tbody = document.getElementById('usersBody');
        if (users.length === 0) {
            tbody.innerHTML = `<tr><td colspan="6" style="text-align:center; padding: 40px; color: var(--text-muted);">No users found.</td></tr>`;
            return;
        }

        tbody.innerHTML = users.map(u => {
            const date = new Date(u.created_at).toLocaleDateString('en-IN', { year: 'numeric', month: 'short', day: 'numeric' });
            
            let kycBadge = '<span class="badge not-started">Not Started</span>';
            if (u.kyc) {
                const s = u.kyc.status;
                let c = 'pending';
                let displayText = s.replace('_', ' ');
                if(s === 'approved') {
                    c = 'approved';
                    displayText = 'completed';
                }
                if(s === 'failed' || s === 'rejected' || s === 'expired') c = 'failed';
                
                const timeStr = u.kyc.kyc_completed_at || u.kyc.updated_at;
                const timeHtml = s === 'approved' ? `<div style="font-size: 10px; color: var(--text-muted); margin-top: 4px;">${formatStatusTime(timeStr)}</div>` : '';
                kycBadge = `
                    <div class="status-badge-container">
                        <span class="badge ${c}">${displayText}</span>
                        ${timeHtml}
                    </div>
                `;
            }

            let esignBadge = '<span class="badge not-started">Not Started</span>';
            if (u.esign_agreement) {
                const s = u.esign_agreement.status || 'pending';
                let c = 'pending';
                if(s === 'signed') c = 'approved';
                if(s === 'failed') c = 'failed';
                
                const timeStr = u.esign_agreement.updated_at;
                const timeHtml = s === 'signed' ? `<div style="font-size: 10px; color: var(--text-muted); margin-top: 4px;">${formatStatusTime(timeStr)}</div>` : '';
                esignBadge = `
                    <div class="status-badge-container">
                        <span class="badge ${c}">${s}</span>
                        ${timeHtml}
                    </div>
                `;
            }

            let kraBadge = '—';
            if (u.kyc && u.kyc.status === 'approved') {
                if (u.kyc.callback_status === 'synced_to_kra') {
                    const timeStr = u.kyc.updated_at;
                    kraBadge = `
                        <div style="display:flex; flex-direction:column; align-items:flex-start;">
                            <span class="badge approved">Synced</span>
                            <div style="font-size: 10px; color: var(--text-muted); margin-top: 4px;">${formatStatusTime(timeStr)}</div>
                        </div>
                    `;
                } else {
                    kraBadge = `
                        <div class="status-badge-container" style="gap:4px;">
                            <span style="display:inline-flex; align-items:center; gap:8px;">
                                <span class="badge failed">Not Synced</span>
                                <button class="sync-action-btn" onclick="syncKra(${u.id}, this)" title="Sync to KRA Now">
                                    <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2.5" fill="none" stroke-linecap="round" stroke-linejoin="round"><polyline points="23 4 23 10 17 10"></polyline><polyline points="1 20 1 14 7 14"></polyline><path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"></path></svg>
                                </button>
                            </span>
                        </div>
                    `;
                }
            }

            const displayName = (u.kyc && u.kyc.customer_name) ? u.kyc.customer_name : (u.name || '—');

            return `
                <tr>
                    <td data-label="User Info">
                        <a href="/admin/users/${u.id}" class="user-link-name">${displayName}</a>
                        <div class="user-contact">${u.mobile} ${u.email ? '• '+u.email : ''}</div>
                    </td>
                    <td data-label="Joined Date">${date}</td>
                    <td data-label="KYC Status">${kycBadge}</td>
                    <td data-label="Agreement Status">${esignBadge}</td>
                    <td data-label="KRA Status">${kraBadge}</td>
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

    async function syncKra(userId, btn) {
        if (btn.classList.contains('spinning')) return;
        btn.classList.add('spinning');
        
        try {
            const res = await fetch(`/admin/users/${userId}/reupload`, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Authorization': 'Bearer ' + adminToken
                }
            });
            const data = await res.json();
            if (data.success) {
                alert('Sync Successful: ' + data.message);
                fetchUsers(currentPage, currentSearch);
            } else {
                alert('Sync failed: ' + (data.message || 'Unknown error'));
            }
        } catch (err) {
            alert('Network error during sync.');
        } finally {
            btn.classList.remove('spinning');
        }
    }

    function openCreateModal() {
        document.getElementById('createModal').style.display = 'flex';
        document.getElementById('createForm').reset();
    }

    function closeCreateModal() {
        document.getElementById('createModal').style.display = 'none';
    }

    async function submitCreateCustomer(e) {
        e.preventDefault();
        const form = document.getElementById('createForm');
        const submitBtn = form.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        submitBtn.innerHTML = 'Creating...';
        submitBtn.disabled = true;

        const payload = {
            name: form.name.value,
            email: form.email.value,
            mobile: form.mobile.value,
            pan_card: form.pan_card.value
        };

        try {
            const res = await fetch('/api/admin/users', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Authorization': 'Bearer ' + adminToken
                },
                body: JSON.stringify(payload)
            });
            const data = await res.json();
            
            if (res.ok && data.success) {
                closeCreateModal();
                
                const link = data.digio_kyc_url || '';
                
                if (link) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Customer Profile Created',
                        html: `
                            <p style="font-size:14px; margin-bottom:15px; color:#475569;">Customer profile created successfully and Digio KYC session has been initiated.</p>
                            <div style="background:#f0f7ff; border:1px solid #004b87; padding:12px; border-radius:8px; text-align:left;">
                                <label style="display:block; font-size:11px; font-weight:700; color:#004b87; margin-bottom:6px; text-transform:uppercase;">Digio KYC Link</label>
                                <input type="text" value="${link}" id="swalKycLink" readonly style="width:100%; padding:8px; border:1px solid var(--border); border-radius:6px; font-size:12px; background:#fff; color:var(--text-dark);" onclick="this.select()">
                                <button onclick="navigator.clipboard.writeText('${link}').then(() => { Swal.fire('Copied', 'Digio KYC link copied to clipboard!', 'success'); })" style="margin-top:10px; width:100%; background:var(--primary); color:#fff; border:none; padding:8px; border-radius:6px; font-size:13px; font-weight:600; cursor:pointer;">Copy Link</button>
                            </div>
                        `,
                        confirmButtonText: 'Done',
                        confirmButtonColor: 'var(--primary)'
                    });
                } else {
                    Swal.fire({
                        icon: 'success',
                        title: 'Created Successfully',
                        text: 'Customer profile was created, but we could not auto-initiate Digio KYC. You can generate it later from their details page.',
                        confirmButtonColor: 'var(--primary)'
                    });
                }
                
                fetchUsers(1, currentSearch);
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Creation Failed',
                    text: data.message || 'Unknown error occurred.',
                    confirmButtonColor: 'var(--primary)'
                });
            }
        } catch (err) {
            console.error(err);
            Swal.fire({
                icon: 'error',
                title: 'Network Error',
                text: 'Failed to communicate with the server.',
                confirmButtonColor: 'var(--primary)'
            });
        } finally {
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
        }
    }

    // Init
    fetchUsers();
</script>
@endsection

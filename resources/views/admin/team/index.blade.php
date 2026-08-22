@extends('layouts.admin')

@section('title', 'Team Management')
@section('header_title', 'Team Management')
@section('header_actions')
    <button class="action-btn-add" onclick="openModal()">
        <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2.5" fill="none" stroke-linecap="round" stroke-linejoin="round" style="margin-right:6px;"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
        Add Member
    </button>
@endsection

@section('styles')
    /* Table Card */
    .table-card { background: var(--card); border: 1px solid var(--border); border-radius: 16px; overflow: hidden; }
    .table-header { padding: 20px 24px; border-bottom: 1px solid var(--border); display: flex; justify-content: space-between; align-items: center; }
    .table-title { font-size: 16px; font-weight: 600; color: var(--text-dark); }
    .search-box { background: #f8fafc; border: 1px solid var(--border); padding: 10px 16px; border-radius: 10px; font-size: 13px; color: var(--text); outline: none; width: 260px; font-family: inherit; transition: all 0.2s; }
    .search-box:focus { border-color: var(--primary); box-shadow: 0 0 0 3px rgba(0,75,135,0.08); }

    table { width: 100%; border-collapse: collapse; text-align: left; }
    th { padding: 14px 24px; font-size: 11px; font-weight: 600; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 1px solid var(--border); background: #f8fafc; }
    td { padding: 16px 24px; font-size: 14px; border-bottom: 1px solid var(--border); }
    tr:hover td { background: #f8fafc; }
    tr:last-child td { border-bottom: none; }

    .user-name { font-weight: 600; color: var(--text-dark); }
    .user-email { font-size: 12px; color: var(--text-muted); margin-top: 4px; }
    
    .badge { display: inline-flex; align-items: center; padding: 4px 10px; border-radius: 20px; font-size: 11px; font-weight: 600; text-transform: capitalize; }
    .badge.admin { background: rgba(0,75,135,0.08); color: var(--primary); border: 1px solid rgba(0,75,135,0.15); }
    .badge.staff { background: rgba(152,209,2,0.15); color: var(--text-dark); border: 1px solid rgba(152,209,2,0.3); }

    .action-btn-add {
        padding: 10px 20px;
        background: var(--primary);
        color: #ffffff;
        border: none;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.2s;
        display: inline-flex;
        align-items: center;
        font-family: inherit;
    }
    .action-btn-add:hover { background: var(--primary-dark); transform: translateY(-1px); }

    .btn-edit { padding: 6px 12px; background: #ffffff; color: var(--text); border: 1px solid var(--border); border-radius: 8px; font-size: 12px; font-weight: 600; text-decoration: none; transition: all 0.2s; display: inline-block; cursor: pointer; margin-right: 6px; }
    .btn-edit:hover { background: rgba(0,75,135,0.06); color: var(--primary); border-color: rgba(0,75,135,0.2); }

    .btn-delete { padding: 6px 12px; background: #ffffff; color: var(--red); border: 1px solid rgba(239,68,68,0.2); border-radius: 8px; font-size: 12px; font-weight: 600; text-decoration: none; transition: all 0.2s; display: inline-block; cursor: pointer; }
    .btn-delete:hover { background: rgba(239,68,68,0.06); border-color: var(--red); }

    /* Modal Styling */
    .modal {
        position: fixed;
        inset: 0;
        background: rgba(11,45,73,0.5);
        backdrop-filter: blur(4px);
        display: none;
        align-items: center;
        justify-content: center;
        z-index: 1000;
        opacity: 0;
        transition: opacity 0.3s ease;
    }
    .modal.active { display: flex; opacity: 1; }
    
    .modal-card {
        background: #ffffff;
        border-radius: 20px;
        width: 100%;
        max-width: 480px;
        padding: 32px;
        box-shadow: 0 20px 40px rgba(0,0,0,0.15);
        transform: translateY(20px);
        transition: transform 0.3s ease;
    }
    .modal.active .modal-card { transform: translateY(0); }

    .modal-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; }
    .modal-header h3 { font-size: 18px; font-weight: 700; color: var(--text-dark); }
    .modal-close { background: none; border: none; font-size: 22px; cursor: pointer; color: var(--text-muted); line-height: 1; }
    .modal-close:hover { color: var(--text-dark); }

    .form-group { display: flex; flex-direction: column; gap: 7px; margin-bottom: 20px; }
    .form-group label { font-size: 12px; font-weight: 600; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.5px; }
    .form-group input, .form-group select { padding: 12px 14px; background: #ffffff; border: 1px solid var(--border); border-radius: 10px; color: var(--text); font-size: 14px; font-family: inherit; outline: none; transition: all 0.2s; }
    .form-group input:focus, .form-group select:focus { border-color: var(--primary); box-shadow: 0 0 0 3px rgba(0,75,135,0.08); }
    .form-group select option { background: #ffffff; color: var(--text-dark); }

    .modal-footer { display: flex; justify-content: flex-end; gap: 12px; margin-top: 28px; }
    .btn-cancel { padding: 12px 24px; background: #f1f5f9; color: var(--text-dark); border: none; border-radius: 8px; font-size: 14px; font-weight: 600; cursor: pointer; font-family: inherit; transition: all 0.2s; }
    .btn-cancel:hover { background: #e2e8f0; }
    
    .btn-submit { padding: 12px 24px; background: var(--primary); color: #ffffff; border: none; border-radius: 8px; font-size: 14px; font-weight: 700; cursor: pointer; font-family: inherit; transition: all 0.2s; }
    .btn-submit:hover { background: var(--primary-dark); }

    .pagination { display: flex; justify-content: center; padding: 20px; gap: 8px; }
    .page-btn { padding: 6px 12px; background: var(--card); border: 1px solid var(--border); border-radius: 6px; color: var(--text); cursor: pointer; font-size: 12px; font-family: inherit; transition: all 0.2s; }
    .page-btn:hover:not(:disabled) { background: #f8fafc; }
    .page-btn:disabled { opacity: 0.5; cursor: not-allowed; }

    @media (max-width: 768px) {
        .table-header { flex-direction: row; align-items: center; justify-content: space-between; gap: 8px; padding: 12px; flex-wrap: nowrap; }
        .table-title { font-size: 13px; text-align: left; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .search-box { width: 140px; padding: 8px 12px; font-size: 11px; flex-shrink: 0; }
        .modal-card { padding: 20px; border-radius: 16px; margin: 16px; width: calc(100% - 32px); }
        .modal-header h3 { font-size: 16px; }
        .form-group label { font-size: 10px; }
        .form-group input, .form-group select { font-size: 12px; padding: 10px 12px; }
        .btn-cancel, .btn-submit { padding: 10px 16px; font-size: 12px; flex: 1; text-align: center; justify-content: center; }
        .modal-footer { gap: 8px; }
        .user-name, .user-email { text-align: right; }
        .btn-edit, .btn-delete { padding: 8px 12px; margin: 0 0 0 6px; }
    }
@endsection

@section('content')
    <div class="table-card">
        <div class="table-header">
            <div class="table-title">Manage Internal Team Members</div>
            <input type="text" class="search-box" id="searchInput" placeholder="Search name or email..." oninput="debounceSearch(event)" />
        </div>
        
        <table class="data-table">
            <thead>
                <tr>
                    <th>Member Info</th>
                    <th>Role</th>
                    <th>Date Added</th>
                    <th style="width: 180px;">Action</th>
                </tr>
            </thead>
            <tbody id="teamBody">
                <tr><td colspan="4" style="text-align:center; padding: 40px; color: var(--text-muted);">Loading team members...</td></tr>
            </tbody>
        </table>

        <div class="pagination" id="pagination"></div>
    </div>

    <!-- Modal Form (Add / Edit) -->
    <div class="modal" id="memberModal" onclick="closeModalOnOutsideClick(event)">
        <div class="modal-card">
            <div class="modal-header">
                <h3 id="modalTitle">Add Team Member</h3>
                <button class="modal-close" onclick="closeModal()">&times;</button>
            </div>
            <form id="memberForm" onsubmit="handleFormSubmit(event)">
                <input type="hidden" id="member-id" name="id" />
                
                <div class="form-group">
                    <label for="member-name">Full Name</label>
                    <input type="text" id="member-name" name="name" placeholder="John Doe" required />
                </div>
                
                <div class="form-group">
                    <label for="member-email">Email Address</label>
                    <input type="email" id="member-email" name="email" placeholder="john@growcapitals.com" required autocomplete="email" />
                </div>
                
                <div class="form-group">
                    <label for="member-role">System Role</label>
                    <select id="member-role" name="role" required>
                        <option value="staff">Staff Admin (Restricted settings access)</option>
                        <option value="admin">Super Admin (Full settings access)</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="member-password" id="pwLabel">Password</label>
                    <input type="password" id="member-password" name="password" placeholder="Enter password (min 6 characters)" required autocomplete="new-password" />
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn-cancel" onclick="closeModal()">Cancel</button>
                    <button type="submit" class="btn-submit" id="submitBtn">Save Member</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    let currentPage = 1;
    let currentSearch = '';
    let editMode = false;

    function fetchTeam(page = 1, search = '') {
        const url = `/api/admin/team?page=${page}&search=${encodeURIComponent(search)}`;
        
        fetch(url, { headers: { Authorization: `Bearer ${adminToken}`, Accept: 'application/json' } })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    renderTeam(data.team.data);
                    renderPagination(data.team);
                }
            })
            .catch(() => {
                document.getElementById('teamBody').innerHTML = `<tr><td colspan="4" style="text-align:center; padding: 40px; color: var(--red);">Failed to load team list.</td></tr>`;
            });
    }

    function renderTeam(team) {
        const tbody = document.getElementById('teamBody');
        if (team.length === 0) {
            tbody.innerHTML = `<tr><td colspan="4" style="text-align:center; padding: 40px; color: var(--text-muted);">No team members found.</td></tr>`;
            return;
        }

        tbody.innerHTML = team.map(t => {
            const date = new Date(t.created_at).toLocaleDateString('en-IN', { year: 'numeric', month: 'short', day: 'numeric' });
            const roleBadge = t.role === 'admin' 
                ? '<span class="badge admin">Super Admin</span>' 
                : '<span class="badge staff">Staff Admin</span>';
            
            return `
                <tr>
                    <td data-label="Member Info">
                        <div class="user-name">${t.name}</div>
                        <div class="user-email">${t.email}</div>
                    </td>
                    <td data-label="Role">${roleBadge}</td>
                    <td data-label="Date Added">${date}</td>
                    <td data-label="Action">
                        <div style="display:flex; justify-content:flex-end;">
                            <button class="btn-edit" onclick="editMember(${JSON.stringify(t).replace(/"/g, '&quot;')})">Edit</button>
                            <button class="btn-delete" onclick="deleteMember(${t.id})">Delete</button>
                        </div>
                    </td>
                </tr>
            `;
        }).join('');
    }

    function renderPagination(meta) {
        const pag = document.getElementById('pagination');
        if (meta.last_page <= 1) { pag.innerHTML = ''; return; }

        let html = `<button class="page-btn" ${meta.current_page === 1 ? 'disabled' : ''} onclick="fetchTeam(${meta.current_page - 1}, currentSearch)">← Prev</button>`;
        html += `<span style="font-size:13px; color:var(--text-muted); display:flex; align-items:center; margin: 0 10px;">Page ${meta.current_page} of ${meta.last_page}</span>`;
        html += `<button class="page-btn" ${meta.current_page === meta.last_page ? 'disabled' : ''} onclick="fetchTeam(${meta.current_page + 1}, currentSearch)">Next →</button>`;
        
        pag.innerHTML = html;
        currentPage = meta.current_page;
    }

    let searchTimeout;
    function debounceSearch(e) {
        clearTimeout(searchTimeout);
        currentSearch = e.target.value;
        searchTimeout = setTimeout(() => { fetchTeam(1, currentSearch); }, 400);
    }

    // Modal helpers
    function openModal() {
        editMode = false;
        document.getElementById('modalTitle').textContent = 'Add Team Member';
        document.getElementById('memberForm').reset();
        document.getElementById('member-id').value = '';
        document.getElementById('member-password').required = true;
        document.getElementById('pwLabel').textContent = 'Password';
        document.getElementById('member-password').placeholder = 'Enter password (min 6 characters)';
        
        const modal = document.getElementById('memberModal');
        modal.style.display = 'flex';
        setTimeout(() => modal.classList.add('active'), 10);
    }

    function closeModal() {
        const modal = document.getElementById('memberModal');
        modal.classList.remove('active');
        setTimeout(() => modal.style.display = 'none', 300);
    }

    function closeModalOnOutsideClick(e) {
        if (e.target.id === 'memberModal') {
            closeModal();
        }
    }

    function editMember(t) {
        editMode = true;
        document.getElementById('modalTitle').textContent = 'Edit Team Member';
        document.getElementById('member-id').value = t.id;
        document.getElementById('member-name').value = t.name;
        document.getElementById('member-email').value = t.email;
        document.getElementById('member-role').value = t.role;
        document.getElementById('member-password').required = false;
        document.getElementById('pwLabel').textContent = 'Password (leave blank to keep current)';
        document.getElementById('member-password').placeholder = '••••••';
        document.getElementById('member-password').value = '';
        
        const modal = document.getElementById('memberModal');
        modal.style.display = 'flex';
        setTimeout(() => modal.classList.add('active'), 10);
    }

    async function handleFormSubmit(e) {
        e.preventDefault();
        const form = document.getElementById('memberForm');
        const submitBtn = document.getElementById('submitBtn');
        const formData = new FormData(form);
        const payload = Object.fromEntries(formData.entries());
        
        const id = payload.id;
        const url = id ? `/api/admin/team/${id}` : '/api/admin/team';
        const method = id ? 'PUT' : 'POST';
        
        submitBtn.disabled = true;
        submitBtn.textContent = 'Saving...';
        
        try {
            const res = await fetch(url, {
                method: method,
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'Authorization': `Bearer ${adminToken}`
                },
                body: JSON.stringify(payload)
            });
            const data = await res.json();
            
            if (data.success) {
                alert(data.message);
                closeModal();
                fetchTeam(currentPage, currentSearch);
            } else {
                alert(data.message || 'Error occurred while saving.');
            }
        } catch (err) {
            alert('Network error. Please try again.');
        } finally {
            submitBtn.disabled = false;
            submitBtn.textContent = 'Save Member';
        }
    }

    async function deleteMember(id) {
        if (!confirm('Are you sure you want to delete this team member? This action is permanent and their access tokens will be revoked.')) {
            return;
        }

        try {
            const res = await fetch(`/api/admin/team/${id}`, {
                method: 'DELETE',
                headers: {
                    'Accept': 'application/json',
                    'Authorization': `Bearer ${adminToken}`
                }
            });
            const data = await res.json();
            
            if (data.success) {
                alert(data.message);
                fetchTeam(currentPage, currentSearch);
            } else {
                alert(data.message || 'Error occurred while deleting.');
            }
        } catch (err) {
            alert('Network error. Please try again.');
        }
    }

    // Init
    fetchTeam();
</script>
@endsection

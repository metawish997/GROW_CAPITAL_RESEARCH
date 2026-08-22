@extends('layouts.admin')

@section('title', 'Change Password')
@section('header_title', 'Change Password')

@section('styles')
    .panel-card {
        background: var(--card);
        border: 1px solid var(--border);
        border-radius: 20px;
        padding: 32px;
        max-width: 520px;
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
        background: rgba(0,75,135,0.12);
    }
    .panel-title-text h3 { font-size: 18px; font-weight: 600; color: var(--text-dark); }
    .panel-title-text p  { font-size: 13px; color: var(--text-muted); margin-top: 2px; }

    .form-group { display: flex; flex-direction: column; gap: 7px; margin-bottom: 20px; }
    .form-group label {
        font-size: 12px;
        font-weight: 600;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .form-group input {
        padding: 12px 14px;
        background: #ffffff;
        border: 1px solid var(--border);
        border-radius: 10px;
        color: var(--text);
        font-size: 14px;
        font-family: inherit;
        outline: none;
        transition: all 0.2s;
    }
    .form-group input:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(0,75,135,0.08);
    }
    .btn-save {
        padding: 13px 32px;
        background: var(--primary);
        color: #fff;
        border: none;
        border-radius: 12px;
        font-size: 14px;
        font-weight: 600;
        font-family: inherit;
        cursor: pointer;
        transition: all 0.25s;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        margin-top: 8px;
    }
    .btn-save:hover { transform: translateY(-1px); box-shadow: 0 6px 20px rgba(0,75,135,0.25); }
    .btn-save:disabled { opacity: 0.6; cursor: not-allowed; transform: none; }

    @media (max-width: 768px) {
        .panel-card { padding: 16px; border-radius: 16px; }
        .panel-title { gap: 10px; margin-bottom: 16px; }
        .panel-title-text h3 { font-size: 15px; }
        .panel-title-text p { font-size: 11px; }
        .panel-icon { width: 36px; height: 36px; }
        .panel-icon svg { width: 18px; height: 18px; }
        .form-group label { font-size: 10px; }
        .form-group input { font-size: 12px; padding: 10px 12px; }
        .btn-save { width: 100%; justify-content: center; font-size: 13px; padding: 10px 20px; }
    }
@endsection

@section('content')
    <div class="panel-card">
        <div class="panel-title">
            <div class="panel-icon">
                <svg viewBox="0 0 24 24" width="22" height="22" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" style="color: var(--primary);">
                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                </svg>
            </div>
            <div class="panel-title-text">
                <h3>Update Your Password</h3>
                <p>Enter your current password and choose a new one.</p>
            </div>
        </div>

        <form id="changePasswordForm" onsubmit="handleChangePassword(event)">
            <div class="form-group">
                <label for="current_password">Current Password</label>
                <input type="password" id="current_password" name="current_password" placeholder="Enter current password" required autocomplete="current-password" />
            </div>
            <div class="form-group">
                <label for="new_password">New Password</label>
                <input type="password" id="new_password" name="new_password" placeholder="Enter new password (min 6 characters)" required minlength="6" autocomplete="new-password" />
            </div>
            <div class="form-group">
                <label for="new_password_confirmation">Confirm New Password</label>
                <input type="password" id="new_password_confirmation" name="new_password_confirmation" placeholder="Re-enter new password" required minlength="6" autocomplete="new-password" />
            </div>
            <button type="submit" class="btn-save" id="saveBtn">
                <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2.5" fill="none"><polyline points="20 6 9 17 4 12"></polyline></svg>
                Update Password
            </button>
        </form>
    </div>
@endsection

@section('scripts')
<script>
    async function handleChangePassword(e) {
        e.preventDefault();
        const btn = document.getElementById('saveBtn');
        const currentPassword = document.getElementById('current_password').value;
        const newPassword = document.getElementById('new_password').value;
        const confirmPassword = document.getElementById('new_password_confirmation').value;

        if (newPassword !== confirmPassword) {
            alert('New password and confirmation do not match.');
            return;
        }

        btn.disabled = true;
        btn.textContent = 'Updating...';

        try {
            const res = await fetch('/api/admin/change-password', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'Authorization': `Bearer ${adminToken}`
                },
                body: JSON.stringify({
                    current_password: currentPassword,
                    new_password: newPassword,
                    new_password_confirmation: confirmPassword
                })
            });
            const data = await res.json();

            if (data.success) {
                alert(data.message || 'Password updated successfully!');
                document.getElementById('changePasswordForm').reset();
            } else {
                alert(data.message || 'Failed to update password.');
            }
        } catch (err) {
            alert('Network error. Please try again.');
        } finally {
            btn.disabled = false;
            btn.innerHTML = '<svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2.5" fill="none"><polyline points="20 6 9 17 4 12"></polyline></svg> Update Password';
        }
    }
</script>
@endsection

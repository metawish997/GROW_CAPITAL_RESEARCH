<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>KYC Verification - Grow Capitals Research</title>
    <meta name="description" content="Complete your KYC verification to access all features of Grow Capitals Research platform." />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet" />
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        :root {
            --primary: #004b87; --primary-dark: #003d7c; --accent: #98d102;
            --amber: #f59e0b; --bg: #f8fafc; --surface: #ffffff;
            --card: #ffffff; --border: #e2e8f0;
            --text: #0f172a; --text-muted: #64748b; --red: #ef4444; --green: #10b981;
        }
        body { font-family: 'Inter', sans-serif; background: var(--bg); color: var(--text); min-height: 100vh; display: flex; flex-direction: column; }

        /* HEADER */
        .header { background: rgba(255,255,255,0.97); backdrop-filter: blur(16px); border-bottom: 1px solid var(--border); height: 68px; display: flex; align-items: center; justify-content: space-between; padding: 0 32px; position: sticky; top: 0; z-index: 100; }
        .header-logo { display: flex; align-items: center; gap: 10px; text-decoration: none; }
        .header-logo-icon { width: 38px; height: 38px; background: #ffffff; border: 1px solid var(--border); border-radius: 10px; display: flex; align-items: center; justify-content: center; padding: 2px; overflow: hidden; }
        .header-logo-text { font-size: 17px; font-weight: 700; background: linear-gradient(135deg, var(--primary), var(--accent)); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; }
        .nav-link { padding: 8px 14px; border-radius: 10px; font-size: 14px; color: var(--text-muted); text-decoration: none; transition: all 0.2s; font-weight: 500; }
        .nav-link:hover { color: var(--text); background: var(--card); }
        .nav-link.active { color: var(--primary); background: rgba(0,75,135,0.1); }
        .back-btn { display: flex; align-items: center; gap: 8px; padding: 8px 16px; background: var(--card); border: 1px solid var(--border); border-radius: 10px; color: var(--text-muted); text-decoration: none; font-size: 13px; font-weight: 500; transition: all 0.2s; }
        .back-btn:hover { color: var(--text); border-color: rgba(0,75,135,0.3); }

        /* MAIN */
        .main { flex: 1; padding: 40px 32px; max-width: 960px; margin: 0 auto; width: 100%; }

        /* PAGE HEADER */
        .page-title { font-size: 28px; font-weight: 800; margin-bottom: 6px; }
        .page-title span { background: linear-gradient(135deg, var(--primary), var(--accent)); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; }
        .page-sub { font-size: 14px; color: var(--text-muted); margin-bottom: 36px; }

        /* STATUS SECTION */
        #kycStatusSection { margin-bottom: 32px; }

        /* STATUS CARDS */
        .status-card { border-radius: 24px; padding: 36px; margin-bottom: 28px; text-align: center; }
        .status-card.not-started { background: linear-gradient(135deg, rgba(245,158,11,0.08), rgba(239,68,68,0.05)); border: 1px solid rgba(245,158,11,0.2); }
        .status-card.pending     { background: linear-gradient(135deg, rgba(0,75,135,0.08), rgba(152,209,2,0.04)); border: 1px solid rgba(0,75,135,0.2); }
        .status-card.approved    { background: linear-gradient(135deg, rgba(16,185,129,0.10), rgba(0,212,170,0.05)); border: 1px solid rgba(16,185,129,0.25); }
        .status-card.failed      { background: linear-gradient(135deg, rgba(239,68,68,0.08), rgba(245,158,11,0.05)); border: 1px solid rgba(239,68,68,0.2); }

        .status-big-icon { font-size: 60px; margin-bottom: 16px; }
        .status-card h2 { font-size: 22px; font-weight: 700; margin-bottom: 8px; }
        .status-card p  { font-size: 14px; color: var(--text-muted); max-width: 460px; margin: 0 auto 20px; line-height: 1.6; }

        /* KYC DETAILS GRID (on approved) */
        .kyc-detail-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; margin-top: 28px; text-align: left; }
        .kyc-detail-item { background: rgba(255,255,255,0.04); border: 1px solid var(--border); border-radius: 14px; padding: 16px; }
        .kyc-detail-item .label { font-size: 11px; color: var(--text-muted); font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 6px; }
        .kyc-detail-item .value { font-size: 14px; font-weight: 600; }

        /* STEPS */
        .steps-section { margin-bottom: 32px; }
        .steps-section h3 { font-size: 16px; font-weight: 700; margin-bottom: 20px; }
        .steps-row { display: grid; grid-template-columns: repeat(4, 1fr); gap: 14px; }
        .step-card { background: var(--card); border: 1px solid var(--border); border-radius: 18px; padding: 22px 18px; text-align: center; transition: all 0.2s; }
        .step-card:hover { border-color: rgba(0,75,135,0.3); transform: translateY(-2px); }
        .step-num { width: 32px; height: 32px; border-radius: 50%; background: rgba(0,75,135,0.15); border: 1px solid rgba(0,75,135,0.3); display: flex; align-items: center; justify-content: center; font-size: 13px; font-weight: 700; color: var(--primary); margin: 0 auto 12px; }
        .step-icon { font-size: 28px; margin-bottom: 10px; }
        .step-title { font-size: 13px; font-weight: 600; margin-bottom: 4px; }
        .step-desc  { font-size: 11px; color: var(--text-muted); line-height: 1.5; }

        /* INITIATE FORM */
        .initiate-card { background: var(--card); border: 1px solid var(--border); border-radius: 24px; padding: 36px; }
        .initiate-card h3 { font-size: 18px; font-weight: 700; margin-bottom: 6px; }
        .initiate-card p  { font-size: 13px; color: var(--text-muted); margin-bottom: 28px; }
        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 20px; }
        .form-group { display: flex; flex-direction: column; gap: 7px; }
        .form-group label { font-size: 12px; font-weight: 600; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.5px; }
        .form-group input { padding: 13px 16px; background: rgba(255,255,255,0.06); border: 1px solid var(--border); border-radius: 12px; color: var(--text); font-size: 14px; font-family: 'Inter', sans-serif; outline: none; transition: all 0.2s; }
        .form-group input:focus { border-color: var(--primary); background: rgba(0,75,135,0.08); box-shadow: 0 0 0 3px rgba(0,75,135,0.12); }
        .form-group input::placeholder { color: var(--text-muted); }
        .consent-box { background: rgba(0,75,135,0.06); border: 1px solid rgba(0,75,135,0.15); border-radius: 12px; padding: 14px 16px; font-size: 12px; color: var(--text-muted); line-height: 1.6; margin-bottom: 24px; display: flex; gap: 10px; }
        .submit-btn { width: 100%; padding: 15px; background: linear-gradient(135deg, var(--amber), #d97706); color: #000; font-size: 15px; font-weight: 700; font-family: 'Inter', sans-serif; border: none; border-radius: 14px; cursor: pointer; transition: all 0.25s; display: flex; align-items: center; justify-content: center; gap: 10px; }
        .submit-btn:hover:not(:disabled) { transform: translateY(-2px); box-shadow: 0 8px 24px rgba(245,158,11,0.4); }
        .submit-btn:disabled { opacity: 0.6; cursor: not-allowed; transform: none; }
        .submit-btn.loading { background: linear-gradient(135deg, rgba(0,75,135,0.5), rgba(0,61,124,0.5)); color: #fff; }

        /* TOAST */
        .toast { position: fixed; bottom: 28px; right: 28px; padding: 14px 20px; border-radius: 12px; font-size: 14px; font-weight: 500; z-index: 9999; display: none; align-items: center; gap: 10px; box-shadow: 0 8px 24px rgba(0,0,0,0.5); animation: toastIn 0.3s ease; }
        @keyframes toastIn { from { transform: translateY(20px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
        .toast-success { background: rgba(16,185,129,0.15); border: 1px solid rgba(16,185,129,0.3); color: #34d399; display: flex; }
        .toast-error   { background: rgba(239,68,68,0.15);  border: 1px solid rgba(239,68,68,0.3);  color: #f87171; display: flex; }
        .toast-info    { background: rgba(0,75,135,0.15); border: 1px solid rgba(0,75,135,0.3); color: #93c5fd; display: flex; }

        /* REDIRECT MODAL */
        .modal-overlay { position: fixed; inset: 0; background: rgba(0,0,0,0.8); backdrop-filter: blur(8px); z-index: 1000; display: none; align-items: center; justify-content: center; }
        .modal-overlay.open { display: flex; }
        .modal-card { background: var(--surface); border: 1px solid var(--border); border-radius: 24px; padding: 40px; max-width: 460px; width: 90%; text-align: center; }
        .modal-icon { font-size: 56px; margin-bottom: 16px; }
        .modal-card h3 { font-size: 20px; font-weight: 700; margin-bottom: 8px; }
        .modal-card p  { font-size: 14px; color: var(--text-muted); margin-bottom: 28px; line-height: 1.6; }
        .modal-url-box { background: rgba(255,255,255,0.04); border: 1px solid var(--border); border-radius: 12px; padding: 12px 16px; font-size: 12px; color: var(--text-muted); word-break: break-all; margin-bottom: 24px; text-align: left; }
        .modal-btn-row { display: flex; gap: 12px; }
        .modal-btn { flex: 1; padding: 13px; border-radius: 12px; font-size: 14px; font-weight: 600; font-family: 'Inter', sans-serif; cursor: pointer; border: none; transition: all 0.2s; }
        .modal-btn.primary { background: linear-gradient(135deg, var(--primary), var(--primary-dark)); color: #fff; }
        .modal-btn.primary:hover { transform: translateY(-1px); box-shadow: 0 6px 20px rgba(0,75,135,0.4); }
        .modal-btn.secondary { background: var(--card); border: 1px solid var(--border); color: var(--text-muted); }
        .modal-btn.secondary:hover { color: var(--text); }

        /* FOOTER */
        footer { background: var(--surface); border-top: 1px solid var(--border); padding: 24px 32px; margin-top: auto; }
        .footer-inner { display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 16px; }
        .footer-copy { font-size: 12px; color: var(--text-muted); }
    </style>
</head>
<body>

<!-- HEADER -->
<header class="header">
    <a href="/dashboard" class="header-logo" style="display: flex; align-items: center; height: 38px;">
        <img src="{{ asset('grologo.png') }}" alt="Grow Capital Research" style="height: 38px; width: auto; object-fit: contain;" />
    </a>
    <button id="kycLogoutBtn" class="back-btn" style="cursor: pointer; background: transparent; border: 1px solid var(--border); font-family: inherit;">⏻ Logout</button>
</header>

<!-- MAIN -->
<main class="main">

    <h1 class="page-title">🪪 KYC <span>Verification</span></h1>
    <p class="page-sub">Complete your identity verification to access all platform features.</p>

    <!-- Dynamic Status Section -->
    <div id="kycStatusSection">
        <!-- Loading skeleton -->
        <div id="kycLoading" style="background: var(--card); border: 1px solid var(--border); border-radius: 24px; padding: 40px; text-align: center; margin-bottom: 28px;">
            <div style="font-size: 48px; margin-bottom: 16px; opacity: 0.3;">⏳</div>
            <div style="height: 20px; background: rgba(255,255,255,0.06); border-radius: 8px; width: 40%; margin: 0 auto 10px;"></div>
            <div style="height: 14px; background: rgba(255,255,255,0.04); border-radius: 8px; width: 60%; margin: 0 auto;"></div>
        </div>
    </div>

    <!-- HOW IT WORKS STEPS -->
    <div class="steps-section" id="stepsSection" style="display:none;">
        <h3>📋 How KYC Works</h3>
        <div class="steps-row">
            <div class="step-card">
                <div class="step-num">1</div>
                <div class="step-icon">📝</div>
                <div class="step-title">Enter Details</div>
                <div class="step-desc">Provide your name and mobile number registered with Aadhaar</div>
            </div>
            <div class="step-card">
                <div class="step-num">2</div>
                <div class="step-icon">🔗</div>
                <div class="step-title">Digio Portal</div>
                <div class="step-desc">You'll be redirected to the secure Digio portal for verification</div>
            </div>
            <div class="step-card">
                <div class="step-num">3</div>
                <div class="step-icon">🪪</div>
                <div class="step-title">Aadhaar Verify</div>
                <div class="step-desc">Complete Aadhaar OTP + selfie + signature on the Digio portal</div>
            </div>
            <div class="step-card">
                <div class="step-num">4</div>
                <div class="step-icon">✅</div>
                <div class="step-title">KYC Approved</div>
                <div class="step-desc">You'll be redirected back with your KYC status updated</div>
            </div>
        </div>
    </div>

    <!-- INITIATE FORM (shown when not started / failed / expired) -->
    <div class="initiate-card" id="initiateCard" style="display:none;">
        <h3>Start KYC Verification</h3>
        <p>Enter your details to begin the KYC process via Digio — India's trusted eKYC platform.</p>

        <form id="kycForm" onsubmit="initiateKyc(event)">
            <div class="form-row">
                <div class="form-group">
                    <label>Full Name</label>
                    <input type="text" id="kycName" name="name" placeholder="As per Aadhaar card" required />
                </div>
                <div class="form-group">
                    <label>Mobile Number</label>
                    <input type="tel" id="kycMobile" name="mobile" placeholder="Aadhaar-linked mobile" maxlength="10" required />
                </div>
            </div>

            <div class="consent-box">
                ℹ️ By proceeding, you agree to complete identity verification via <strong>Digio</strong>. Your Aadhaar data will be used only for KYC compliance and stored securely.
            </div>

            <button type="submit" class="submit-btn" id="kycSubmitBtn">
                🚀 Proceed to KYC Verification
            </button>
        </form>
    </div>

</main>

<!-- FOOTER -->
<footer>
    <div class="footer-inner">
        <span style="font-size:13px; color: var(--text-muted); font-weight: 600;">🔒 Powered by Digio — Secure eKYC</span>
        <div class="footer-copy">© 2026 Grow Capitals Research. All rights reserved.</div>
    </div>
</footer>

<!-- REDIRECT MODAL -->
<div class="modal-overlay" id="redirectModal">
    <div class="modal-card">
        <div class="modal-icon">🔐</div>
        <h3>Redirecting to Digio</h3>
        <p>You'll be taken to the secure Digio KYC portal. Complete the verification there and you'll be brought back automatically.</p>
        <div class="modal-url-box" id="modalUrl">Generating secure link...</div>
        <div class="modal-btn-row">
            <button class="modal-btn secondary" onclick="closeModal()">Cancel</button>
            <button class="modal-btn primary" id="openDigioBtn" onclick="openDigio()">Open Digio Portal →</button>
        </div>
    </div>
</div>

<!-- TOAST -->
<div class="toast" id="toast"></div>

<script>
    const userToken = localStorage.getItem('user_token');
    if (!userToken) window.location.href = '/login';

    const userInfo = JSON.parse(localStorage.getItem('user_info') || '{}');
    if (userInfo.name)   document.getElementById('kycName').value   = userInfo.name;
    if (userInfo.mobile) document.getElementById('kycMobile').value = userInfo.mobile;

    let digioRedirectUrl = null;

    // ─── LOAD KYC STATUS ───────────────────────────────────────────
    async function loadKycStatus() {
        try {
            const res  = await fetch('/api/user/kyc/status', {
                headers: { 'Authorization': 'Bearer ' + userToken, 'Accept': 'application/json' }
            });
            const data = await res.json();
            renderStatus(data.kyc_status, data.kyc);
        } catch (e) {
            renderStatus('error', null);
        }
    }

    function renderStatus(status, kyc) {
        const section = document.getElementById('kycStatusSection');
        const stepsEl = document.getElementById('stepsSection');
        const formEl  = document.getElementById('initiateCard');

        const showable = ['not_started', 'error', 'rejected', 'failed', 'expired'];
        const pending  = ['initiated', 'pending', 'approval_pending'];

        if (status === 'approved') {
            window.location.href = '/dashboard';
            return;
        } else if (pending.includes(status)) {
            // ── PENDING
            const labels = { initiated: ['⏳ KYC In Progress', 'Your KYC request has been created. Please open the Digio link to complete verification.'], pending: ['⏳ KYC Under Review', 'Your KYC is submitted and under review. This usually takes a few minutes.'], approval_pending: ['⏳ Awaiting Approval', 'Your documents have been verified. Final approval is in progress.'] };
            const [title, desc] = labels[status] || labels['pending'];

            section.innerHTML = `
                <div class="status-card pending">
                    <div class="status-big-icon">⏳</div>
                    <h2>${title}</h2>
                    <p>${desc}</p>
                    <div style="display:flex; justify-content:center; gap:12px; margin-top:20px;">
                        <button onclick="syncKycStatus()" style="padding: 11px 24px; background: rgba(0,75,135,0.15); border: 1px solid rgba(0,75,135,0.3); border-radius: 12px; color: #93c5fd; font-size: 13px; font-weight: 600; cursor: pointer; font-family: 'Inter', sans-serif;" id="syncBtn">
                            ↻ Refresh Status
                        </button>
                        <button onclick="restartKyc()" style="padding: 11px 24px; background: rgba(245,158,11,0.15); border: 1px solid rgba(245,158,11,0.3); border-radius: 12px; color: #fbbf24; font-size: 13px; font-weight: 600; cursor: pointer; font-family: 'Inter', sans-serif;" title="Start a fresh KYC application if you are stuck">
                            🔄 Restart KYC
                        </button>
                    </div>
                </div>`;
            stepsEl.style.display = 'none';
            formEl.style.display  = 'none';

        } else {
            // ── NOT STARTED / FAILED / REJECTED / EXPIRED / ERROR
            const errLabels = { rejected: ['❌ KYC Rejected', 'Your KYC was rejected. Please fill the form below and retry.'], failed: ['❌ KYC Failed', 'An error occurred during verification. Please retry.'], expired: ['⏰ KYC Session Expired', 'Your previous KYC session expired. Please start fresh.'], error: ['⚠️ Could not load status', 'Please refresh the page or try again.'] };
            const [title, desc] = errLabels[status] || ['🪪 KYC Not Started', 'You have not completed KYC verification yet. Start below.'];

            if (status !== 'not_started') {
                section.innerHTML = `
                    <div class="status-card failed">
                        <div class="status-big-icon">${status === 'expired' ? '⏰' : '❌'}</div>
                        <h2>${title}</h2>
                        <p>${desc}</p>
                    </div>`;
            } else {
                section.innerHTML = '';
            }

            stepsEl.style.display = 'block';
            formEl.style.display  = 'block';
        }

        // Remove loading skeleton
        const loader = document.getElementById('kycLoading');
        if (loader) loader.remove();
    }

    // ─── SYNC STATUS ───────────────────────────────────────────────
    async function syncKycStatus() {
        const btn = document.getElementById('syncBtn');
        if (btn) { btn.textContent = '⏳ Syncing...'; btn.disabled = true; }

        try {
            const res  = await fetch('/api/user/kyc/sync', {
                method: 'POST',
                headers: { 'Authorization': 'Bearer ' + userToken, 'Accept': 'application/json', 'Content-Type': 'application/json' }
            });
            const data = await res.json();
            showToast(data.is_approved ? '✅ KYC Approved!' : '↻ Status updated: ' + (data.kyc_status || 'pending'), data.is_approved ? 'success' : 'info');
            setTimeout(() => loadKycStatus(), 800);
        } catch (e) {
            showToast('Could not sync. Please try again.', 'error');
            if (btn) { btn.textContent = '↻ Refresh Status'; btn.disabled = false; }
        }
    }

    // ─── RESTART KYC ───────────────────────────────────────────────
    function restartKyc() {
        if(confirm("Are you sure you want to cancel your current KYC request and start fresh?")) {
            // Force the form to show up and hide the pending status banner
            document.getElementById('kycStatusSection').innerHTML = '';
            document.getElementById('stepsSection').style.display = 'block';
            document.getElementById('initiateCard').style.display = 'block';
            showToast('Ready to restart! Please fill the form below.', 'info');
        }
    }

    // ─── INITIATE KYC ──────────────────────────────────────────────
    async function initiateKyc(e) {
        e.preventDefault();

        const mobile = document.getElementById('kycMobile').value.trim();
        const name   = document.getElementById('kycName').value.trim();
        const btn    = document.getElementById('kycSubmitBtn');

        if (!mobile || mobile.length < 10) {
            showToast('Please enter a valid 10-digit mobile number.', 'error'); return;
        }
        if (!name) {
            showToast('Please enter your full name.', 'error'); return;
        }

        btn.disabled = true;
        btn.classList.add('loading');
        btn.innerHTML = '⏳ Creating KYC Request...';

        try {
            const res  = await fetch('/api/user/kyc/initiate', {
                method: 'POST',
                headers: { 'Authorization': 'Bearer ' + userToken, 'Accept': 'application/json', 'Content-Type': 'application/json' },
                body: JSON.stringify({ mobile, name }),
            });
            const data = await res.json();

            if (data.success) {
                window.location.href = data.redirect_url;
            } else {
                showToast(data.message || 'Failed to initiate KYC.', 'error');
                btn.disabled = false;
                btn.classList.remove('loading');
                btn.innerHTML = '🚀 Proceed to KYC Verification';
            }
        } catch (err) {
            showToast('Network error. Please try again.', 'error');
            btn.disabled = false;
            btn.classList.remove('loading');
            btn.innerHTML = '🚀 Proceed to KYC Verification';
        }
    }

    function openDigio() {
        if (digioRedirectUrl) {
            window.location.href = digioRedirectUrl;
        }
    }

    function closeModal() {
        document.getElementById('redirectModal').classList.remove('open');
        const btn = document.getElementById('kycSubmitBtn');
        btn.disabled = false;
        btn.classList.remove('loading');
        btn.innerHTML = '🚀 Proceed to KYC Verification';
    }

    // ─── TOAST ─────────────────────────────────────────────────────
    function showToast(msg, type = 'success') {
        const el = document.getElementById('toast');
        el.className = `toast toast-${type}`;
        el.innerHTML = `<span>${type === 'success' ? '✅' : type === 'info' ? 'ℹ️' : '❌'}</span> ${msg}`;
        el.style.display = 'flex';
        setTimeout(() => el.style.display = 'none', 5000);
    }

    // Check URL params for callback (from Digio redirect)
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('kyc') === 'success') showToast('✅ KYC verified successfully!', 'success');
    if (urlParams.get('kyc') === 'pending')  showToast('⏳ KYC submitted and under review.', 'info');
    if (urlParams.get('kyc') === 'failed')   showToast('❌ KYC verification failed. Please retry.', 'error');

    document.getElementById('kycLogoutBtn').addEventListener('click', async () => {
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

    // INIT
    loadKycStatus();
</script>
</body>
</html>

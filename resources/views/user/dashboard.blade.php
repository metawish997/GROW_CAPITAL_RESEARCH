<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Onboarding Portal - Grow Capitals</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet" />
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        
        :root {
            --primary: #004b87;
            --primary-dark: #003764;
            --accent: #98d102;
            --text-dark: #0b2d49;
            --bg: #fdfdfd;
            --card: #ffffff;
            --border: #e2e8f0;
            --text-muted: #64748b;
            --green: #10b981;
            --red: #ef4444;
            --amber-bg: #fffbeb;
            --amber-border: #fef3c7;
            --amber-text: #b45309;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: var(--bg);
            color: var(--text-dark);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* ─── HEADER ─────────────────────────────────────────────────── */
        .header {
            background: rgba(255,255,255,0.97);
            backdrop-filter: blur(16px);
            border-bottom: 1px solid var(--border);
            height: 72px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 40px;
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .header-logo {
            display: flex;
            align-items: center;
            height: 38px;
            text-decoration: none;
        }

        .header-logo img {
            height: 38px;
            width: auto;
            object-fit: contain;
        }

        .logout-btn {
            background: transparent;
            color: var(--text-dark);
            border: 1px solid var(--border);
            padding: 10px 24px;
            border-radius: 50px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .logout-btn:hover {
            border-color: var(--primary);
            color: var(--primary);
            background: rgba(0,75,135,0.03);
        }

        /* ─── CENTERING CONTAINER ─────────────────────────────────────── */
        .container {
            max-width: 1380px;
            width: 100%;
            margin: 0 auto;
            padding: 0 40px;
        }

        /* ─── MAIN LAYOUT ────────────────────────────────────────────── */
        .main {
            padding: 40px 0 60px 0;
            width: 100%;
        }

        /* ─── PROGRESS STEPPER ────────────────────────────────────────── */
        .stepper {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 24px;
            margin-bottom: 40px;
            background: #ffffff;
            border: 1px solid var(--border);
            padding: 20px 32px;
            border-radius: 20px;
            width: 100%;
        }

        .stepper-steps {
            display: flex;
            align-items: center;
            gap: 24px;
        }

        .step {
            display: flex;
            align-items: center;
            gap: 12px;
            opacity: 0.4;
            transition: all 0.3s;
            font-weight: 500;
        }

        .step.active {
            opacity: 1;
            font-weight: 700;
            color: var(--primary);
        }

        .step.completed {
            opacity: 1;
            color: var(--text-dark);
            font-weight: 700;
        }

        .step-num {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            background: #f1f5f9;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: 700;
            border: 2px solid var(--border);
            color: var(--text-muted);
        }

        .step.active .step-num {
            background: var(--primary);
            border-color: var(--primary);
            color: #ffffff;
        }

        .step.completed .step-num {
            background: var(--accent);
            border-color: var(--accent);
            color: var(--text-dark);
        }

        .step-label {
            font-size: 14px;
        }

        .step-line {
            width: 80px;
            height: 2px;
            background: var(--border);
        }

        /* ─── DUAL-COLUMN GRID ────────────────────────────────────────── */
        .portal-grid {
            display: grid;
            grid-template-columns: 1.35fr 1fr;
            gap: 32px;
            width: 100%;
        }

        /* LEFT COLUMN (Forms and PDF Viewers) */
        .left-col {
            display: flex;
            flex-direction: column;
            gap: 28px;
        }

        /* RIGHT COLUMN (Static sidebar checklists and help guides) */
        .right-col {
            display: flex;
            flex-direction: column;
            gap: 24px;
        }

        /* PREMIUM CONTENT CARDS */
        .section-card {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 24px;
            padding: 36px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.02);
            position: relative;
        }

        /* Full Width Edge-to-Edge PDF Layout inside Card */
        .section-card.pdf-container {
            padding: 0;
            border-radius: 0;
            box-shadow: none;
            border: none;
            background: transparent;
        }
        .section-card.pdf-container .pdf-header {
            padding: 0 0 16px 0;
            margin-bottom: 0;
            border-bottom: none;
        }
        .section-card.pdf-container iframe {
            border: 1px solid var(--border);
            border-radius: 16px;
            margin-bottom: 24px;
        }
        .section-card.pdf-container .pdf-footer {
            padding: 0;
            text-align: right;
        }

        .card-header-block {
            margin-bottom: 24px;
            border-bottom: 1px solid var(--border);
            padding-bottom: 16px;
        }

        .card-header-block h2 {
            font-size: 20px;
            font-weight: 700;
            color: var(--text-dark);
            letter-spacing: -0.5px;
        }

        .card-header-block p {
            font-size: 13px;
            color: var(--text-muted);
            margin-top: 6px;
            line-height: 1.5;
        }

        /* FORM ELEMENTS */
        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            font-size: 12px;
            font-weight: 700;
            color: var(--text-dark);
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        input {
            width: 100%;
            padding: 15px 18px;
            background: #ffffff;
            border: 1px solid var(--border);
            border-radius: 12px;
            color: var(--text-dark);
            font-size: 15px;
            font-family: inherit;
            outline: none;
            transition: all 0.2s ease;
        }

        input:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(0,75,135,0.12);
        }

        input::placeholder {
            color: #94a3b8;
        }

        /* BUTTONS */
        .kyc-btn {
            padding: 14px 28px;
            border-radius: 50px;
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
            border: none;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            text-decoration: none;
        }

        .kyc-btn.purple {
            background: var(--primary);
            color: #ffffff;
        }

        .kyc-btn.purple:hover {
            background: var(--primary-dark);
            transform: translateY(-1px);
            box-shadow: 0 6px 15px rgba(0,75,135,0.25);
        }

        .kyc-btn.outline {
            background: transparent;
            border: 1px solid var(--border);
            color: var(--text-dark);
        }

        .kyc-btn.outline:hover {
            background: rgba(0,75,135,0.03);
            border-color: var(--primary);
            color: var(--primary);
        }

        .kyc-btn.amber {
            background: var(--accent);
            color: var(--text-dark);
        }

        .kyc-btn.amber:hover {
            box-shadow: 0 6px 15px rgba(152,209,2,0.25);
            transform: translateY(-1px);
        }

        .kyc-btn:disabled {
            opacity: 0.7;
            cursor: not-allowed;
            transform: none !important;
            box-shadow: none !important;
        }

        /* STATUS DISPLAY BANNERS */
        .kyc-banner {
            border-radius: 20px;
            padding: 24px 28px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
        }

        .kyc-banner.approved {
            background: linear-gradient(135deg, rgba(16,185,129,0.08), rgba(0,212,170,0.04));
            border: 1px solid rgba(16,185,129,0.2);
        }

        .kyc-banner.failed {
            background: linear-gradient(135deg, rgba(239,68,68,0.08), rgba(245,158,11,0.04));
            border: 1px solid rgba(239,68,68,0.2);
        }

        .kyc-left {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .kyc-icon {
            width: 46px;
            height: 46px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            flex-shrink: 0;
        }

        .kyc-icon.green { background: rgba(16,185,129,0.12); }
        .kyc-icon.red { background: rgba(239,68,68,0.12); }

        .kyc-text h3 {
            font-size: 15px;
            font-weight: 700;
            color: var(--text-dark);
        }

        .kyc-text p {
            font-size: 13px;
            color: var(--text-muted);
            margin-top: 3px;
        }

        /* ─── SIDEBAR BRAND CHECKLISTS & HELP ────────────────────────── */
        .sidebar-card {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 24px;
            padding: 32px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.02);
        }

        .sidebar-card.brand-green {
            background: var(--accent);
            color: #002f55;
            border: none;
            box-shadow: 0 10px 25px -5px rgba(152,209,2,0.25);
        }
        .sidebar-card.brand-green .sidebar-badge {
            background: rgba(0,47,85,0.06);
            border-color: rgba(0,47,85,0.12);
            color: #002f55;
        }
        .sidebar-card.brand-green h3 {
            color: #002f55;
        }
        .sidebar-card.brand-green .checklist-item {
            color: rgba(0,47,85,0.85);
        }
        .sidebar-card.brand-green .checklist-bullet {
            color: var(--primary);
        }

        .sidebar-badge {
            background: rgba(11,45,73,0.06);
            border: 1px solid rgba(11,45,73,0.12);
            color: var(--text-dark);
            padding: 6px 14px;
            border-radius: 50px;
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            width: fit-content;
            margin-bottom: 20px;
        }

        .sidebar-card h3 {
            font-size: 18px;
            font-weight: 700;
            margin-bottom: 16px;
        }

        .checklist-item {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            margin-bottom: 16px;
            font-size: 13px;
            line-height: 1.5;
            color: #475569;
        }

        .checklist-bullet {
            color: var(--primary);
            font-weight: 700;
            font-size: 15px;
            line-height: 1;
        }

        /* FAQ Card inside Sidebar */
        .faq-item {
            margin-bottom: 16px;
        }

        .faq-question {
            font-size: 13px;
            font-weight: 700;
            color: var(--text-dark);
            margin-bottom: 4px;
        }

        .faq-answer {
            font-size: 12px;
            color: var(--text-muted);
            line-height: 1.5;
        }

        /* ─── FOOTER ─────────────────────────────────────────────────── */
        footer {
            background: #ffffff;
            border-top: 1px solid var(--border);
            padding: 28px 0;
            margin-top: auto;
            width: 100%;
        }

        .footer-inner {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 16px;
        }

        .footer-logo img {
            height: 28px;
            width: auto;
            object-fit: contain;
        }

        .footer-links {
            display: flex;
            gap: 24px;
        }

        .footer-links a {
            font-size: 12px;
            color: var(--text-muted);
            text-decoration: none;
            transition: color 0.2s;
            font-weight: 500;
        }

        .footer-links a:hover {
            color: var(--primary);
        }

        .footer-copy {
            font-size: 12px;
            color: var(--text-muted);
            font-weight: 500;
        }

        /* TOAST */
        .toast {
            position: fixed;
            top: 24px;
            right: 24px;
            padding: 14px 20px;
            border-radius: 12px;
            font-size: 13px;
            font-weight: 600;
            z-index: 9999;
            display: none;
            align-items: center;
            gap: 10px;
            box-shadow: 0 8px 24px rgba(0,0,0,0.08);
            animation: slideIn 0.3s ease;
        }

        .toast.success { display: flex; background: rgba(16,185,129,0.15); border: 1px solid rgba(16,185,129,0.3); color: #10b981; }
        .toast.error   { display: flex; background: rgba(239,68,68,0.15); border: 1px solid rgba(239,68,68,0.3); color: #ef4444; }
        .toast.info    { display: flex; background: rgba(0,75,135,0.15); border: 1px solid rgba(0,75,135,0.3); color: #004b87; }
        
        @keyframes slideIn { from { transform: translateX(30px); opacity: 0; } to { transform: translateX(0); opacity: 1; } }

        .login-loader {
            width: 16px;
            height: 16px;
            border: 2px solid rgba(255,255,255,0.3);
            border-top-color: #ffffff;
            border-radius: 50%;
            animation: spin 0.7s linear infinite;
        }
        @keyframes spin { to { transform: rotate(360deg); } }

        /* SKELETON */
        .skeleton {
            background: linear-gradient(90deg, rgba(0,0,0,0.04) 25%, rgba(0,0,0,0.08) 50%, rgba(0,0,0,0.04) 75%);
            background-size: 200% 100%;
            animation: shimmer 1.5s infinite;
            border-radius: 8px;
            height: 20px;
        }
        @keyframes shimmer { 0% { background-position: 200% 0; } 100% { background-position: -200% 0; } }

        /* RESPONSIVE LAYOUT */
        @media (max-width: 992px) {
            .header { padding: 0 20px; }
            .container { padding: 0 20px; }
            .stepper { width: 100%; justify-content: space-between; padding: 14px 16px; margin-bottom: 24px; border-radius: 16px; gap: 8px; flex-direction: column; align-items: flex-start; }
            .stepper-steps { width: 100%; justify-content: space-between; gap: 8px; }
            #stepperRightAction { width: 100%; margin-top: 8px; }
            #stepperRightAction button { width: 100%; }
            .step { gap: 6px; }
            .step-num { width: 24px; height: 24px; font-size: 10px; border-width: 1px; }
            .step-label { font-size: 11px; }
            .step-line { flex: 1; width: auto; max-width: none; }
            .portal-grid { grid-template-columns: 1fr; gap: 24px; }
            .right-col { display: none; } /* Hide the helper sidebar on mobile viewport */
            .section-card { padding: 24px 20px; border-radius: 20px; }
            .card-header-block { margin-bottom: 16px; padding-bottom: 12px; }
            .card-header-block h2 { font-size: 18px; }
            .card-header-block p { font-size: 12px; }
            .form-group { margin-bottom: 16px; }
            .form-group label { font-size: 10px; margin-bottom: 6px; }
            .form-group input { padding: 13px 15px; font-size: 14px; border-radius: 10px; }
            .kyc-btn { padding: 13px; font-size: 14px; }
            #onboardingContent label[for="kycDeclaration"] { font-size: 10px !important; line-height: 1.4 !important; }
            .section-card.pdf-container { padding: 0 !important; }
            .section-card.pdf-container .pdf-header { padding: 0 0 12px 0 !important; }
            .section-card.pdf-container .pdf-header h2 { font-size: 16px !important; }
            .section-card.pdf-container .pdf-header p { font-size: 11px !important; }
            .section-card.pdf-container iframe { height: 45vh !important; margin-bottom: 18px !important; border-radius: 12px !important; }
            .section-card.pdf-container .pdf-footer { padding: 0 !important; text-align: center !important; }
            .section-card.pdf-container .pdf-footer button { width: 100% !important; padding: 13px !important; }
            .kyc-banner { flex-direction: column; align-items: center; text-align: center; padding: 24px 20px; gap: 16px; }
            .kyc-left { flex-direction: column; align-items: center; text-align: center; gap: 12px; }
            .kyc-banner button { width: 100% !important; }
            /* Accordion & Splash screen overrides on mobile */
            .accordion-item div[style*="grid-template-columns"] { grid-template-columns: 1fr !important; gap: 12px !important; }
            .accordion-item iframe { height: 40vh !important; }
            .accordion-item button { width: 100% !important; margin-top: 8px !important; }
            .section-card[style*="max-width: 720px"] { padding: 32px 20px !important; }
            .section-card[style*="max-width: 720px"] h2 { font-size: 20px !important; }
            .section-card[style*="max-width: 720px"] p { font-size: 12px !important; }
            .section-card[style*="max-width: 720px"] div[style*="font-size: 56px"] { font-size: 40px !important; margin-bottom: 16px !important; }
        }
    </style>
</head>
<body>

<!-- HEADER -->
<header class="header">
    <a href="/dashboard" class="header-logo">
        <img src="{{ asset('grologo.png') }}" alt="Grow Capital Research" />
    </a>
    <div class="header-right">
        <button id="logoutBtn" class="logout-btn">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="feather feather-power"><path d="M18.36 6.64a9 9 0 1 1-12.73 0"></path><line x1="12" y1="2" x2="12" y2="12"></line></svg>
            Logout
        </button>
    </div>
</header>

<!-- CENTERING LAYOUT -->
<div class="container">
    <main class="main">

        <!-- Stepper Progress Tracker -->
        <div class="stepper">
            <div class="stepper-steps">
                <div class="step" id="step1">
                    <div class="step-num">1</div>
                    <span class="step-label">KYC Verification</span>
                </div>
                <div class="step-line" id="stepLine"></div>
                <div class="step" id="step2">
                    <div class="step-num">2</div>
                    <span class="step-label">Service Agreement</span>
                </div>
            </div>
            <div id="stepperRightAction"></div>
        </div>

        <!-- DUAL COLUMN CONTENT GRID -->
        <div class="portal-grid">
            
            <!-- LEFT COLUMN (Dynamic Content Panel) -->
            <div class="left-col" id="onboardingContent">
                <!-- Loading Skeleton -->
                <div class="section-card" style="display:flex; flex-direction:column; align-items:center; justify-content:center; padding:48px; gap:16px; min-height: 280px;">
                    <div class="skeleton" style="width:52px; height:52px; border-radius:50%; flex-shrink:0;"></div>
                    <div class="skeleton" style="width: 40%; height: 16px;"></div>
                    <div class="skeleton" style="width: 65%; height: 12px;"></div>
                </div>
            </div>

            <!-- RIGHT COLUMN (Sidebar Help Panel) -->
            <div class="right-col" id="sidebarContainer">
                <!-- Checklist Card -->
                <div class="sidebar-card brand-green" id="sidebarChecklistCard">
                    <div class="sidebar-badge">Verification Progress</div>
                    <h3>KYC Checklist</h3>
                    <div class="checklist-item">
                        <span class="checklist-bullet">✓</span>
                        <span>Provide Aadhaar number and PAN details correctly.</span>
                    </div>
                    <div class="checklist-item">
                        <span class="checklist-bullet">✓</span>
                        <span>Keep your Aadhaar registered mobile active to receive OTP.</span>
                    </div>
                    <div class="checklist-item">
                        <span class="checklist-bullet">✓</span>
                        <span>Verify details and e-sign the official Service Agreement.</span>
                    </div>
                </div>

                <!-- FAQ Card -->
                <div class="sidebar-card" id="sidebarFaqCard">
                    <h3>Frequently Asked Questions</h3>
                    
                    <div class="faq-item">
                        <div class="faq-question">How long does verification take?</div>
                        <div class="faq-answer">Identity checks and e-signature routing take about 2-3 minutes. Access is granted instantly on successful completion.</div>
                    </div>

                    <div class="faq-item">
                        <div class="faq-question">Is my information safe?</div>
                        <div class="faq-answer">Absolutely. All data verification is powered by SEBI-approved Digio gateway services, utilizing end-to-end encryption.</div>
                    </div>
                </div>
            </div>

        </div>

    </main>
</div>

<!-- FOOTER -->
<footer>
    <div class="container">
        <div class="footer-inner">
            <a href="/dashboard" class="footer-logo">
                <img src="{{ asset('grologo.png') }}" alt="Grow Capital Research" />
            </a>
            <div class="footer-links">
                <a href="#">About</a>
                <a href="#">Privacy Policy</a>
                <a href="#">Terms of Use</a>
                <a href="#">Support</a>
            </div>
            <div class="footer-copy">© 2026 Grow Capitals Research. All rights reserved.</div>
        </div>
    </div>
</footer>

<!-- TOAST -->
<div class="toast" id="toast"></div>

<script>
    const userToken = localStorage.getItem('user_token');
    if (!userToken) window.location.href = '/login';

    const userInfo = JSON.parse(localStorage.getItem('user_info') || '{}');
    const urlParams = new URLSearchParams(window.location.search);

    let logoutTimer = null;
    function startAutoLogout(seconds = 15) {
        let count = seconds;
        const el = document.getElementById('logoutCountdown');
        if (logoutTimer) clearInterval(logoutTimer);
        
        logoutTimer = setInterval(async () => {
            count--;
            if (el) el.innerText = count;
            if (count <= 0) {
                clearInterval(logoutTimer);
                try {
                    await fetch('/api/user/logout', {
                        method: 'POST',
                        headers: { 'Authorization': 'Bearer ' + userToken, 'Accept': 'application/json' }
                    });
                } catch (e) {}
                localStorage.removeItem('user_token');
                localStorage.removeItem('user_info');
                window.location.href = '/login?logout=timeout';
            }
        }, 1000);
    }

    function toggleAccordion(id) {
        const el = document.getElementById(id);
        const arrow = document.getElementById(id + '-arrow');
        if (el) {
            const isHidden = el.style.display === 'none';
            el.style.display = isHidden ? 'block' : 'none';
            if (arrow) {
                arrow.style.transform = isHidden ? 'rotate(180deg)' : 'rotate(0deg)';
            }
            if (id === 'agreement-acc' && isHidden) {
                loadSignedPdfInline();
            }
        }
    }

    function validateKycForm() {
        const name = document.getElementById('kycName')?.value.trim();
        const mobile = document.getElementById('kycMobile')?.value.trim();
        const checkbox = document.getElementById('kycDeclaration')?.checked;
        const btn = document.getElementById('kycSubmitBtn');
        if (btn) {
            if (name && mobile && mobile.length === 10 && checkbox) {
                btn.disabled = false;
            } else {
                btn.disabled = true;
            }
        }
    }

    function updateSidebar(step) {
        const checklistCard = document.getElementById('sidebarChecklistCard');
        const faqCard = document.getElementById('sidebarFaqCard');
        if (!checklistCard || !faqCard) return;

        if (step === 'kyc') {
            checklistCard.className = "sidebar-card brand-green";
            checklistCard.innerHTML = `
                <div class="sidebar-badge">Verification Progress</div>
                <h3>KYC Checklist</h3>
                <div class="checklist-item">
                    <span class="checklist-bullet">✓</span>
                    <span>Provide Aadhaar number and PAN details correctly.</span>
                </div>
                <div class="checklist-item">
                    <span class="checklist-bullet">✓</span>
                    <span>Keep your Aadhaar registered mobile active to receive OTP.</span>
                </div>
                <div class="checklist-item">
                    <span class="checklist-bullet">✓</span>
                    <span>Verify details and e-sign the official Service Agreement.</span>
                </div>
            `;
            faqCard.innerHTML = `
                <h3>Frequently Asked Questions</h3>
                <div class="faq-item">
                    <div class="faq-question">How long does verification take?</div>
                    <div class="faq-answer">Identity checks and e-signature routing take about 2-3 minutes. Access is granted instantly on successful completion.</div>
                </div>
                <div class="faq-item">
                    <div class="faq-question">Is my information safe?</div>
                    <div class="faq-answer">Absolutely. All data verification is powered by SEBI-approved Digio gateway services, utilizing end-to-end encryption.</div>
                </div>
            `;
        } else if (step === 'agreement') {
            checklistCard.className = "sidebar-card brand-green";
            checklistCard.innerHTML = `
                <div class="sidebar-badge">Verification Progress</div>
                <h3>Agreement Checklist</h3>
                <div class="checklist-item">
                    <span class="checklist-bullet">✓</span>
                    <span>Review the service agreement document on the left.</span>
                </div>
                <div class="checklist-item">
                    <span class="checklist-bullet">✓</span>
                    <span>Click the Proceed to E-Sign button below to launch signature gate.</span>
                </div>
                <div class="checklist-item">
                    <span class="checklist-bullet">✓</span>
                    <span>Input Aadhaar number and verify identity using NSDL OTP.</span>
                </div>
            `;
            faqCard.innerHTML = `
                <h3>Frequently Asked Questions</h3>
                <div class="faq-item">
                    <div class="faq-question">Is Aadhaar e-Sign legally binding?</div>
                    <div class="faq-answer">Yes, Aadhaar-based electronic signatures carry the same legal weight as traditional physical signatures under the Indian IT Act, 2000.</div>
                </div>
                <div class="faq-item">
                    <div class="faq-question">Can I download a copy?</div>
                    <div class="faq-answer">Yes. As soon as signature routing completes, you can view and download the fully executed PDF right here.</div>
                </div>
            `;
        }
    }

    // LOGOUT BUTTON
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

    // TOAST NOTIFICATIONS
    function showToast(message, type = 'info') {
        const toast = document.getElementById('toast');
        toast.className = `toast ${type}`;
        toast.innerText = message;
        toast.style.display = 'flex';
        setTimeout(() => { toast.style.display = 'none'; }, 4000);
    }

    // ─── STEPPER MANAGEMENT ──────────────────────────────────────────
    function setStepState(step1State, step2State) {
        const s1 = document.getElementById('step1');
        const s2 = document.getElementById('step2');
        const line = document.getElementById('stepLine');

        s1.className = `step ${step1State}`;
        s2.className = `step ${step2State}`;
        
        if (step1State === 'completed') {
            line.style.background = 'var(--accent)';
        } else {
            line.style.background = 'var(--border)';
        }
    }

    // ─── KYC STATUS CHECK ───────────────────────────────────────────
    async function loadKycStatus() {
        try {
            updateSidebar('kyc');
            if (document.getElementById('stepperRightAction')) {
                document.getElementById('stepperRightAction').innerHTML = '';
            }
            const res  = await fetch('/api/user/kyc/status', {
                headers: { 'Authorization': 'Bearer ' + userToken, 'Accept': 'application/json' }
            });
            const data = await res.json();
            const status = data.kyc_status; // not_started | initiated | pending | approval_pending | approved | rejected | failed | expired
            
            const content = document.getElementById('onboardingContent');

            const showForm = ['not_started', 'rejected', 'failed', 'expired'];
            const pendingReview = ['initiated', 'pending', 'approval_pending'];

            if (showForm.includes(status)) {
                setStepState('active', 'pending');
                content.innerHTML = `
                <div class="section-card">
                    <div class="card-header-block">
                        <h2>Submit KYC Details</h2>
                        <p>Fill in your details as they appear on your Aadhaar card to initiate secure digital identification.</p>
                        <div style="font-size: 11px; color: var(--text-muted); margin-top: 10px;"><span style="color: var(--red);">*</span> Indicates required fields</div>
                    </div>
                    <form onsubmit="initiateKyc(event)">
                        <div class="form-group">
                            <label>Full Name (as on Aadhaar) <span style="color: var(--red);">*</span></label>
                            <input type="text" id="kycName" placeholder="Enter your full name" required oninput="validateKycForm()" />
                        </div>
                        <div class="form-group">
                            <label>Mobile Number (Aadhaar Linked) <span style="color: var(--red);">*</span></label>
                            <input type="tel" id="kycMobile" placeholder="Enter 10-digit mobile number" required maxlength="10" pattern="[0-9]{10}" oninput="this.value = this.value.replace(/[^0-9]/g, ''); validateKycForm();" />
                        </div>
                        <div class="form-group" style="display:flex; gap:12px; align-items:flex-start; margin-top:20px; margin-bottom:24px;">
                            <input type="checkbox" id="kycDeclaration" required style="width:20px; height:20px; margin-top:2px; flex-shrink:0; cursor:pointer;" onchange="validateKycForm()" />
                            <label for="kycDeclaration" style="font-size:12px; font-weight:500; text-transform:none; letter-spacing:0; color:#475569; cursor:pointer; line-height:1.5; margin-bottom:0;">
                                ${data.declaration_text || 'I hereby authorize Grow Capital Research to retrieve my profile and verify my identity details via Digio secure KYC gateway.'} <span style="color: var(--red);">*</span>
                            </label>
                        </div>
                        <button type="submit" id="kycSubmitBtn" class="kyc-btn purple" style="width:100%; margin-top:8px;" disabled>
                            Proceed to KYC Verification &nbsp;➔
                        </button>
                    </form>
                </div>`;
                
                if (userInfo.mobile) document.getElementById('kycMobile').value = userInfo.mobile;
                validateKycForm();

            } else if (pendingReview.includes(status)) {
                setStepState('active', 'pending');
                content.innerHTML = `
                <div class="section-card" style="text-align:center; padding:54px 32px;">
                    <div style="font-size:48px; margin-bottom:20px; color: var(--primary);">⏳</div>
                    <h2 style="font-size:20px; font-weight:700; color:var(--text-dark);">KYC Verification Under Review</h2>
                    <p style="font-size:14px; color:var(--text-muted); margin-top:8px; max-width:480px; margin-left:auto; margin-right:auto; line-height:1.6;">
                        Your KYC request is currently pending verification. Once approved, you will proceed to the Service Agreement step.
                    </p>
                    <button onclick="loadKycStatus()" class="kyc-btn outline" style="margin-top:28px;">
                        ↻ Refresh Status
                    </button>
                </div>`;

            } else if (status === 'approved') {
                setStepState('completed', 'active');
                loadEsignStatus();
            }

        } catch (e) {
            console.error('Error checking KYC status', e);
            showToast('Unable to check onboarding status. Please reload.', 'error');
        }
    }

    // ─── KYC SUBMISSION ──────────────────────────────────────────────
    async function initiateKyc(e) {
        e.preventDefault();
        const name = document.getElementById('kycName').value;
        const mobile = document.getElementById('kycMobile').value;
        const btn = document.getElementById('kycSubmitBtn');

        btn.disabled = true;
        btn.innerHTML = '<span class="login-loader"></span> Initiating secure KYC...';

        try {
            const res = await fetch('/api/user/kyc/initiate', {
                method: 'POST',
                headers: { 'Authorization': 'Bearer ' + userToken, 'Accept': 'application/json', 'Content-Type': 'application/json' },
                body: JSON.stringify({ name, mobile })
            });
            const data = await res.json();

            if (data.success && data.redirect_url) {
                showToast('Redirecting to secure gateway...', 'success');
                setTimeout(() => { window.location.href = data.redirect_url; }, 800);
            } else {
                showToast(data.message || 'Failed to initiate KYC.', 'error');
                btn.disabled = false;
                btn.innerHTML = 'Proceed to KYC Verification &nbsp;➔';
            }
        } catch (err) {
            showToast('Connection error. Please try again.', 'error');
            btn.disabled = false;
            btn.innerHTML = 'Proceed to KYC Verification &nbsp;➔';
        }
    }

    // ─── E-SIGN STATUS LOAD ─────────────────────────────────────────
    async function loadEsignStatus() {
        try {
            updateSidebar('agreement');
            const content = document.getElementById('onboardingContent');
            content.innerHTML = `
            <div class="section-card" style="display:flex; flex-direction:column; align-items:center; justify-content:center; padding:48px; gap:16px; min-height: 280px;">
                <div class="skeleton" style="width:52px; height:52px; border-radius:50%; flex-shrink:0;"></div>
                <div class="skeleton" style="width: 40%; height: 16px;"></div>
                <div class="skeleton" style="width: 65%; height: 12px;"></div>
            </div>`;

            const res = await fetch('/api/user/esign/status', {
                headers: { 'Authorization': 'Bearer ' + userToken, 'Accept': 'application/json' }
            });
            const data = await res.json();
            
            if (data.is_signed) {
                // Hide Stepper and Sidebar completely to focus on Thank You screen
                if (document.querySelector('.stepper')) {
                    document.querySelector('.stepper').style.display = 'none';
                }
                if (document.getElementById('sidebarContainer')) {
                    document.getElementById('sidebarContainer').style.display = 'none';
                }
                if (document.querySelector('.portal-grid')) {
                    document.querySelector('.portal-grid').style.gridTemplateColumns = '1fr';
                }

                content.innerHTML = `
                <div class="section-card" style="max-width: 720px; margin: 0 auto; text-align: center; padding: 48px 32px;">
                    <div style="font-size: 56px; color: var(--green); margin-bottom: 24px;">🎉</div>
                    <h2 style="font-size: 24px; font-weight: 800; color: var(--text-dark); margin-bottom: 12px; letter-spacing:-0.5px;">Thank You! Onboarding Complete</h2>
                    <p style="font-size: 14px; color: var(--text-muted); line-height: 1.6; max-width: 520px; margin: 0 auto 32px auto;">
                        Your identity has been verified and the service agreement is successfully executed. You are now registered with Grow Capital Research.
                    </p>

                    <!-- Accordions -->
                    <div style="text-align: left; margin-bottom: 32px; display: flex; flex-direction: column; gap: 14px;">
                        
                        <!-- KYC Accordion -->
                        <div class="accordion-item" style="border: 1px solid var(--border); border-radius: 12px; overflow: hidden;">
                            <div onclick="toggleAccordion('kyc-acc')" style="padding: 16px 20px; background: #ffffff; display: flex; justify-content: space-between; align-items: center; cursor: pointer; font-weight: 700; font-size: 14px; border-bottom: 1px solid var(--border);">
                                <span style="display:flex; align-items:center; gap:8px;">🛡️ KYC Verification Details</span>
                                <span id="kyc-acc-arrow" style="display: flex; align-items: center; transition: transform 0.2s; transform: rotate(0deg);">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#475569" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"></polyline></svg>
                                </span>
                            </div>
                            <div id="kyc-acc" style="display: none; padding: 20px; background: #ffffff;">
                                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; font-size: 13px;">
                                    <div><strong>Name:</strong> ${data.kyc?.customer_name || userInfo.name || 'N/A'}</div>
                                    <div><strong>Mobile:</strong> ${data.kyc?.customer_mobile || userInfo.mobile || 'N/A'}</div>
                                    <div><strong>Status:</strong> <span style="color: var(--green); font-weight: 700;">APPROVED</span></div>
                                    <div><strong>Completed At:</strong> ${data.kyc?.kyc_completed_at || 'N/A'}</div>
                                </div>
                            </div>
                        </div>

                        <!-- Agreement Accordion -->
                        <div class="accordion-item" style="border: 1px solid var(--border); border-radius: 12px; overflow: hidden;">
                            <div onclick="toggleAccordion('agreement-acc')" style="padding: 16px 20px; background: #ffffff; display: flex; justify-content: space-between; align-items: center; cursor: pointer; font-weight: 700; font-size: 14px; border-bottom: 1px solid var(--border);">
                                <span style="display:flex; align-items:center; gap:8px;">📄 Signed Service Agreement</span>
                                <span id="agreement-acc-arrow" style="display: flex; align-items: center; transition: transform 0.2s; transform: rotate(0deg);">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#475569" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"></polyline></svg>
                                </span>
                            </div>
                            <div id="agreement-acc" style="display: none; padding: 20px; background: #ffffff; text-align: center;">
                                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; flex-wrap: wrap; gap: 10px;">
                                    <span style="font-size: 13px; color: var(--text-muted);">Fully executed digital agreement</span>
                                    <button onclick="downloadSignedPdfOnly()" class="kyc-btn purple" style="font-size: 11px; padding: 8px 16px; border-radius: 50px;">📥 Download Agreement</button>
                                </div>
                                <iframe id="signedPdfIframe" style="width:100%; height:45vh; border:1px solid var(--border); border-radius:8px; background:#f8fafc;"></iframe>
                            </div>
                        </div>

                    </div>

                    <!-- Security Timeout Warning -->
                    <div style="background: var(--amber-bg); border: 1px solid var(--amber-border); color: var(--amber-text); padding: 14px 20px; border-radius: 12px; font-size: 12px; font-weight: 600; display: inline-flex; align-items: center; gap: 8px;">
                        🔐 Security Note: You will be automatically logged out in <span id="logoutCountdown" style="font-size: 14px; font-weight: 700;">15</span> seconds.
                    </div>
                </div>`;
                
                startAutoLogout(15);
            } else {
                if (document.getElementById('stepperRightAction')) {
                    document.getElementById('stepperRightAction').innerHTML = '';
                }
                if (urlParams.get('kyc') === 'success') {
                    content.innerHTML = `
                    <div class="section-card" style="text-align:center; padding:54px 32px;">
                        <div style="font-size:48px; margin-bottom:20px; color: var(--primary);">⏳</div>
                        <h2 style="font-size:20px; font-weight:700; color:var(--text-dark);">KYC Verified Successfully!</h2>
                        <p style="font-size:14px; color:var(--text-muted); margin-top:8px; max-width:480px; margin-left:auto; margin-right:auto; line-height:1.6;">
                            Preparing your service agreement. Redirecting you to e-Sign in a moment...
                        </p>
                    </div>`;

                    try {
                        const signRes = await fetch('/api/user/esign/sign', {
                            method: 'POST',
                            headers: { 'Authorization': 'Bearer ' + userToken, 'Accept': 'application/json' }
                        });
                        const signData = await signRes.json();
                        if (signData.status === 'success' && signData.esign_url) {
                            const cleanUrl = window.location.protocol + "//" + window.location.host + window.location.pathname;
                            window.history.replaceState({path: cleanUrl}, '', cleanUrl);

                            showToast('Redirecting to e-sign gateway...', 'success');
                            setTimeout(() => { window.location.href = signData.esign_url; }, 800);
                            return;
                        } else {
                            showToast(signData.message || 'Auto e-sign trigger failed.', 'error');
                        }
                    } catch (e) {
                        console.error('Auto sign trigger error', e);
                    }
                }

                content.innerHTML = `
                <div class="section-card" style="display:flex; flex-direction:column; align-items:center; justify-content:center; padding:48px; gap:16px; min-height: 280px;">
                    <div class="skeleton" style="width:52px; height:52px; border-radius:50%; flex-shrink:0;"></div>
                    <div class="skeleton" style="width: 40%; height: 16px;"></div>
                    <div class="skeleton" style="width: 65%; height: 12px;"></div>
                </div>`;
                
                const previewRes = await fetch('/api/user/esign/preview', {
                    headers: { 'Authorization': 'Bearer ' + userToken, 'Accept': 'application/json' }
                });
                const previewData = await previewRes.json();
                
                if (previewData.status === 'success' && previewData.pdf_base64) {
                    content.innerHTML = `
                    <div class="section-card pdf-container">
                        <div class="pdf-header">
                            <h2>Review & Sign Service Agreement</h2>
                            <p>Please review the agreement below and click the button to proceed to secure e-Signature gateway.</p>
                        </div>
                        <iframe src="data:application/pdf;base64,${previewData.pdf_base64}" style="width:100%; height:60vh; background:#f8fafc;"></iframe>
                        <div class="pdf-footer">
                            <button onclick="signAgreementInline(this)" class="kyc-btn purple" style="padding:14px 36px;">
                                Proceed to E-Sign &nbsp;➔
                            </button>
                        </div>
                    </div>`;
                } else {
                    content.innerHTML = `
                    <div class="kyc-banner failed">
                        <div class="kyc-left">
                            <div class="kyc-icon red">❌</div>
                            <div class="kyc-text">
                                <h3>Failed to load agreement preview</h3>
                                <p>${previewData.message || 'An error occurred. Please try again.'}</p>
                            </div>
                        </div>
                        <button onclick="loadEsignStatus()" class="kyc-btn amber">🔁 Retry Load</button>
                    </div>`;
                }
            }
        } catch (e) {
            console.error('Error esign status', e);
        }
    }

    async function loadSignedPdfInline() {
        try {
            const res = await fetch('/api/user/esign/download', {
                headers: { 'Authorization': 'Bearer ' + userToken }
            });
            if (!res.ok) throw new Error('Failed to load PDF');
            const blob = await res.blob();
            const blobUrl = URL.createObjectURL(blob);
            document.getElementById('signedPdfIframe').src = blobUrl;
        } catch (e) {
            console.error('Error loading signed PDF inline', e);
        }
    }

    async function downloadSignedPdfOnly() {
        try {
            const res = await fetch('/api/user/esign/download?download=1', {
                headers: { 'Authorization': 'Bearer ' + userToken }
            });
            if (!res.ok) throw new Error('Failed to load PDF');
            const blob = await res.blob();
            const blobUrl = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = blobUrl;
            a.download = 'GROW_CAPITAL_RESEARCH_Agreement.pdf';
            document.body.appendChild(a);
            a.click();
            a.remove();
        } catch (e) {
            console.error('Error downloading PDF', e);
            showToast('Error downloading the agreement document.', 'error');
        }
    }

    async function signAgreementInline(btn) {
        const originalText = btn.innerHTML;
        btn.innerHTML = '<span class="login-loader"></span> Processing...';
        btn.disabled = true;
        try {
            const res = await fetch('/api/user/esign/sign', {
                method: 'POST',
                headers: { 'Authorization': 'Bearer ' + userToken, 'Accept': 'application/json' }
            });
            const data = await res.json();
            if (data.status === 'success') {
                if (data.esign_url) {
                    showToast('Redirecting to e-sign gateway...', 'success');
                    setTimeout(() => { window.location.href = data.esign_url; }, 800);
                } else {
                    loadEsignStatus();
                }
            } else {
                showToast(data.message || 'Error initiating e-sign.', 'error');
                btn.innerHTML = originalText;
                btn.disabled = false;
            }
        } catch (e) {
            console.error('Error signing agreement', e);
            showToast('Connection error. Please try again.', 'error');
            btn.innerHTML = originalText;
            btn.disabled = false;
        }
    }

    // URL Callback Toasts
    if (urlParams.get('kyc') === 'success') showToast('✅ KYC verified successfully!', 'success');
    if (urlParams.get('kyc') === 'pending')  showToast('⏳ KYC submitted and under review.', 'info');
    if (urlParams.get('kyc') === 'failed')   showToast('❌ KYC verification failed. Please retry.', 'error');
    if (urlParams.get('esign') === 'success') showToast('✅ Agreement signed successfully!', 'success');

    // INIT
    loadKycStatus();
</script>

</body>
</html>

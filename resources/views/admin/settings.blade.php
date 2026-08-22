@extends('layouts.admin')

@section('title', 'API Settings')
@section('header_title', 'API Settings')
@section('header_actions')
    <span class="header-badge" style="padding: 5px 12px; background: rgba(0,75,135,0.12); border: 1px solid rgba(0,75,135,0.25); border-radius: 20px; font-size: 12px; color: var(--primary); font-weight: 500;">🔒 Secure DB Storage</span>
@endsection

@section('styles')
        /* --- TABS --- */
        .tabs {
            display: flex;
            gap: 6px;
            margin-bottom: 28px;
            background: var(--card);
            border: 1px solid var(--border);
            padding: 6px;
            border-radius: 14px;
            width: fit-content;
        }

        .tab-btn {
            padding: 9px 20px;
            border-radius: 10px;
            border: none;
            background: transparent;
            color: var(--text-muted);
            font-size: 13px;
            font-weight: 500;
            font-family: inherit;
            cursor: pointer;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            gap: 7px;
        }

        .tab-btn:hover { color: var(--text); background: rgba(255,255,255,0.05); }
        .tab-btn.active { background: var(--primary); color: #fff; font-weight: 600; }

        .status-dot {
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: var(--text-muted);
        }

        .status-dot.configured { background: var(--green); }

        /* --- SETTING PANELS --- */
        .settings-panel { display: none; }
        .settings-panel.active { display: block; }

        .panel-card {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 20px;
            padding: 32px;
            max-width: 700px;
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
            font-size: 24px;
        }

        .panel-icon.smtp     { background: rgba(108,99,255,0.15); }
        .panel-icon.sms      { background: rgba(16,185,129,0.15); }
        .panel-icon.digio    { background: rgba(59,130,246,0.15); }
        .panel-icon.razorpay { background: rgba(245,158,11,0.15); }

        .panel-title-text h3 { font-size: 18px; font-weight: 600; color: var(--text-dark); }
        .panel-title-text p  { font-size: 13px; color: var(--text-muted); margin-top: 2px; }

        .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
        .form-grid .full { grid-column: 1 / -1; }

        .form-group { display: flex; flex-direction: column; gap: 7px; }

        .form-group label {
            font-size: 12px;
            font-weight: 600;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
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

        .form-group select option { background: #ffffff; color: var(--text-dark); }

        .form-group input:focus,
        .form-group select:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(0,75,135,0.08);
        }

        .form-group input::placeholder { color: var(--text-muted); }

        .btn-save {
            margin-top: 24px;
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
        }

        .btn-save:hover { transform: translateY(-1px); box-shadow: 0 6px 20px rgba(0,75,135,0.25); }
        .btn-save:disabled { opacity: 0.6; cursor: not-allowed; transform: none; }

        .toast {
            position: fixed;
            bottom: 28px;
            right: 28px;
            padding: 14px 20px;
            border-radius: 12px;
            font-size: 14px;
            font-weight: 500;
            display: none;
            align-items: center;
            gap: 10px;
            z-index: 9999;
            box-shadow: 0 8px 24px rgba(0,0,0,0.1);
            animation: slideUp 0.3s ease;
        }

        @keyframes slideUp { from { transform: translateY(20px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }

        .toast-success { background: rgba(16,185,129,0.12); border: 1px solid rgba(16,185,129,0.25); color: var(--green); display: flex; }
        .toast-error   { background: rgba(239,68,68,0.1);  border: 1px solid rgba(239,68,68,0.2);  color: var(--red); display: flex; }

        .divider { border: none; border-top: 1px solid var(--border); margin: 24px 0; }

        .info-note {
            display: flex;
            gap: 10px;
            padding: 12px 16px;
            background: rgba(0,75,135,0.06);
            border: 1px solid rgba(0,75,135,0.15);
            border-radius: 10px;
            font-size: 12px;
            color: var(--text-muted);
            margin-bottom: 20px;
        }

        /* --- EYE TOGGLE WRAPPER --- */
        .pw-wrap {
            position: relative;
            display: flex;
            align-items: center;
        }

        .pw-wrap input {
            padding-right: 44px !important;
            width: 100%;
        }

        .eye-btn {
            position: absolute;
            right: 12px;
            background: none;
            border: none;
            cursor: pointer;
            color: var(--text-muted);
            padding: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: color 0.2s;
            border-radius: 6px;
            flex-shrink: 0;
        }

        .eye-btn:hover { color: var(--primary); }
        .eye-btn svg { width: 18px; height: 18px; stroke: currentColor; fill: none; stroke-width: 2; stroke-linecap: round; stroke-linejoin: round; }

        @media (max-width: 768px) {
            .tabs { width: 100%; overflow-x: auto; flex-wrap: nowrap; -webkit-overflow-scrolling: touch; border-radius: 12px; padding: 8px; margin-bottom: 20px; }
            .tabs::-webkit-scrollbar { display: none; }
            .tab-btn { flex-shrink: 0; padding: 6px 12px; font-size: 11px; }
            .panel-card { padding: 16px; border-radius: 16px; }
            .form-grid { grid-template-columns: 1fr; gap: 12px; }
            .panel-title { gap: 10px; margin-bottom: 16px; }
            .panel-title-text h3 { font-size: 15px; }
            .panel-title-text p { font-size: 11px; line-height: 1.4; }
            .panel-icon { width: 36px; height: 36px; font-size: 18px; }
            .panel-icon svg { width: 18px; height: 18px; }
            .form-group label { font-size: 10px; }
            .form-group input, .form-group select, .form-group textarea { font-size: 12px; padding: 10px 12px; }
            .info-note { font-size: 11px; padding: 10px 12px; }
            .btn-save { width: 100%; justify-content: center; margin-top: 16px; font-size: 13px; padding: 10px 20px; }
        }
@endsection

@section('content')
        <!-- TABS -->
        <div class="tabs" role="tablist">
            <button class="tab-btn active" id="tab-smtp" onclick="switchTab('smtp')" role="tab">
                <span class="status-dot" id="dot-smtp"></span>
                <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: middle;"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>
                SMTP
            </button>
            <button class="tab-btn" id="tab-digio" onclick="switchTab('digio')" role="tab">
                <span class="status-dot" id="dot-digio"></span>
                <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: middle;"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                Digio
            </button>
            <button class="tab-btn" id="tab-kra" onclick="switchTab('kra')" role="tab">
                <span class="status-dot" id="dot-kra"></span>
                <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: middle;"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><line x1="9" y1="3" x2="9" y2="21"></line></svg>
                KRA Settings
            </button>
            <button class="tab-btn" id="tab-kyc" onclick="switchTab('kyc')" role="tab">
                <span class="status-dot" id="dot-kyc"></span>
                <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: middle;"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
                Declaration Text
            </button>
        </div>

        <!-- SMTP PANEL -->
        <div id="panel-smtp" class="settings-panel active">
            <div class="panel-card">
                <div class="panel-title">
                    <div class="panel-icon smtp">
                        <svg viewBox="0 0 24 24" width="22" height="22" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" style="color: #6c63ff;"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>
                    </div>
                    <div class="panel-title-text">
                        <h3>SMTP Email Configuration</h3>
                        <p>Used to send OTP emails, transactional notifications, and alerts.</p>
                    </div>
                </div>
                <div class="info-note">ℹ️ These credentials will be used to dynamically configure the mailer at runtime — no .env changes needed.</div>
                <form id="form-smtp" onsubmit="saveSettings(event, 'smtp')">
                    <div class="form-grid">
                        <div class="form-group">
                            <label>SMTP Host</label>
                            <input type="text" name="host" id="smtp-host" placeholder="smtp.gmail.com" />
                        </div>
                        <div class="form-group">
                            <label>Port</label>
                            <input type="number" name="port" id="smtp-port" placeholder="587" />
                        </div>
                        <div class="form-group">
                            <label>Username</label>
                            <input type="email" name="username" id="smtp-username" placeholder="yourmail@gmail.com" />
                        </div>
                        <div class="form-group">
                            <label>Password / App Password</label>
                            <div class="pw-wrap">
                                <input type="password" name="password" id="smtp-password" placeholder="••••••••" />
                                <button type="button" class="eye-btn" onclick="togglePw('smtp-password', this)" title="Show/Hide">
                                    <svg id="eye-smtp-password" viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                </button>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Encryption</label>
                            <select name="encryption" id="smtp-encryption">
                                <option value="tls">TLS</option>
                                <option value="ssl">SSL</option>
                                <option value="">None</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>From Email</label>
                            <input type="email" name="from_address" id="smtp-from_address" placeholder="noreply@growcapitals.com" />
                        </div>
                        <div class="form-group full">
                            <label>From Name</label>
                            <input type="text" name="from_name" id="smtp-from_name" placeholder="Grow Capitals Research" />
                        </div>
                    </div>
                    <button type="submit" class="btn-save" id="save-smtp"><svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: middle;"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path><polyline points="17 21 17 13 7 13 7 21"></polyline><polyline points="7 3 7 8 15 8"></polyline></svg> Save SMTP Settings</button>
                </form>
            </div>
        </div>



        <!-- DIGIO PANEL -->
        <div id="panel-digio" class="settings-panel">
            <div class="panel-card">
                <div class="panel-title">
                    <div class="panel-icon digio">
                        <svg viewBox="0 0 24 24" width="22" height="22" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" style="color: #3b82f6;"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                    </div>
                    <div class="panel-title-text">
                        <h3>Digio API Configuration</h3>
                        <p>KYC verification, e-signing, and document management services.</p>
                    </div>
                </div>
                <div class="info-note">ℹ️ Get credentials from your Digio dashboard at app.digio.in</div>
                <form id="form-digio" onsubmit="saveSettings(event, 'digio')">
                    <div class="form-grid">
                        <div class="form-group">
                            <label>Client ID</label>
                            <input type="text" name="client_id" id="digio-client_id" placeholder="DID••••••••••" />
                        </div>
                        <div class="form-group">
                            <label>Client Secret</label>
                            <div class="pw-wrap">
                                <input type="password" name="client_secret" id="digio-client_secret" placeholder="••••••••••••" />
                                <button type="button" class="eye-btn" onclick="togglePw('digio-client_secret', this)" title="Show/Hide">
                                    <svg viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                </button>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Base URL</label>
                            <input type="url" name="base_url" id="digio-base_url" placeholder="https://api.digio.in" />
                        </div>
                        <div class="form-group">
                            <label>Environment</label>
                            <select name="environment" id="digio-environment">
                                <option value="production">Production</option>
                                <option value="sandbox">Sandbox / Testing</option>
                            </select>
                        </div>
                        <div class="form-group full">
                            <label>KYC Workflow / Template Name</label>
                            <input type="text" name="workflow" id="digio-workflow" placeholder="e.g. kyc_with_aadhaar" />
                            <span style="font-size:11px; color:var(--text-muted); margin-top:5px; display:block; line-height:1.6;">
                                💡 Digio dashboard → <strong>Templates</strong> me jaake workflow name copy karo. Example: <code style="background:rgba(255,255,255,0.07); padding:2px 7px; border-radius:4px; color:#a5b4fc;">kyc_with_aadhaar</code>
                            </span>
                        </div>
                    </div>
                    <button type="submit" class="btn-save" id="save-digio"><svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: middle;"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path><polyline points="17 21 17 13 7 13 7 21"></polyline><polyline points="7 3 7 8 15 8"></polyline></svg> Save Digio Settings</button>
                </form>
            </div>
        </div>



        <!-- KRA PANEL -->
        <div id="panel-kra" class="settings-panel">
            <div class="panel-card" style="max-width: 800px;">
                <div class="panel-title">
                    <div class="panel-icon kra" style="background: rgba(99,102,241,0.15); color: #6366f1;">
                        <svg viewBox="0 0 24 24" width="22" height="22" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><line x1="9" y1="3" x2="9" y2="21"></line></svg>
                    </div>
                    <div class="panel-title-text">
                        <h3>NDML KRA Configuration</h3>
                        <p>Setup SOAP endpoints for registration/inquiry and SFTP server credentials for PDF uploads.</p>
                    </div>
                </div>
                <div class="info-note">ℹ️ Supports both live production and pilot UAT environment profiles for NDML KRA verification.</div>
                
                <!-- Connection Test Result Banner -->
                <div id="kraTestResult" class="alert-box" style="display: none; margin-bottom: 20px;">
                    <div style="flex: 1;">
                        <strong id="kraTestResultTitle"></strong>
                        <p id="kraTestResultDesc" style="margin-top: 4px; font-size: 12px;"></p>
                    </div>
                </div>

                <form id="form-kra" onsubmit="saveSettings(event, 'kra')">
                    <h4 style="font-size: 13px; font-weight:700; color:var(--text-dark); margin-bottom: 12px; text-transform:uppercase; letter-spacing:0.5px;">SOAP Webservices</h4>
                    <div class="form-grid" style="margin-bottom: 24px;">
                        <div class="form-group">
                            <label>NDML User ID / POS Code</label>
                            <input type="text" name="ndml_user_id" id="kra-ndml_user_id" placeholder="e.g. USER1234" />
                        </div>
                        <div class="form-group">
                            <label>MI Code / Okra Code (BP ID)</label>
                            <input type="text" name="ndml_bp_id" id="kra-ndml_bp_id" placeholder="e.g. B1465" />
                        </div>
                        <div class="form-group">
                            <label>Portal Login Password</label>
                            <div class="pw-wrap">
                                <input type="password" name="ndml_password" id="kra-ndml_password" placeholder="••••••••" />
                                <button type="button" class="eye-btn" onclick="togglePw('kra-ndml_password', this)" title="Show/Hide">
                                    <svg viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                </button>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Passkey (Registration Hashing)</label>
                            <div class="pw-wrap">
                                <input type="password" name="ndml_passkey" id="kra-ndml_passkey" placeholder="e.g. SecretPasskey" />
                                <button type="button" class="eye-btn" onclick="togglePw('kra-ndml_passkey', this)" title="Show/Hide">
                                    <svg viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                </button>
                            </div>
                        </div>
                        <div class="form-group full">
                            <label>Encryption Key (Inquiry Passcode)</label>
                            <div class="pw-wrap">
                                <input type="password" name="ndml_encryption_key" id="kra-ndml_encryption_key" placeholder="e.g. EncKey8" />
                                <button type="button" class="eye-btn" onclick="togglePw('kra-ndml_encryption_key', this)" title="Show/Hide">
                                    <svg viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="divider"></div>

                    <h4 style="font-size: 13px; font-weight:700; color:var(--text-dark); margin-bottom: 12px; text-transform:uppercase; letter-spacing:0.5px;">SFTP Server Document Upload</h4>
                    <div class="form-grid" style="margin-bottom: 24px;">
                        <div class="form-group full">
                            <label>SFTP Server Host</label>
                            <input type="text" name="sftp_host" id="kra-sftp_host" placeholder="e.g. sftp.kra.ndml.in" />
                        </div>
                        <div class="form-group">
                            <label>SFTP Port</label>
                            <input type="number" name="sftp_port" id="kra-sftp_port" placeholder="22" />
                        </div>
                        <div class="form-group">
                            <label>SFTP Username</label>
                            <input type="text" name="sftp_username" id="kra-sftp_username" placeholder="e.g. sftp_user" />
                        </div>
                        <div class="form-group full">
                            <label>SFTP Password</label>
                            <div class="pw-wrap">
                                <input type="password" name="sftp_password" id="kra-sftp_password" placeholder="••••••••" />
                                <button type="button" class="eye-btn" onclick="togglePw('kra-sftp_password', this)" title="Show/Hide">
                                    <svg viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="divider"></div>

                    <h4 style="font-size: 13px; font-weight:700; color:var(--text-dark); margin-bottom: 12px; text-transform:uppercase; letter-spacing:0.5px;">Workflow Modes & Automation</h4>
                    <div class="form-grid">
                        <div class="form-group">
                            <label>Environment Mode</label>
                            <select name="ndml_uat_mode" id="kra-ndml_uat_mode">
                                <option value="1">UAT / Pilot Server</option>
                                <option value="0">Production Server</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Auto-Sync Trigger</label>
                            <select name="auto_upload_on_approval" id="kra-auto_upload_on_approval">
                                <option value="1">Auto-Upload on KYC Approval</option>
                                <option value="0">Disabled (Manual Sync Only)</option>
                            </select>
                        </div>
                    </div>

                    <div style="display:flex; justify-content:space-between; align-items:center; margin-top:32px;">
                        <button type="submit" class="btn-save" id="save-kra" style="margin-top:0;"><svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: middle;"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path><polyline points="17 21 17 13 7 13 7 21"></polyline><polyline points="7 3 7 8 15 8"></polyline></svg> Save KRA Settings</button>
                        
                        <div style="display:flex; gap:12px;">
                            <button type="button" class="btn btn-sync" id="btn-test-soap" onclick="runKraTest('soap')" style="font-size:12px; padding:10px 16px;">⚡ Test SOAP API</button>
                            <button type="button" class="btn btn-sync" id="btn-test-upload" onclick="runKraTest('upload')" style="font-size:12px; padding:10px 16px;">📁 Test SFTP Upload</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>



        <!-- KYC PANEL -->
        <div id="panel-kyc" class="settings-panel">
            <div class="panel-card">
                <div class="panel-title">
                    <div class="panel-icon kyc" style="background: rgba(16,185,129,0.15); color: #10b981;">
                        <svg viewBox="0 0 24 24" width="22" height="22" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
                    </div>
                    <div class="panel-title-text">
                        <h3>KYC Declaration Settings</h3>
                        <p>Configure the legal authorization text users must agree to before submitting KYC.</p>
                    </div>
                </div>
                <div class="info-note">ℹ️ This text is displayed above the submit button on the user's KYC verification page.</div>
                <form id="form-kyc" onsubmit="saveSettings(event, 'kyc')">
                    <div class="form-grid">
                        <div class="form-group full">
                            <label>KYC Authorization Declaration Text</label>
                            <textarea name="declaration" id="kyc-declaration" placeholder="Enter KYC declaration text" required style="min-height: 120px; resize: vertical;"></textarea>
                        </div>
                    </div>
                    <button type="submit" class="btn-save" id="save-kyc"><svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: middle;"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path><polyline points="17 21 17 13 7 13 7 21"></polyline><polyline points="7 3 7 8 15 8"></polyline></svg> Save KYC Settings</button>
                </form>
            </div>
        </div>
@endsection

@section('scripts')
<!-- TOAST -->
<div class="toast" id="toast"></div>

<script>
    const API_BASE = '/api';
    const groups   = ['smtp', 'digio', 'kyc', 'kra'];

    function getHeaders() {
        return {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'Authorization': `Bearer ${adminToken}`,
        };
    }

    // ---- TAB SWITCHING ----
    function switchTab(group) {
        groups.forEach(g => {
            document.getElementById(`tab-${g}`).classList.remove('active');
            document.getElementById(`panel-${g}`).classList.remove('active');
        });
        document.getElementById(`tab-${group}`).classList.add('active');
        document.getElementById(`panel-${group}`).classList.add('active');
        loadSettings(group);
    }

    // ---- LOAD SETTINGS ----
    async function loadSettings(group) {
        try {
            const res  = await fetch(`${API_BASE}/admin/settings/${group}`, { headers: getHeaders() });
            const data = await res.json();
            if (!data.success) return;

            const settings = data.settings;
            Object.entries(settings).forEach(([key, value]) => {
                const el = document.getElementById(`${group}-${key}`);
                if (el) el.value = value || '';
            });
        } catch (err) {
            console.warn('Could not load', group, 'settings:', err);
        }
    }

    // ---- SAVE SETTINGS ----
    async function saveSettings(e, group) {
        e.preventDefault();
        const form = document.getElementById(`form-${group}`);
        const btn  = document.getElementById(`save-${group}`);
        const formData = new FormData(form);
        const payload  = Object.fromEntries(formData.entries());

        const originalHtml = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '⏳ Saving...';

        try {
            const res  = await fetch(`${API_BASE}/admin/settings/${group}`, {
                method: 'POST',
                headers: getHeaders(),
                body: JSON.stringify(payload),
            });
            const data = await res.json();

            if (data.success) {
                showToast(data.message, 'success');
                document.getElementById(`dot-${group}`).classList.add('configured');
            } else {
                showToast(data.message || 'Save failed.', 'error');
            }
        } catch (err) {
            showToast('Network error. Please try again.', 'error');
        } finally {
            btn.disabled = false;
            btn.innerHTML = originalHtml;
        }
    }

    // ---- RUN KRA TEST ----
    async function runKraTest(type) {
        const btn = document.getElementById(`btn-test-${type}`);
        const resultBox = document.getElementById('kraTestResult');
        const titleEl = document.getElementById('kraTestResultTitle');
        const descEl = document.getElementById('kraTestResultDesc');

        const originalHtml = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '⏳ Testing...';

        resultBox.style.display = 'none';
        resultBox.className = 'alert-box';

        try {
            const res = await fetch(`${API_BASE}/admin/settings/kra/test-${type}`, {
                method: 'POST',
                headers: getHeaders()
            });
            const data = await res.json();
            
            resultBox.style.display = 'flex';
            if (res.ok && (data.status === 'success' || data.success)) {
                resultBox.className = 'alert-box success';
                titleEl.innerHTML = '✅ Connection Succeeded';
                descEl.innerHTML = data.message;
            } else {
                resultBox.className = 'alert-box error';
                titleEl.innerHTML = '❌ Connection Failed';
                descEl.innerHTML = data.message || 'Unknown error occurred.';
            }
        } catch (e) {
            resultBox.style.display = 'flex';
            resultBox.className = 'alert-box error';
            titleEl.innerHTML = '❌ Connection Error';
            descEl.innerHTML = 'A network error occurred while testing connection.';
        } finally {
            btn.disabled = false;
            btn.innerHTML = originalHtml;
        }
    }

    // ---- TOAST ----
    function showToast(msg, type = 'success') {
        const el = document.getElementById('toast');
        el.className = `toast toast-${type}`;
        el.innerHTML = `<span>${type === 'success' ? '✅' : '❌'}</span> ${msg}`;
        el.style.display = 'flex';
        setTimeout(() => el.style.display = 'none', 4000);
    }

    // ---- Load all statuses on mount ----
    async function loadSummary() {
        try {
            const res  = await fetch(`${API_BASE}/admin/settings`, { headers: getHeaders() });
            const data = await res.json();
            if (!data.success) return;
            Object.entries(data.settings).forEach(([group, info]) => {
                if (info.configured) {
                    document.getElementById(`dot-${group}`)?.classList.add('configured');
                }
            });
        } catch {}
    }

    // ---- EYE TOGGLE ----
    function togglePw(inputId, btn) {
        const inp = document.getElementById(inputId);
        const isHidden = inp.type === 'password';
        inp.type = isHidden ? 'text' : 'password';

        // Swap icon: eye-off when visible, eye when hidden
        btn.querySelector('svg').innerHTML = isHidden
            ? '<path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94"/><path d="M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19"/><line x1="1" y1="1" x2="23" y2="23"/>'
            : '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>';

        btn.style.color = isHidden ? 'var(--primary)' : '';
    }

    // INIT
    loadSummary();
    loadSettings('smtp'); // Load active tab on page load
</script>
@endsection

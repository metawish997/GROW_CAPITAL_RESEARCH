@extends('layouts.admin')

@section('title', 'User Details')
@section('header_title')
    <a href="/admin/users" class="back-link" style="color: var(--text-muted); text-decoration: none;">Users</a> / <span id="headerName">Loading...</span>
@endsection

@section('header_actions')
    <button id="consolidatedDownloadBtn" onclick="downloadConsolidatedPdf(userId)" class="btn" style="display:none; background:var(--primary); color:#ffffff; border-color:var(--primary); font-size:13px; font-weight:700; padding:8px 16px; border-radius:8px;">
        <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: middle; margin-right: 4px;">
            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
            <polyline points="7 10 12 15 17 10"></polyline>
            <line x1="12" y1="15" x2="12" y2="3"></line>
        </svg>
        Download KYC & Agreement
    </button>
@endsection

@section('styles')
    .back-link:hover { color: var(--text-dark); }
    .info-card { background: var(--card); border: 1px solid var(--border); border-radius: 16px; overflow: hidden; margin-bottom: 24px; }
    .card-header { padding: 16px 24px; border-bottom: 1px solid var(--border); font-size: 15px; font-weight: 700; background: #f8fafc; display: flex; justify-content: space-between; align-items: center; gap: 10px; color: var(--text-dark); }
    
    .grid-list { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 24px; padding: 24px; }
    .grid-item { display: flex; flex-direction: column; gap: 6px; }
    .grid-label { font-size: 11px; color: var(--text-muted); text-transform: uppercase; font-weight: 600; letter-spacing: 0.5px; }
    .grid-value { font-size: 14px; color: var(--text); font-weight: 500; word-break: break-all; }

    .badge { display: inline-flex; align-items: center; padding: 4px 10px; border-radius: 20px; font-size: 11px; font-weight: 600; text-transform: capitalize; }
    .badge.approved { background: rgba(16,185,129,0.12); color: var(--green); border: 1px solid rgba(16,185,129,0.25); }
    .badge.pending, .badge.initiated, .badge.approval_pending { background: rgba(0,75,135,0.08); color: var(--primary); border: 1px solid rgba(0,75,135,0.15); }
    .badge.failed, .badge.rejected { background: rgba(239,68,68,0.1); color: var(--red); border: 1px solid rgba(239,68,68,0.2); }
    .badge.not-started { background: rgba(0,0,0,0.04); color: var(--text-muted); border: 1px solid var(--border); }

    .loader { padding: 40px; text-align: center; color: var(--text-muted); }

    .btn { padding: 8px 16px; border-radius: 8px; font-size: 13px; font-weight: 700; cursor: pointer; border: 1px solid transparent; font-family: inherit; transition: all 0.2s; display: inline-flex; align-items: center; gap: 6px; }
    .btn-sync { background: #ffffff; border-color: var(--border); color: var(--text); }
    .btn-sync:hover { background: #f8fafc; border-color: var(--text-muted); }

    pre { background: #f8fafc; padding: 16px; border: 1px solid var(--border); border-radius: 8px; overflow-x: auto; font-size: 12px; color: var(--text); margin-top: 10px; white-space: pre-wrap; word-break: break-all; font-family: monospace; }
    
    .card-header-actions { display: flex; gap: 10px; align-items: center; }
    
    .aadhaar-pan-container { display: flex; gap: 24px; margin-bottom: 24px; background: #f8fafc; padding: 20px; border-radius: 16px; border: 1px solid var(--border); }
    .demographics-grid { flex-grow: 1; display: grid; grid-template-columns: repeat(2, 1fr); gap: 16px 24px; }
    .demo-label { font-size: 11px; color: var(--text-muted); display: block; text-transform: uppercase; font-weight:600; letter-spacing:0.5px; }
    .demo-value { color: var(--text-dark); font-size:14px; font-weight:500; word-break: break-all; }

    @media (max-width: 768px) {
        .card-header { padding: 12px 16px; font-size: 13px; flex-wrap: nowrap; }
        .card-title-text { white-space: nowrap; overflow: hidden; text-overflow: ellipsis; font-size: 13px; }
        .card-header-actions { gap: 6px; flex-shrink: 0; }
        .card-header-actions .btn { padding: 6px 8px; font-size: 10px; }
        .card-header-actions .btn svg { display: none; }

        .aadhaar-pan-container { flex-direction: column; padding: 16px; gap: 16px; }
        .demographics-grid { grid-template-columns: repeat(2, 1fr); gap: 12px; }
        .demo-label { font-size: 9px; }
        .demo-value { font-size: 11px; }

        .grid-list { padding: 12px; gap: 12px; grid-template-columns: repeat(2, 1fr); }
        .grid-label { font-size: 9px; }
        .grid-value { font-size: 11px; word-break: break-all; }
        .btn { padding: 6px 12px; font-size: 11px; }
        /* Header specific fixes */
        .header h2 { font-size: 14px !important; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 50vw; }
        #consolidatedDownloadBtn { padding: 6px 10px !important; font-size: 11px !important; white-space: normal; line-height: 1.2; text-align: center; }
        #consolidatedDownloadBtn svg { width: 12px; height: 12px; }
    }
@endsection

@section('content')
    <div id="contentArea">
        <div class="loader">Fetching user details...</div>
    </div>

    <!-- Edit User Profile Modal -->
    <div id="editModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:9999; align-items:center; justify-content:center;">
        <div style="background:#ffffff; border-radius:16px; width:550px; max-width:90%; padding:28px; box-shadow: 0 10px 25px rgba(0,0,0,0.15); display:flex; flex-direction:column; gap:20px;">
            <div style="display:flex; justify-content:space-between; align-items:center;">
                <h3 style="font-size:16px; font-weight:700; margin:0; color:var(--text-dark);">Edit Customer Registration Details</h3>
                <button onclick="closeEditModal()" style="background:none; border:none; font-size:20px; cursor:pointer; color:var(--text-muted);">&times;</button>
            </div>
            
            <form id="editForm" onsubmit="saveUserDetails(event)" style="display:flex; flex-direction:column; gap:16px;">
                <!-- Autofill Helper -->
                <div id="autofillContainer" style="display:none; background:#f0fdf4; border:1px solid #bbf7d0; padding:10px 12px; border-radius:8px; justify-content:space-between; align-items:center; font-size:12px; color:#166534;">
                    <span>Aadhaar data was found for this user!</span>
                    <button type="button" class="btn btn-sync" onclick="autofillFromAadhaar()" style="font-size:11px; padding:4px 8px; border-color:#bbf7d0; color:#166534; background:#ffffff;">Autofill from Aadhaar</button>
                </div>

                <div style="display:grid; grid-template-columns: repeat(2, 1fr); gap:14px;">
                    <div>
                        <label style="font-size:12px; font-weight:600; color:var(--text-muted); display:block; margin-bottom:6px;">PAN Card Number</label>
                        <input type="text" name="pan_card" style="width:100%; border:1px solid var(--border); padding:8px 12px; border-radius:8px; font-size:13px; text-transform:uppercase;" />
                    </div>
                    <div>
                        <label style="font-size:12px; font-weight:600; color:var(--text-muted); display:block; margin-bottom:6px;">PAN Card Name</label>
                        <input type="text" name="pan_card_name" style="width:100%; border:1px solid var(--border); padding:8px 12px; border-radius:8px; font-size:13px;" />
                    </div>
                </div>

                <div style="display:grid; grid-template-columns: repeat(2, 1fr); gap:14px;">
                    <div>
                        <label style="font-size:12px; font-weight:600; color:var(--text-muted); display:block; margin-bottom:6px;">Father's Name</label>
                        <input type="text" name="father_name" style="width:100%; border:1px solid var(--border); padding:8px 12px; border-radius:8px; font-size:13px;" />
                    </div>
                    <div>
                        <label style="font-size:12px; font-weight:600; color:var(--text-muted); display:block; margin-bottom:6px;">Date of Birth</label>
                        <input type="text" name="dob" placeholder="DD/MM/YYYY" style="width:100%; border:1px solid var(--border); padding:8px 12px; border-radius:8px; font-size:13px;" />
                    </div>
                </div>

                <div>
                    <label style="font-size:12px; font-weight:600; color:var(--text-muted); display:block; margin-bottom:6px;">Marital Status</label>
                    <select name="marital_status" style="width:100%; border:1px solid var(--border); padding:8px 12px; border-radius:8px; font-size:13px; background:#ffffff; height: 38px;">
                        <option value="">Select Status</option>
                        <option value="Single">Single</option>
                        <option value="Married">Married</option>
                        <option value="Divorced">Divorced</option>
                        <option value="Widowed">Widowed</option>
                    </select>
                </div>

                <div style="display:grid; grid-template-columns: repeat(3, 1fr); gap:10px;">
                    <div style="grid-column: span 3;">
                        <label style="font-size:12px; font-weight:600; color:var(--text-muted); display:block; margin-bottom:6px;">Address Line</label>
                        <input type="text" name="address" style="width:100%; border:1px solid var(--border); padding:8px 12px; border-radius:8px; font-size:13px;" />
                    </div>
                    <div>
                        <label style="font-size:12px; font-weight:600; color:var(--text-muted); display:block; margin-bottom:6px;">City</label>
                        <input type="text" name="city" style="width:100%; border:1px solid var(--border); padding:8px 12px; border-radius:8px; font-size:13px;" />
                    </div>
                    <div>
                        <label style="font-size:12px; font-weight:600; color:var(--text-muted); display:block; margin-bottom:6px;">State</label>
                        <input type="text" name="state" style="width:100%; border:1px solid var(--border); padding:8px 12px; border-radius:8px; font-size:13px;" />
                    </div>
                    <div>
                        <label style="font-size:12px; font-weight:600; color:var(--text-muted); display:block; margin-bottom:6px;">Pincode</label>
                        <input type="text" name="pincode" style="width:100%; border:1px solid var(--border); padding:8px 12px; border-radius:8px; font-size:13px;" />
                    </div>
                </div>

                <div style="display:flex; justify-content:flex-end; gap:12px; margin-top:12px;">
                    <button type="button" class="btn btn-sync" onclick="closeEditModal()">Cancel</button>
                    <button type="submit" class="btn" style="background:var(--primary); color:#ffffff; border-color: var(--primary);">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    // Inject userId from Laravel blade
    const userId = {{ $id }};
    let currentUserObj = null;

    async function loadUserDetails() {
        try {
            const res = await fetch(`/api/admin/users/${userId}`, {
                headers: { Authorization: `Bearer ${adminToken}`, Accept: 'application/json' }
            });
            const data = await res.json();
            
            if (data.success) {
                renderUser(data.user);
            } else {
                document.getElementById('contentArea').innerHTML = `<div class="loader" style="color:var(--red);">${data.message || 'Error loading user.'}</div>`;
            }
        } catch (e) {
            document.getElementById('contentArea').innerHTML = `<div class="loader" style="color:var(--red);">Network error.</div>`;
        }
    }

    function renderUser(u) {
        currentUserObj = u;
        const displayName = (u.kyc && u.kyc.customer_name) ? u.kyc.customer_name : (u.name || '—');
        document.getElementById('headerName').textContent = displayName;

        // Toggle consolidated download button
        const downloadBtn = document.getElementById('consolidatedDownloadBtn');
        if (u.kyc && (u.kyc.status === 'approved' || u.kyc.status === 'completed' || u.kyc.status === 'success')) {
            downloadBtn.style.display = 'inline-flex';
        } else {
            downloadBtn.style.display = 'none';
        }

        const joinDate = new Date(u.created_at).toLocaleString('en-IN');
        
        let kycHtml = '';
        if (u.kyc) {
            const k = u.kyc;
            const s = k.status;
            let c = 'pending';
            let displayText = s.replace('_', ' ');
            if(s === 'approved') {
                c = 'approved';
                displayText = 'completed';
            }
            if(s === 'failed' || s === 'rejected' || s === 'expired') c = 'failed';
            
            let detailsHtml = '';
            if (k.kyc_details) {
                const details = k.kyc_details;
                const aadhaar = details.aadhaar || {};
                const currentAddr = aadhaar.current_address || '—';
                
                // Aadhaar Image
                let aadhaarPhotoHtml = '';
                if (aadhaar.image) {
                    aadhaarPhotoHtml = `<img src="data:image/jpeg;base64,${aadhaar.image}" style="width: 80px; height: 100px; object-fit: cover; border-radius: 8px; border: 1px solid var(--border); background: #ffffff;" />`;
                } else {
                    aadhaarPhotoHtml = `<div style="width: 80px; height: 100px; display: flex; align-items: center; justify-content: center; background: #f1f5f9; border-radius: 8px; border: 1px solid var(--border); color: var(--text-muted); font-size: 11px;">No Photo</div>`;
                }

                // Selfie HTML
                const hasSelfie = details.selfie_file || details.selfie_local_path;
                let selfieImgHtml = '';
                if (hasSelfie) {
                    selfieImgHtml = `
                        <div style="position:relative; width: 100%; height: 150px; background: #f8fafc; border-radius: 8px; border: 1px solid var(--border); overflow: hidden; display: flex; align-items: center; justify-content: center;">
                            <img id="selfieImg" style="max-width: 100%; max-height: 100%; object-fit: contain;" />
                        </div>
                    `;
                } else {
                    selfieImgHtml = `
                        <div style="width: 100%; height: 150px; background: #f8fafc; border-radius: 8px; border: 1px dashed var(--border); display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 8px; color: var(--text-muted);">
                            <svg viewBox="0 0 24 24" width="28" height="28" stroke="currentColor" stroke-width="1.5" fill="none" stroke-linecap="round" stroke-linejoin="round"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"></path><circle cx="12" cy="13" r="4"></circle></svg>
                            <span style="font-size:12px;">No Selfie Photo</span>
                        </div>
                    `;
                }

                // Signature HTML
                const hasSig = details.signature_file || details.signature_local_path;
                let sigImgHtml = '';
                if (hasSig) {
                    sigImgHtml = `
                        <div style="position:relative; width: 100%; height: 150px; background: #f8fafc; border-radius: 8px; border: 1px solid var(--border); overflow: hidden; display: flex; align-items: center; justify-content: center;">
                            <img id="sigImg" style="max-width: 100%; max-height: 100%; object-fit: contain;" />
                        </div>
                    `;
                } else {
                    sigImgHtml = `
                        <div style="width: 100%; height: 150px; background: #f8fafc; border-radius: 8px; border: 1px dashed var(--border); display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 8px; color: var(--text-muted);">
                            <svg viewBox="0 0 24 24" width="28" height="28" stroke="currentColor" stroke-width="1.5" fill="none" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"></path><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path></svg>
                            <span style="font-size:12px;">No Signature Uploaded</span>
                        </div>
                    `;
                }

                let mediaLinkHtml = '';
                if (u.kyc && (u.kyc.status === 'approved' || u.kyc.status === 'completed')) {
                    // KYC is already approved, no link needed
                } else if (u.digio_kyc_url) {
                    mediaLinkHtml = `
                        <div style="background: #f0f7ff; border: 1px solid #004b87; border-radius: 12px; padding: 16px; margin-bottom: 24px; margin-top: 16px;">
                            <span style="font-size: 11px; font-weight: 700; text-transform: uppercase; color: #004b87; letter-spacing: 0.5px; display: block; margin-bottom: 6px;">Digio KYC Gateway Link</span>
                            <p style="font-size: 13px; color: #475569; margin-bottom: 12px;">Copy and send this link to the customer. They will complete Aadhaar verification, selfie capture, and signature capture directly on the Digio secure portal.</p>
                            <div style="display: flex; gap: 8px;">
                                <input type="text" value="${u.digio_kyc_url}" readonly style="flex: 1; padding: 8px 12px; border: 1px solid var(--border); border-radius: 8px; font-size: 13px; background: #ffffff; color: var(--text-dark);" id="mediaUploadLinkInput">
                                <button class="btn" onclick="copyMediaUploadLink()" style="background: var(--primary); color: #ffffff; border-color: var(--primary); font-size: 13px; padding: 8px 16px;">Copy Link</button>
                            </div>
                        </div>
                    `;
                } else {
                    mediaLinkHtml = `
                        <div style="background: #fafafa; border: 1px dashed var(--border); border-radius: 12px; padding: 16px; margin-bottom: 24px; margin-top: 16px; text-align: center;">
                            <p style="font-size: 13px; color: var(--text-muted); margin-bottom: 12px;">No active Digio KYC session found for this user.</p>
                            <button class="btn" id="generateDigioLinkBtn" onclick="generateDigioKycLink(${u.id})" style="background: var(--primary); color: #ffffff; border-color: var(--primary); font-size: 13px; padding: 8px 16px;">Generate Digio KYC Link</button>
                        </div>
                    `;
                }

                const pan = details.pan || {};

                // PAN Card Icon placeholder
                let panCardHtml = '';
                if (pan.id_number) {
                    const panIconHtml = `
                        <div style="width: 80px; height: 100px; display: flex; flex-direction:column; align-items: center; justify-content: center; background: #eff6ff; border-radius: 8px; border: 1px solid #bfdbfe; color: #1d4ed8; flex-shrink: 0;">
                            <svg viewBox="0 0 24 24" width="28" height="28" stroke="currentColor" stroke-width="1.5" fill="none" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="16" rx="2" ry="2"></rect><line x1="7" y1="8" x2="17" y2="8"></line><line x1="7" y1="12" x2="17" y2="12"></line><line x1="7" y1="16" x2="13" y2="16"></line></svg>
                            <span style="font-size: 8px; font-weight:700; margin-top:6px; text-transform:uppercase; letter-spacing:0.5px;">PAN Card</span>
                        </div>
                    `;
                    panCardHtml = `
                        <h3 style="font-size:15px; font-weight:700; margin-top: 24px; margin-bottom: 20px; color: var(--text-dark);">Verified PAN Demographics</h3>
                        <div class="aadhaar-pan-container">
                            ${panIconHtml}
                            <div class="demographics-grid">
                                <div><span class="demo-label">PAN Full Name</span><strong class="demo-value">${pan.name || '—'}</strong></div>
                                <div><span class="demo-label">PAN Card Number</span><strong class="demo-value">${pan.id_number || '—'}</strong></div>
                                <div><span class="demo-label">Father's Name</span><strong class="demo-value">${u.father_name || '—'}</strong></div>
                                <div><span class="demo-label">Date of Birth</span><strong class="demo-value">${formatDobDate(pan.dob)}</strong></div>
                                <div><span class="demo-label">Gender</span><strong class="demo-value">${pan.gender === 'M' ? 'Male' : (pan.gender === 'F' ? 'Female' : (pan.gender || '—'))}</strong></div>
                            </div>
                        </div>
                    `;
                }

                // Extract Aadhaar C/O Father Name
                let fatherNameFromCO = '—';
                if (currentAddr && currentAddr !== '—') {
                    const parts = currentAddr.split(',');
                    if (parts.length > 0 && parts[0].trim().split(' ').length > 1) {
                        fatherNameFromCO = parts[0].trim();
                    }
                }

                detailsHtml = `
                    <div style="margin-top:28px; padding-top:28px; border-top: 1px solid var(--border);">
                        <h3 style="font-size:15px; font-weight:700; margin-bottom: 20px; color: var(--text-dark);">Verified Aadhaar Demographics</h3>
                        
                        <div class="aadhaar-pan-container">
                            <div style="flex-shrink: 0;">
                                ${aadhaarPhotoHtml}
                            </div>
                            <div class="demographics-grid">
                                <div><span class="demo-label">Aadhaar Full Name</span><strong class="demo-value">${aadhaar.name || '—'}</strong></div>
                                <div><span class="demo-label">Aadhaar Number</span><strong class="demo-value">${aadhaar.id_number || '—'}</strong></div>
                                <div><span class="demo-label">Father's Name (C/O)</span><strong class="demo-value">${fatherNameFromCO}</strong></div>
                                <div><span class="demo-label">Date of Birth</span><strong class="demo-value">${aadhaar.dob || '—'}</strong></div>
                                <div><span class="demo-label">Gender</span><strong class="demo-value">${aadhaar.gender === 'M' ? 'Male' : (aadhaar.gender === 'F' ? 'Female' : (aadhaar.gender || '—'))}</strong></div>
                                <div>&nbsp;</div>
                                <div style="grid-column: 1 / -1;"><span class="demo-label">Aadhaar Address</span><strong class="demo-value" style="line-height: 1.4;">${currentAddr}</strong></div>
                            </div>
                        </div>

                        ${panCardHtml}

                        ${mediaLinkHtml}

                        <h3 style="font-size:15px; font-weight:700; margin-bottom: 20px; color: var(--text-dark); margin-top:24px;">Customer Verification Files</h3>
                        
                        <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 24px; margin-bottom: 12px;">
                            <div style="background: #ffffff; border: 1px solid var(--border); padding: 20px; border-radius: 16px; display: flex; flex-direction: column; gap: 14px;">
                                <div style="display:flex; justify-content:space-between; align-items:center;">
                                    <strong style="color: var(--text-dark); font-size:14px;">Live Selfie Photo</strong>
                                    ${!hasSelfie ? `
                                    <label class="btn btn-sync" style="margin:0; padding:6px 12px; cursor:pointer; font-size:12px;">
                                        Upload Selfie
                                        <input type="file" accept="image/*" style="display:none;" onchange="uploadKycFile(${u.id}, 'selfie', this)" />
                                    </label>
                                    ` : ''}
                                </div>
                                ${selfieImgHtml}
                            </div>
                            
                            <div style="background: #ffffff; border: 1px solid var(--border); padding: 20px; border-radius: 16px; display: flex; flex-direction: column; gap: 14px;">
                                <div style="display:flex; justify-content:space-between; align-items:center;">
                                    <strong style="color: var(--text-dark); font-size:14px;">Signature Specimen</strong>
                                    ${!hasSig ? `
                                    <label class="btn btn-sync" style="margin:0; padding:6px 12px; cursor:pointer; font-size:12px;">
                                        Upload Signature
                                        <input type="file" accept="image/*" style="display:none;" onchange="uploadKycFile(${u.id}, 'signature', this)" />
                                    </label>
                                    ` : ''}
                                </div>
                                ${sigImgHtml}
                            </div>
                        </div>
                    </div>
                `;
            }

            kycHtml = `
                <div class="info-card">
                    <div class="card-header">
                        KYC Verification Data
                        <button class="btn btn-sync" onclick="syncKyc(${k.id})" id="syncBtn"><svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" style="margin-right: 4px; vertical-align: middle;"><polyline points="23 4 23 10 17 10"></polyline><polyline points="1 20 1 14 7 14"></polyline><path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"></path></svg>Sync from Digio</button>
                    </div>
                    <div class="grid-list">
                        <div class="grid-item"><span class="grid-label">Status</span><span class="badge ${c}">${displayText}</span></div>
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
            
            let actionsHtml = '';
            if (e.is_signed) {
                actionsHtml = `
                    <div style="display: flex; gap: 8px;">
                        <button class="btn btn-sync" onclick="fetchAdminSignedPdf(${u.id})" style="font-size: 11px; padding: 6px 12px;">
                            <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" style="margin-right: 4px; vertical-align: middle;"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>View PDF
                        </button>
                        <button class="btn" id="resendEmailBtn" onclick="resendAgreementEmail(${u.id})" style="font-size: 11px; padding: 6px 12px; background: var(--primary); color: #ffffff; border-color: var(--primary);">
                            <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" style="margin-right: 4px; vertical-align: middle;"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>Resend Email
                        </button>
                    </div>
                `;
            }

            let logsHtml = '';
            if (e.email_logs && e.email_logs.length > 0) {
                let logItems = e.email_logs.map(log => {
                    const dateStr = new Date(log.sent_at).toLocaleString('en-IN');
                    const badgeClass = log.customer_status === 'success' ? 'approved' : 'failed';
                    const labelText = log.type === 'automatic' ? 'Automatic' : 'Manual Resend';
                    
                    return `
                        <div style="display:flex; justify-content:space-between; align-items:center; padding:10px 0; border-bottom:1px solid #f1f5f9; font-size:12px;">
                            <div>
                                <span style="font-weight:700; color:#334155;">${labelText}</span>
                                <div style="color:var(--text-muted); font-size:11px; margin-top:2px;">Sent to: ${log.customer_email}</div>
                            </div>
                            <div style="text-align:right;">
                                <span class="badge ${badgeClass}" style="font-size:10px; padding:2px 8px;">${log.customer_status.toUpperCase()}</span>
                                <div style="color:var(--text-muted); font-size:10px; margin-top:3px;">${dateStr}</div>
                            </div>
                        </div>
                    `;
                }).join('');

                logsHtml = `
                    <div style="padding:16px 24px; border-top:1px solid var(--border); background:#fafafa;">
                        <div style="font-size:11px; font-weight:700; text-transform:uppercase; color:var(--text-muted); letter-spacing:0.5px; margin-bottom:12px;">Email Dispatch History</div>
                        <div style="display:flex; flex-direction:column; gap:2px;">
                            ${logItems}
                        </div>
                    </div>
                `;
            }

            actionsHtml = `<div class="card-header-actions">`;
            if (e.pdf_url) {
                actionsHtml += `<a href="${e.pdf_url}" target="_blank" class="btn btn-sync"><svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2.5" fill="none" stroke-linecap="round" stroke-linejoin="round" style="margin-right:6px;"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg> <span class="btn-text">View PDF</span></a>`;
            }
            actionsHtml += `<button onclick="resendEsignEmail()" class="btn" style="background:var(--primary); color:#ffffff; border-color:var(--primary);"><svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2.5" fill="none" stroke-linecap="round" stroke-linejoin="round" style="margin-right:6px;"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg> <span class="btn-text">Resend Email</span></button>`;
            actionsHtml += `</div>`;

            esignHtml = `
            <div class="info-card">
                <div class="card-header">
                    <span class="card-title-text">E-Sign Agreement Data</span>
                    ${actionsHtml}
                </div>
                <div class="grid-list">
                    <div class="grid-item"><span class="grid-label">Status</span><span class="badge ${c_badge}">${e_status.toUpperCase()}</span></div>
                    <div class="grid-item"><span class="grid-label">Digio Document ID</span><span class="grid-value">${e.digio_document_id || '—'}</span></div>
                    <div class="grid-item"><span class="grid-label">IP Address</span><span class="grid-value">${e.ip_address || '—'}</span></div>
                    <div class="grid-item"><span class="grid-label">Signed At</span><span class="grid-value">${e.signed_at ? new Date(e.signed_at).toLocaleString('en-IN') : '—'}</span></div>
                </div>
                ${logsHtml}
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
                    <!-- Basic Info -->
                    <div class="info-card">
                        <div class="card-header" style="display:flex; justify-content:space-between; align-items:center;">
                            Basic Information
                            <button class="btn btn-sync" onclick="openEditModal()" style="font-size:12px; padding:6px 12px;"><svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" style="margin-right: 4px; vertical-align: middle;"><path d="M12 20h9"></path><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path></svg>Edit Details</button>
                        </div>
                        <div class="grid-list">
                            <div class="grid-item"><span class="grid-label">User ID</span><span class="grid-value">#${u.id}</span></div>
                            <div class="grid-item"><span class="grid-label">Role</span><span class="grid-value" style="text-transform:uppercase;">${u.role}</span></div>
                            <div class="grid-item"><span class="grid-label">Full Name</span><span class="grid-value">${displayName}</span></div>
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
        `;

        // Asynchronously load secure images using Authorization Bearer token
        if (document.getElementById('selfieImg')) {
            loadKycImage(u.id, 'selfie', 'selfieImg');
        }
        if (document.getElementById('sigImg')) {
            loadKycImage(u.id, 'signature', 'sigImg');
        }
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

    async function resendAgreementEmail(uid) {
        const btn = document.getElementById('resendEmailBtn');
        const originalText = btn.innerHTML;
        btn.innerHTML = 'Resending...';
        btn.disabled = true;

        try {
            const res = await fetch(`/api/admin/users/${uid}/resend-agreement-email`, {
                method: 'POST',
                headers: {
                    'Authorization': `Bearer ${adminToken}`,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            });
            const data = await res.json();
            
            if (res.ok && data.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Email Sent',
                    text: 'Signed agreement copy has been resent successfully to the customer and admin.',
                    confirmButtonColor: 'var(--primary)'
                });
                loadUserDetails();
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Failed to Resend',
                    text: data.message || 'Unknown error occurred.',
                    confirmButtonColor: 'var(--primary)'
                });
            }
        } catch (e) {
            console.error(e);
            Swal.fire({
                icon: 'error',
                title: 'Network Error',
                text: 'Could not connect to the server to resend the emails.',
                confirmButtonColor: 'var(--primary)'
            });
        } finally {
            btn.innerHTML = originalText;
            btn.disabled = false;
        }
    }

    async function downloadConsolidatedPdf(uid) {
        const btn = document.getElementById('consolidatedDownloadBtn');
        const originalText = btn.innerHTML;
        btn.innerHTML = 'Downloading...';
        btn.style.pointerEvents = 'none';

        try {
            const res = await fetch(`/api/admin/users/${uid}/download-consolidated`, {
                headers: { 'Authorization': `Bearer ${adminToken}` }
            });
            if (!res.ok) throw new Error('Failed to download consolidated PDF');
            const blob = await res.blob();
            const blobUrl = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = blobUrl;
            
            const aadhaarName = (currentUserObj && currentUserObj.kyc && currentUserObj.kyc.customer_name) ? currentUserObj.kyc.customer_name : (currentUserObj ? currentUserObj.name : 'user');
            const cleanedName = aadhaarName.toLowerCase().replace(/[^a-z0-9]+/g, '_').replace(/^_+|_+$/g, '');
            let signDateStr = '';
            if (currentUserObj && currentUserObj.esign_agreement && currentUserObj.esign_agreement.signed_at) {
                const sDate = new Date(currentUserObj.esign_agreement.signed_at);
                const dd = String(sDate.getDate()).padStart(2, '0');
                const mm = String(sDate.getMonth() + 1).padStart(2, '0');
                const yyyy = sDate.getFullYear();
                signDateStr = `${dd}-${mm}-${yyyy}`;
            } else {
                const today = new Date();
                const dd = String(today.getDate()).padStart(2, '0');
                const mm = String(today.getMonth() + 1).padStart(2, '0');
                const yyyy = today.getFullYear();
                signDateStr = `${dd}-${mm}-${yyyy}`;
            }

            a.download = `${cleanedName}_kyc_agreement_${signDateStr}.pdf`;
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
        } catch (e) {
            console.error(e);
            alert('Failed to download the consolidated PDF.');
        } finally {
            btn.innerHTML = originalText;
            btn.style.pointerEvents = 'auto';
        }
    }

    async function uploadKycFile(userId, type, input) {
        if (!input.files || !input.files[0]) return;
        const file = input.files[0];
        
        const formData = new FormData();
        formData.append('file', file);
        
        // Show loading state
        const label = input.parentElement;
        const originalText = label.textContent;
        label.textContent = 'Uploading...';
        
        try {
            const res = await fetch(`/api/admin/users/${userId}/media/${type}`, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Authorization': 'Bearer ' + adminToken
                },
                body: formData
            });
            const data = await res.json();
            if (data.success) {
                alert(`${type.charAt(0).toUpperCase() + type.slice(1)} uploaded successfully!`);
                loadUserDetails();
            } else {
                alert(`Upload failed: ${data.message}`);
                loadUserDetails();
            }
        } catch (err) {
            alert('Network error during upload.');
            loadUserDetails();
        }
    }

    async function loadKycImage(uid, type, imgElementId) {
        try {
            const res = await fetch(`/api/admin/users/${uid}/media/${type}`, {
                headers: { Authorization: `Bearer ${adminToken}` }
            });
            if (res.ok) {
                const blob = await res.blob();
                const objectUrl = URL.createObjectURL(blob);
                const img = document.getElementById(imgElementId);
                if (img) img.src = objectUrl;
            }
        } catch (e) {
            console.error('Failed to load secure image', e);
        }
    }

    function openEditModal() {
        if (!currentUserObj) return;
        const modal = document.getElementById('editModal');
        const form = document.getElementById('editForm');
        
        // Populate inputs with current values
        form.pan_card.value = currentUserObj.pan_card || '';
        form.pan_card_name.value = currentUserObj.pan_card_name || '';
        form.father_name.value = currentUserObj.father_name || '';
        form.dob.value = formatDobDate(currentUserObj.dob);
        form.marital_status.value = currentUserObj.marital_status || '';
        form.address.value = currentUserObj.address || '';
        form.city.value = currentUserObj.city || '';
        form.state.value = currentUserObj.state || '';
        form.pincode.value = currentUserObj.pincode || '';

        // Display autofill helper if Aadhaar data exists in kyc_details
        const hasAadhaar = currentUserObj.kyc && currentUserObj.kyc.kyc_details && currentUserObj.kyc.kyc_details.aadhaar;
        document.getElementById('autofillContainer').style.display = hasAadhaar ? 'flex' : 'none';

        modal.style.display = 'flex';
    }

    function closeEditModal() {
        document.getElementById('editModal').style.display = 'none';
    }

    function autofillFromAadhaar() {
        if (!currentUserObj || !currentUserObj.kyc || !currentUserObj.kyc.kyc_details || !currentUserObj.kyc.kyc_details.aadhaar) return;
        const aadhaar = currentUserObj.kyc.kyc_details.aadhaar;
        const form = document.getElementById('editForm');

        form.dob.value = aadhaar.dob || form.dob.value;
        form.address.value = aadhaar.current_address || form.address.value;

        if (aadhaar.current_address_details) {
            form.city.value = aadhaar.current_address_details.district_or_city || form.city.value;
            form.state.value = aadhaar.current_address_details.state || form.state.value;
            form.pincode.value = aadhaar.current_address_details.pincode || form.pincode.value;
        }

        // Suggest Father's Name from care of line (first comma-separated segment)
        if (aadhaar.current_address && !form.father_name.value) {
            const parts = aadhaar.current_address.split(',');
            if (parts.length > 0 && parts[0].split(' ').length > 1) {
                form.father_name.value = parts[0].trim();
            }
        }
    }

    async function saveUserDetails(e) {
        e.preventDefault();
        const form = document.getElementById('editForm');
        
        const payload = {
            pan_card: form.pan_card.value,
            pan_card_name: form.pan_card_name.value,
            father_name: form.father_name.value,
            dob: form.dob.value,
            marital_status: form.marital_status.value,
            address: form.address.value,
            city: form.city.value,
            state: form.state.value,
            pincode: form.pincode.value,
        };

        try {
            const res = await fetch(`/api/admin/users/${userId}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Authorization': 'Bearer ' + adminToken
                },
                body: JSON.stringify(payload)
            });
            const data = await res.json();
            if (data.success) {
                alert('Customer registration details updated successfully!');
                closeEditModal();
                loadUserDetails();
            } else {
                alert('Update failed: ' + data.message);
            }
        } catch (err) {
            alert('Network error while saving details.');
        }
    }

    function formatDobDate(dateStr) {
        if (!dateStr) return '—';
        try {
            if (dateStr.includes('/')) {
                return dateStr;
            }
            const date = new Date(dateStr);
            if (isNaN(date.getTime())) return dateStr;
            
            const day = String(date.getDate()).padStart(2, '0');
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const year = date.getFullYear();
            return `${day}/${month}/${year}`;
        } catch (e) {
            return dateStr;
        }
    }

    function copyMediaUploadLink() {
        const input = document.getElementById('mediaUploadLinkInput');
        if (!input) return;
        input.select();
        input.setSelectionRange(0, 99999);
        navigator.clipboard.writeText(input.value).then(() => {
            Swal.fire({
                icon: 'success',
                title: 'Link Copied',
                text: 'Media upload URL has been copied to clipboard.',
                confirmButtonColor: 'var(--primary)'
            });
        }).catch(err => {
            console.error('Failed to copy: ', err);
            alert('Failed to copy link. Please manually copy it.');
        });
    }

    async function generateDigioKycLink(uid) {
        const btn = document.getElementById('generateDigioLinkBtn');
        const originalText = btn.innerHTML;
        btn.innerHTML = 'Generating...';
        btn.disabled = true;

        try {
            const res = await fetch(`/api/admin/users/${uid}/generate-digio-kyc-link`, {
                method: 'POST',
                headers: {
                    'Authorization': `Bearer ${adminToken}`,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });
            const data = await res.json();
            
            if (res.ok && data.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Link Generated',
                    text: 'Digio KYC gateway link generated successfully! Refreshing details...',
                    confirmButtonColor: 'var(--primary)'
                });
                loadUserDetails();
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Generation Failed',
                    text: data.message || 'Unknown error occurred.',
                    confirmButtonColor: 'var(--primary)'
                });
            }
        } catch (e) {
            console.error(e);
            Swal.fire({
                icon: 'error',
                title: 'Network Error',
                text: 'Could not connect to the server to generate link.',
                confirmButtonColor: 'var(--primary)'
            });
        } finally {
            btn.innerHTML = originalText;
            btn.disabled = false;
        }
    }

    // Init
    loadUserDetails();
</script>
@endsection

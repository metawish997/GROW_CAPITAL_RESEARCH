@extends('layouts.app')

@section('content')
<div class="p-6 space-y-6">
    <!-- HEADER -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-xl font-bold text-slate-800">NDML KRA Configuration</h1>
            <p class="text-sm text-slate-500 mt-1">Configure SOAP API web services, SFTP credentials, and auto-upload workflows</p>
        </div>
        
        <div class="flex gap-2">
            <a href="{{ route('admin.api.dashboard') }}" style="background: #f1f5f9 !important; color: #334155 !important;" class="px-4 py-2 text-slate-700 border rounded text-sm font-medium hover:bg-slate-200 transition-all">
                <i class="fas fa-arrow-left mr-2"></i>Back to API Dashboard
            </a>
            <button type="button" onclick="testSoapCredentials()" id="testSoapBtn" style="background: #2563eb !important; color: #ffffff !important;" class="px-4 py-2 text-white rounded text-sm font-medium hover:bg-blue-700 shadow-sm transition-all">
                <i class="fas fa-vial mr-2"></i>Test SOAP Credentials
            </button>
            <form action="{{ route('admin.api.kra-settings.test-upload') }}" method="POST" class="inline" onsubmit="return confirm('This will generate a test customer with real-looking details, attach mock selfie and signature images, and trigger a live SOAP XML registration and SFTP PDF upload to the NDML KRA UAT server. Proceed?')">
                @csrf
                <button type="submit" style="background: #a855f7 !important; color: #ffffff !important;" class="px-4 py-2 text-white rounded text-sm font-medium hover:bg-purple-700 shadow-sm transition-all">
                    <i class="fas fa-file-upload mr-2"></i>Trigger UAT Test Upload
                </button>
            </form>
        </div>
    </div>

    <!-- NOTIFICATIONS -->
    @if(session('success'))
        <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-lg flex items-start gap-3">
            <i class="fas fa-check-circle mt-0.5 text-emerald-600"></i>
            <div>
                <p class="text-sm font-bold">Success</p>
                <p class="text-xs mt-0.5">{{ session('success') }}</p>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="p-4 bg-rose-50 border border-rose-200 text-rose-800 rounded-lg flex items-start gap-3">
            <i class="fas fa-exclamation-circle mt-0.5 text-rose-600"></i>
            <div>
                <p class="text-sm font-bold">Error</p>
                <p class="text-xs mt-0.5">{{ session('error') }}</p>
            </div>
        </div>
    @endif

    <!-- LIVE CONNECTION TEST MODAL/ALERT -->
    <div id="testResultContainer" class="hidden p-4 rounded-lg flex items-start gap-3 transition-all duration-300">
        <i id="testResultIcon" class="fas mt-0.5"></i>
        <div class="flex-1">
            <p id="testResultTitle" class="text-sm font-bold"></p>
            <p id="testResultDesc" class="text-xs mt-0.5"></p>
            <div id="testResultDetails" class="hidden mt-2 p-2 bg-slate-950 text-slate-300 font-mono text-[10px] rounded border border-slate-800"></div>
        </div>
        <button onclick="document.getElementById('testResultContainer').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 text-xs">
            <i class="fas fa-times"></i>
        </button>
    </div>

    <!-- CONFIGURATION FORM -->
    <form action="{{ route('admin.api.kra-settings.update') }}" method="POST" class="space-y-6">
        @csrf
        
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- LEFT PANEL: SOAP SERVICES -->
            <div class="lg:col-span-2 space-y-6">
                <!-- SOAP API CONFIG CARD -->
                <div class="bg-white rounded-lg shadow-sm border p-6 space-y-4">
                    <div class="flex items-center gap-3 border-b pb-3">
                        <div class="p-2 bg-blue-50 text-blue-600 rounded-lg">
                            <i class="fas fa-cogs"></i>
                        </div>
                        <div>
                            <h2 class="text-base font-bold text-slate-800">SOAP Webservices Credentials</h2>
                            <p class="text-xs text-slate-500">Configure credentials for NDML OKRA & PAN services</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- User ID -->
                        <div class="space-y-1">
                            <label class="text-xs font-bold text-slate-600">NDML User ID / POS Code</label>
                            <input type="text" name="ndml_user_id" value="{{ old('ndml_user_id', $settings->ndml_user_id) }}" placeholder="e.g. USER1234" class="w-full text-sm border rounded px-3 py-2 focus:ring-1 focus:ring-blue-500 focus:outline-none">
                        </div>

                        <!-- BP ID / MI Code -->
                        <div class="space-y-1">
                            <label class="text-xs font-bold text-slate-600">MI Code / Okra Code (BP ID)</label>
                            <input type="text" name="ndml_bp_id" value="{{ old('ndml_bp_id', $settings->ndml_bp_id) }}" placeholder="e.g. A1249" class="w-full text-sm border rounded px-3 py-2 focus:ring-1 focus:ring-blue-500 focus:outline-none">
                        </div>

                        <!-- Password -->
                        <div class="space-y-1">
                            <label class="text-xs font-bold text-slate-600">Portal Login Password</label>
                            <input type="password" name="ndml_password" value="{{ old('ndml_password', $settings->ndml_password) }}" placeholder="••••••••" class="w-full text-sm border rounded px-3 py-2 focus:ring-1 focus:ring-blue-500 focus:outline-none">
                        </div>

                        <!-- Passkey -->
                        <div class="space-y-1">
                            <label class="text-xs font-bold text-slate-600">Passkey (Registration hashing - Max 16 chars)</label>
                            <input type="password" name="ndml_passkey" value="{{ old('ndml_passkey', $settings->ndml_passkey) }}" placeholder="e.g. MySecretPasskey" class="w-full text-sm border rounded px-3 py-2 focus:ring-1 focus:ring-blue-500 focus:outline-none">
                        </div>

                        <!-- Encryption Key -->
                        <div class="space-y-1 md:col-span-2">
                            <label class="text-xs font-bold text-slate-600">Encryption Key (Inquiry passcode - Max 8 chars)</label>
                            <input type="password" name="ndml_encryption_key" value="{{ old('ndml_encryption_key', $settings->ndml_encryption_key) }}" placeholder="e.g. EncKey8" class="w-full text-sm border rounded px-3 py-2 focus:ring-1 focus:ring-blue-500 focus:outline-none">
                        </div>
                    </div>
                </div>

                <!-- SFTP SERVER CONFIG CARD -->
                <div class="bg-white rounded-lg shadow-sm border p-6 space-y-4">
                    <div class="flex items-center gap-3 border-b pb-3">
                        <div class="p-2 bg-indigo-50 text-indigo-600 rounded-lg">
                            <i class="fas fa-folder-open"></i>
                        </div>
                        <div>
                            <h2 class="text-base font-bold text-slate-800">SFTP Document Upload Credentials</h2>
                            <p class="text-xs text-slate-500">Configure parameters to securely upload POI/POA customer PDFs</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <!-- Host -->
                        <div class="md:col-span-2 space-y-1">
                            <label class="text-xs font-bold text-slate-600">SFTP Server Host</label>
                            <input type="text" name="sftp_host" value="{{ old('sftp_host', $settings->sftp_host) }}" placeholder="e.g. sftp.kra.ndml.in" class="w-full text-sm border rounded px-3 py-2 focus:ring-1 focus:ring-indigo-500 focus:outline-none">
                        </div>

                        <!-- Port -->
                        <div class="space-y-1">
                            <label class="text-xs font-bold text-slate-600">SFTP Port</label>
                            <input type="number" name="sftp_port" value="{{ old('sftp_port', $settings->sftp_port) }}" class="w-full text-sm border rounded px-3 py-2 focus:ring-1 focus:ring-indigo-500 focus:outline-none">
                        </div>

                        <!-- Username -->
                        <div class="space-y-1">
                            <label class="text-xs font-bold text-slate-600">SFTP Username</label>
                            <input type="text" name="sftp_username" value="{{ old('sftp_username', $settings->sftp_username) }}" placeholder="e.g. sftp_user" class="w-full text-sm border rounded px-3 py-2 focus:ring-1 focus:ring-indigo-500 focus:outline-none">
                        </div>

                        <!-- Password -->
                        <div class="md:col-span-2 space-y-1">
                            <label class="text-xs font-bold text-slate-600">SFTP Password</label>
                            <input type="password" name="sftp_password" value="{{ old('sftp_password', $settings->sftp_password) }}" placeholder="••••••••" class="w-full text-sm border rounded px-3 py-2 focus:ring-1 focus:ring-indigo-500 focus:outline-none">
                        </div>
                    </div>
                </div>
            </div>

            <!-- RIGHT PANEL: SYSTEM MODES & AUTOMATION -->
            <div class="space-y-6">
                <!-- WORKFLOW CONTROL -->
                <div class="bg-white rounded-lg shadow-sm border p-6 space-y-5">
                    <div class="flex items-center gap-3 border-b pb-3">
                        <div class="p-2 bg-purple-50 text-purple-600 rounded-lg">
                            <i class="fas fa-toggle-on"></i>
                        </div>
                        <div>
                            <h2 class="text-base font-bold text-slate-800">Workflow & Triggers</h2>
                            <p class="text-xs text-slate-500">Configure environments and syncing automation</p>
                        </div>
                    </div>

                    <!-- Environment Mode -->
                    <div class="space-y-2">
                        <label class="text-xs font-bold text-slate-700 block">KRA Environment</label>
                        <div class="grid grid-cols-2 gap-2">
                            <label class="border rounded-lg p-3 flex items-center gap-2 cursor-pointer transition-all hover:bg-slate-50" id="labelUat">
                                <input type="radio" name="ndml_uat_mode" value="1" {{ old('ndml_uat_mode', $settings->ndml_uat_mode) ? 'checked' : '' }} onchange="toggleEnvStyle(true)" class="text-blue-600 focus:ring-blue-500">
                                <div>
                                    <span class="text-xs font-bold text-slate-800 block">UAT / Pilot</span>
                                    <span class="text-[9px] text-slate-400 font-mono">Sandbox API</span>
                                </div>
                            </label>

                            <label class="border rounded-lg p-3 flex items-center gap-2 cursor-pointer transition-all hover:bg-slate-50" id="labelProd">
                                <input type="radio" name="ndml_uat_mode" value="0" {{ !old('ndml_uat_mode', $settings->ndml_uat_mode) ? 'checked' : '' }} onchange="toggleEnvStyle(false)" class="text-indigo-600 focus:ring-indigo-500">
                                <div>
                                    <span class="text-xs font-bold text-slate-800 block">Production</span>
                                    <span class="text-[9px] text-rose-500 font-bold uppercase tracking-wider">Live KRA</span>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Auto-upload -->
                    <div class="space-y-2 pt-2">
                        <label class="text-xs font-bold text-slate-700 block">Auto-Syncing System</label>
                        <div class="flex items-start gap-3 p-3 bg-slate-50 border rounded-lg">
                            <input type="hidden" name="auto_upload_on_approval" value="0">
                            <input type="checkbox" name="auto_upload_on_approval" value="1" {{ old('auto_upload_on_approval', $settings->auto_upload_on_approval) ? 'checked' : '' }} class="mt-1 rounded text-blue-600 focus:ring-blue-500">
                            <div>
                                <span class="text-xs font-bold text-slate-800 block">Auto-Upload on KYC Approval</span>
                                <span class="text-[10px] text-slate-500 block mt-0.5">Automatically trigger NDML API XML register and upload verified POI/POA PDFs to KRA SFTP when a customer's local KYC is approved.</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- SAVE CARD -->
                <div class="bg-white rounded-lg shadow-sm border p-6 flex flex-col gap-3">
                    <button type="submit" style="background: #10b981 !important; color: #ffffff !important;" class="w-full py-3 text-white font-bold rounded-lg text-sm hover:bg-emerald-600 shadow transition-all">
                        <i class="fas fa-save mr-2"></i>Save Configuration
                    </button>
                    <p class="text-[10px] text-slate-400 text-center">Changes will take effect immediately upon saving.</p>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- LOADING OVERLAY -->
<div id="loadingOverlay" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 flex items-center space-x-3 shadow-xl">
        <i class="fas fa-spinner fa-spin text-blue-600 text-lg"></i>
        <span class="text-sm font-semibold text-slate-800">Verifying NDML SOAP endpoints...</span>
    </div>
</div>

<script>
function toggleEnvStyle(isUat) {
    const uat = document.getElementById('labelUat');
    const prod = document.getElementById('labelProd');
    if (isUat) {
        uat.className = 'border-2 border-blue-500 bg-blue-50/30 rounded-lg p-3 flex items-center gap-2 cursor-pointer transition-all';
        prod.className = 'border border-slate-200 rounded-lg p-3 flex items-center gap-2 cursor-pointer transition-all hover:bg-slate-50';
    } else {
        prod.className = 'border-2 border-indigo-500 bg-indigo-50/30 rounded-lg p-3 flex items-center gap-2 cursor-pointer transition-all';
        uat.className = 'border border-slate-200 rounded-lg p-3 flex items-center gap-2 cursor-pointer transition-all hover:bg-slate-50';
    }
}

async function testSoapCredentials() {
    const overlay = document.getElementById('loadingOverlay');
    const container = document.getElementById('testResultContainer');
    const icon = document.getElementById('testResultIcon');
    const title = document.getElementById('testResultTitle');
    const desc = document.getElementById('testResultDesc');
    const details = document.getElementById('testResultDetails');

    overlay.classList.remove('hidden');
    container.classList.add('hidden');

    try {
        const response = await fetch('{{ route("admin.api.kra-settings.test-soap") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        });

        const result = await response.json();
        overlay.classList.add('hidden');
        container.classList.remove('hidden');

        if (response.ok && result.status === 'success') {
            container.className = 'p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-lg flex items-start gap-3';
            icon.className = 'fas fa-check-circle mt-0.5 text-emerald-600 text-base';
            title.textContent = 'Connection Successful';
            desc.textContent = result.message;
            details.classList.remove('hidden');
            details.innerHTML = `Registration password generated: ${result.data.registration_hash}<br>Inquiry passcode generated: ${result.data.inquiry_hash}`;
        } else {
            throw new Error(result.message || 'Verification failed');
        }
    } catch (error) {
        overlay.classList.add('hidden');
        container.classList.remove('hidden');
        
        container.className = 'p-4 bg-rose-50 border border-rose-200 text-rose-800 rounded-lg flex items-start gap-3';
        icon.className = 'fas fa-exclamation-circle mt-0.5 text-rose-600 text-base';
        title.textContent = 'Connection Failed';
        desc.textContent = error.message;
        details.classList.add('hidden');
    }
}

// Set initial radio styling on load
document.addEventListener('DOMContentLoaded', () => {
    const isUat = document.querySelector('input[name="ndml_uat_mode"]:checked').value === "1";
    toggleEnvStyle(isUat);
});
</script>
@endsection

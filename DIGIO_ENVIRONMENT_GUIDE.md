# Digio Environment Switch Guide

This document explains how to switch the application between the **Sandbox (Testing)** environment and the **Production (Live)** environment.

---

## Current Status: Sandbox (Testing)
The application has been successfully configured to use the Digio Sandbox environment.
* **Database Base URL:** `https://ext.digio.in:444`

> [!NOTE]
> Please ensure you enter your **Sandbox Client ID** and **Sandbox Client Secret** in the Admin Settings panel. Production credentials will fail on the Sandbox URL.

---

## Switching to Production (Live)
When you are ready to go live, complete the following steps to switch back to the production environment:

### Step 1: Update Settings in Admin Panel
1. Log into your Admin Panel at `/admin/login`.
2. Go to **API Settings** in the sidebar.
3. Update the following fields:
   * **Base URL:** `https://api.digio.in`
   * **Digio Client ID:** *(Your Live Production Client ID)*
   * **Digio Client Secret:** *(Your Live Production Client Secret)*
4. Click **Save Settings**.

Alternatively, you can run this database one-liner command in your terminal to update the URL:
```bash
php artisan tinker --execute="App\Models\AppSetting::updateOrCreate(['group' => 'digio', 'key' => 'base_url'], ['value' => 'https://api.digio.in']);"
```

---

## Key Differences

| Feature | Sandbox Environment (Testing) | Production Environment (Live) |
| :--- | :--- | :--- |
| **API Base URL** | `https://ext.digio.in:444` | `https://api.digio.in` |
| **Digio Web SDK** | `https://ext.digio.in` | `https://app.digio.in` |
| **Billing / Cost** | Free (No credits debited) | Debits credits per signature/KYC request |
| **Aadhaar/OTP** | Dummy Aadhaar & Mock OTP values | Real Aadhaar authentication with OTP |
| **Enterprise Portal** | `https://ext-enterprise.digio.in` | `https://enterprise.digio.in` |

---

## Webhook Whitelisting (Optional)
If your production server is protected by a firewall, make sure to whitelist Digio's IPs to receive callbacks (like document status changes):
* **Sandbox IP:** `35.154.20.28`
* **Production IPs:** Contact your Digio Account Manager for their current production whitelisting IPs.

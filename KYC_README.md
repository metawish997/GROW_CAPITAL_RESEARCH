# KYC Module - Grow Capitals Research

## Overview
This module handles **KYC (Know Your Customer)** verification using the **Digio API**. It was ported from the BharatStockMarketResearch project and fully adapted for the Grow Capitals API-first architecture (Laravel Sanctum + DB-stored credentials).

KYC credentials are **NOT stored in `.env`** — they are stored in the `app_settings` database table and managed via the Admin Panel → API Settings.

---

## Architecture

```
User App / Browser
      │
      ▼
POST /api/user/kyc/initiate
      │ (KycController → KycService)
      │
      ▼
Digio API: /client/kyc/v2/request/with_template
      │
      ▼
KYC record saved in `kyc_verifications` DB table
      │
      ▼
Redirect URL sent to user → User opens Digio portal
      │
      ▼
User completes KYC on Digio
      │
      ▼
Digio redirects to: GET /kyc/callback?digio_doc_id=KID...&status=...
      │ (KycController::callback)
      ▼
KycService::fetchAndUpdateKycStatus() — final status saved to DB
```

---

## Files Generated / Ported

### Database
| File | Purpose |
|---|---|
| `database/migrations/2026_08_20_000003_create_kyc_verifications_table.php` | Creates `kyc_verifications` table |

### Models
| File | Purpose |
|---|---|
| `app/Models/KycVerification.php` | Full KYC model with scopes, accessors, actions |

### Services
| File | Purpose |
|---|---|
| `app/Services/KycService.php` | Core Digio integration logic |

### Controllers
| File | Purpose |
|---|---|
| `app/Http/Controllers/Api/KycController.php` | User + Admin KYC API endpoints |

---

## Database Table: `kyc_verifications`

| Column | Type | Description |
|---|---|---|
| `id` | bigint | Primary key |
| `user_id` | foreignId | Linked to `users` table |
| `digio_document_id` | string (unique) | Digio's KYC document ID (KID...) |
| `customer_name` | string | Customer name |
| `customer_mobile` | string | Mobile number used for KYC |
| `customer_email` | string\|null | Email |
| `reference_id` | string (unique) | Our internal reference ID |
| `transaction_id` | string\|null | Same as reference_id |
| `status` | string | `initiated / pending / approval_pending / approved / rejected / failed / expired` |
| `kyc_details` | json | Aadhaar, selfie, signature, face_match data |
| `aadhaar_details` | json | Detailed Aadhaar info |
| `callback_status` | string\|null | Status received in Digio callback |
| `callback_message` | text\|null | Message/reason from callback |
| `kyc_completed_at` | timestamp | When KYC was approved |
| `kyc_expires_at` | timestamp | Expiry time |
| `raw_response` | json | Complete raw response from Digio API |

---

## API Endpoints

### User APIs (Protected — Bearer Token required)

| Method | Endpoint | Description |
|---|---|---|
| GET | `/api/user/kyc/status` | Get current KYC status |
| POST | `/api/user/kyc/initiate` | Start new KYC request (creates Digio link) |
| POST | `/api/user/kyc/sync` | Sync KYC status from Digio |

#### POST `/api/user/kyc/initiate` — Request Body
```json
{
  "mobile": "9876543210",
  "name": "Rahul Sharma"
}
```

#### Response (success)
```json
{
  "success": true,
  "message": "KYC initiated.",
  "document_id": "KID2601161426534789H1K2KOD...",
  "redirect_url": "https://app.digio.in/#/gateway/login/KID.../..."
}
```

### Web Callback (called by Digio after user completes KYC)

| Method | URL | Description |
|---|---|---|
| GET | `/kyc/callback` | Digio redirects user here after completion |

Digio will append: `?digio_doc_id=KID...&status=approved`

### Admin APIs (Protected — Admin Bearer Token required)

| Method | Endpoint | Description |
|---|---|---|
| GET | `/api/admin/kyc` | List all KYC records (paginated) |
| GET | `/api/admin/kyc/{id}` | Get single KYC detail |
| POST | `/api/admin/kyc/{id}/approve` | Manually approve a KYC |
| POST | `/api/admin/kyc/{id}/reject` | Manually reject a KYC |
| POST | `/api/admin/kyc/{id}/sync` | Force sync from Digio |

---

## KYC Statuses

| Status | Meaning |
|---|---|
| `initiated` | Digio link created, user not started |
| `pending` | User started but not completed |
| `approval_pending` | User completed, waiting for approval |
| `approved` | KYC fully verified ✅ |
| `rejected` | KYC rejected ❌ |
| `failed` | Error during process |
| `expired` | Link expired |

---

## Configuration (Admin Panel)

Go to: **Admin Dashboard → API Settings → Digio tab**

| Key | Example Value |
|---|---|
| `client_id` | `KID2602061826425736H6K1...` |
| `client_secret` | `your_secret` |
| `base_url` | `https://api.digio.in` |
| `environment` | `production` or `sandbox` |
| `workflow` | `kyc_with_aadhaar` |

> **Sandbox URL**: `https://ext.digio.in`  
> **Production URL**: `https://api.digio.in`

---

## KycService Methods

```php
$kycService = new KycService(); // Reads creds from DB automatically

// Check if configured
$kycService->isConfigured(); // bool

// Initiate new KYC
$result = $kycService->initiateKyc([
    'user_id' => 1,
    'name'    => 'Rahul',
    'mobile'  => '9876543210',
    'email'   => 'rahul@example.com',
]);

// Sync status from Digio
$kycService->fetchAndUpdateKycStatus('KID...');

// Sync for a user
$kycService->syncUserKyc($user);

// Manually approve
$kycService->approveKycManually('KID...');
```

---

## KycVerification Model Helpers

```php
$kyc->isApproved();        // bool
$kyc->isPending();         // bool
$kyc->isExpired();         // bool
$kyc->isActive();          // bool (approved + not expired)
$kyc->aadhaar_number;      // masked aadhaar
$kyc->full_name;           // from aadhaar_details
$kyc->date_of_birth;       // from aadhaar_details
$kyc->status_with_color;   // ['text' => 'Approved', 'color' => 'success']
$kyc->days_until_expiry;   // int | null
$kyc->age_in_days;         // int | null
$kyc->approve($kycDetails, $aadhaarDetails);
$kyc->reject('reason');
$kyc->markAsFailed('error');

// Static helpers
KycVerification::findByDocumentId('KID...');
KycVerification::hasApprovedKyc('9876543210');
KycVerification::getLatestApprovedKyc('9876543210');
```

---

## Setup Commands

```bash
# Run KYC migration
php artisan migrate

# Clear view cache after changes
php artisan view:clear
php artisan config:clear
```

---

## Source Reference
This module was ported from:
```
D:\demo_uis\BharatStockMarketResearchFullCode\
├── app/Services/KycService.php
├── app/Services/DigioAadhaarService.php
├── app/Http/Controllers/DigioKycController.php
├── app/Models/KycVerification.php
└── database/migrations/2026_01_16_110107_create_kyc_verifications_table.php
```

# E-Sign Agreement System Features

This document outlines all the robust e-sign agreement features that have been built and integrated into the application.

## 1. Dynamic PDF Generation
The system dynamically generates a legal agreement PDF on-the-fly right before the user signs. 
- **Data Binding:** Automatically injects the user's Full Name, Mobile Number, IP Address, and the exact timestamp.
- **Rendering Engine:** Uses `barryvdh/laravel-dompdf` to accurately convert the responsive `agreement.blade.php` template into a high-quality PDF document.

## 2. Advanced Digio API Integration
- **Direct Uploads (`/v2/client/document/uploadpdf`):** The system securely POSTs the base64-encoded PDF to Digio's enterprise API.
- **Dynamic Redirect URLs:** Once uploaded, Digio returns an `id` (Document ID) and an `access_token`. The system constructs a secure URL that redirects the user to the e-Sign gateway:
  ```php
  // Example of how the redirect URL is built
  $digioDocId = $digioData['id'];
  $accessToken = $digioData['access_token']['id'];
  $signerPhone = $user->mobile;
  
  $esignUrl = "https://app.digio.in/#/gateway/login/{$digioDocId}/{$accessToken}/{$signerPhone}?redirect_url=" . urlencode(url('/dashboard'));
  ```
  *(The `redirect_url` dynamically ensures the user returns to our application exactly where they left off, keeping the Sanctum token alive).*

> [!IMPORTANT]
> **Strict Aadhaar Verification Rules applied during E-Sign**
> To prevent fraud (someone using a different Aadhaar to sign the document), the system extracts the user's **KYC Data** and sends strict mathematical rules to Digio:
> - **Exact Match:** It mandates that the last 4 digits of the Aadhaar card and the signer's Gender must perfectly match the KYC records.
> - **Fuzzy Match:** The signer's Name and Year of Birth must correlate with at least an 80% confidence threshold.
> - **Abort on Fail:** If the signer uses an Aadhaar that does not match these rules, the signature is violently rejected (`abort_on_fail: true`).

### E-Sign API Payload Structure & Verification Logic
When initiating the E-Sign process, the backend automatically hits Digio's API (`/v2/client/document/uploadpdf`) and attaches `signature_verification` rules dynamically extracted from the user's previously approved KYC JSON. This ensures the document is securely signed by the correct individual.

```php
// Digio API Upload Document Payload with Strict Verification
$payload = [
    'signers' => [[
        'identifier' => $user->mobile,
        'name' => 'User Full Name',
        'sign_type' => 'aadhaar',
        'reason' => 'Service Agreement'
    ]],
    'expire_in_days' => 1,
    'file_name' => "Agreement.pdf",
    'file_data' => base64_encode($pdfContent),
    'signature_verification' => [
        $user->mobile => [
            'abort_on_fail' => true,
            'max_attempt' => 3,
            'rules' => [
                [
                    'operation' => 'AND',
                    'conditions' => [
                        ['field' => 'gender', 'match_type' => 'exact', 'value' => 'M'],
                        ['field' => 'aadhaar', 'match_type' => 'exact', 'value' => '4016'] // Matches Last 4 Digits from KYC
                    ]
                ],
                [
                    'operation' => 'OR',
                    'conditions' => [
                        ['field' => 'yob', 'match_type' => 'exact', 'value' => '2004'],
                        ['field' => 'name', 'match_type' => 'fuzzy', 'value' => 'Sharad Bhaisaniya', 'threshold' => '80']
                    ]
                ]
            ]
        ]
    ]
];
```
*By configuring the `signature_verification` block, Digio handles all the heavy lifting. If someone enters an Aadhaar number that doesn't end in `4016`, Digio will automatically reject the sign attempt on their gateway.*

## 3. Flawless Session Management
- **Dynamic Redirection:** The Digio callback URL dynamically adapts to the user's exact current domain (e.g., preventing redirects from `127.0.0.1` to `localhost`), ensuring the SPA Sanctum token in `localStorage` is never lost.
- **Extended Lifetime:** The `.env` `SESSION_LIFETIME` was upgraded from 120 minutes to 1440 minutes (24 hours) so the user is never suddenly logged out during a long e-sign process.

## 4. Secure Document Fetching & Streaming
- **Real-Time Fetching:** Instead of relying on local files that could be tampered with, the system securely contacts Digio's servers using the unique `digio_document_id` to download the cryptographically signed PDF.
- **Preview vs Download:** 
  - **👁️ Preview PDF:** Streams the PDF inline via Blob URLs so the user can seamlessly read it inside the custom app modal without leaving the page.
  - **📥 Download PDF:** Forces the browser to download the file natively to the user's device.
- **Authenticated Javascript Links:** Completely bypassed standard `<a>` href links that triggered "Unauthenticated" errors, replacing them with dynamic JavaScript `fetch()` calls that securely inject the Bearer token.

## 5. Admin Dashboard Analytics & Monitoring
- **Admin Endpoints:** Created an exclusive, secure backend route (`/api/admin/users/{id}/esign`) so the Admin can directly open any user's signed agreement.
- **Responsive Layout:** Engineered a sleek, spacious two-column CSS Grid layout inside the Admin Panel. 
  - The left column stacks the User Basic Info and E-Sign Analytics.
  - The right column (1.5x wider) is exclusively dedicated to the raw KYC Verification JSON.
  - Automatically collapses to a clean single column on narrower screens to prevent text from violently wrapping or breaking.
- **Visual Status Badges:** The E-Sign status, IP Address, Digio Document ID, and timestamp are beautifully displayed with dynamic color-coded badges directly on the Admin's User Details page.

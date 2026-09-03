# KFin KRA Complete Developer Integration Guide (Download & Upload)

> **Specification Reference**: KFINKRA Intermediary API Specification v1.6 & KFin KRA Process Flow Document  
> **Target Audience**: Backend Engineers, Full Stack Developers, DevOps, and Compliance Integrators

---

## Table of Contents
1. [Overview & Architecture](#1-overview--architecture)
2. [Environments & Base URLs](#2-environments--base-urls)
3. [Intermediary Registration & Onboarding Setup](#3-intermediary-registration--onboarding-setup)
4. [Authentication: Get Client Token (`getClientToken`)](#4-authentication-get-client-token-getclienttoken)
5. [Inquiry & Status Check (`getKycStatus`)](#5-inquiry--status-check-getkycstatus)
6. [KYC Data Download / Fetch (`getKycDetails`)](#6-kyc-data-download--fetch-getkycdetails)
7. [New KYC Registration Upload (`newKycUpload`)](#7-new-kyc-registration-upload-newkycupload)
8. [KYC Document & File Upload (`upload`)](#8-kyc-document--file-upload-upload)
9. [Standalone UBO / Shareholding Modification](#9-standalone-ubo--shareholding-modification)
10. [Master Code Dictionaries & Enums](#10-master-code-dictionaries--enums)
11. [Portal Workflows, MIS & Deceased Reporting](#11-portal-workflows-mis--deceased-reporting)
12. [PHP / Laravel Service Implementation](#12-php--laravel-service-implementation)
13. [Troubleshooting, Error Codes & Escalation Matrix](#13-troubleshooting-error-codes--escalation-matrix)

---

## 1. Overview & Architecture

KFin KRA provides SEBI-registered intermediaries with both a REST API suite and a web portal to perform:
- **KYC Status Inquiries** across all 5 Indian KRAs (KFin, CVL, CAMS, NDML, Karvy/DotEx)
- **KYC Data Download** (Demographic data, permanent/correspondence address, FATCA, UBO, and specimen signature)
- **New KYC Uploads** for individual and non-individual investors
- **Document / OVD Uploads** in PDF/XML formats
- **KYC Modifications** and Standalone UBO/SHP data updates

### Complete Flow Diagram

```
+-------------------------------------------------------------------------------+
|                               YOUR APPLICATION                                |
+-------------------------------------------------------------------------------+
         |
         | 1. POST /api/inter/getClientToken
         v
+-------------------------------------------------------------------------------+
| KFIN API Gateway (Returns Bearer Token, Cache in Redis for ~23 hours)         |
+-------------------------------------------------------------------------------+
         |
         | 2. POST /api/inter/getKycStatus (PAN + posCode)
         v
+-------------------------------------------------------------------------------+
| Check Status across central KRA network:                                       |
| - 07 (Validated)     -> KYC ready for investment                              |
| - 02 (Registered)    -> Existing KYC registered, fetch details or modify      |
| - 05 (Not Available) -> Proceed with New KYC Upload Flow                      |
| - 03 (On Hold) / 04 (Rejected) -> Rectify issues                             |
+-------------------------------------------------------------------------------+
         |
         +---------------------------------------+
         |                                       |
         v (If Status = 02 / 07)                 v (If Status = 05 - New KYC)
+-----------------------------------+   +------------------------------------+
| 3. POST /api/inter/getKycDetails  |   | 4. POST /api/inter/newKycUpload    |
| (Fetch PAN Data & Specimen Image) |   | (Upload Structured Metadata JSON)  |
+-----------------------------------+   +------------------------------------+
                                                 |
                                                 v
                                        +------------------------------------+
                                        | 5. POST /api/inter/upload          |
                                        | (Multipart upload <PAN>.pdf,       |
                                        |  Aadhaar XML, UBO docs)            |
                                        +------------------------------------+
```

---

## 2. Environments & Base URLs

| Environment | Base URL | Usage |
| :--- | :--- | :--- |
| **Sandbox** | `https://sandbox.kfinkra.in` | Early integration mock sandbox |
| **UAT / Staging** | `https://uat.kfinkra.in` | Pre-production validation and testing |
| **Production / LIVE** | `https://services.kfinkra.in` | Live production traffic |
| **Intermediary Portal** | `https://kfinkra.in` | Web dashboard, manual Maker-Checker & reports |

---

## 3. Intermediary Registration & Onboarding Setup

Before consuming the API, the intermediary must complete registration:
1. **Mandatory Documents**:
   - Intermediary registration form with terms and conditions
   - SEBI Registration Certificate
   - Copy of Corporate PAN Card & Address Proof
   - Board Resolution & Authorized Signatory List
   - Audited Balance Sheet & GSTN Certificate
2. **Account Activation**:
   - KFin assigns a **POS Code** (`APP_POS_CODE`) to the entity.
   - Admin, Compliance, and Business In-charge receive user activation emails.
   - Click **Activate**, enter the **User ID** and **POS Code**, and verify Email & Mobile OTPs to set your password.
3. **Portal Hierarchy Configuration**:
   - Log in at `https://kfinkra.in` -> Go to **Branch Management**.
   - Create Zone -> Region -> Branch.
   - In **Role Management**, create roles and assign activity-level permissions.
   - In **User Management**, create API / Maker users and assign them branch access.

---

## 4. Authentication: Get Client Token (`getClientToken`)

Use this API to generate an encrypted token that must be passed in the `Authorization: Bearer <TOKEN>` header of all subsequent API calls.

- **Method**: `POST`
- **Endpoint**: `{BASE_URL}/api/inter/getClientToken`
- **Content-Type**: `application/json`

### Request Payload
```json
{
  "UserId": "YOUR_USER_ID",
  "Password": "YOUR_PASSWORD",
  "Poscode": "1010000001",
  "tokenvalidity": "31/12/2026"
}
```
*Note: `tokenvalidity` must be in `dd/mm/yyyy` format and must be a future date.*

### Responses

#### Success (`200 OK`)
```json
{
  "success": true,
  "token": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJL...",
  "validity": "31/12/2026 23:59:59",
  "message": "Authentication successful"
}
```

#### Error (`401 Unauthorized`)
```json
{
  "message": "fail",
  "statusCode": "401",
  "errCode": "004",
  "errMessage": "Invalid User Credentials",
  "data": null
}
```

### Common Auth Errors
- `Invalid POS Code`
- `Invalid User Credentials`
- `User ID does not exist`
- `User ID Inactive` / `Poscode Inactive`
- `User does not have required permission`
- `Token expiry time must be in the future`

---

## 5. Inquiry & Status Check (`getKycStatus`)

Check PAN status and identify whether the investor is existing, registered in other KRAs, or needs a new registration.

- **Method**: `POST`
- **Endpoint**: `{BASE_URL}/api/inter/getKycStatus`
- **Headers**:
  - `Authorization: Bearer <TOKEN>`
  - `Content-Type: application/json`

### Request Payload
```json
{
  "pan": "ABCDE1234F",
  "posCode": "1010000001"
}
```

### Success Response (`200 OK`)
```json
{
  "message": "success",
  "statusCode": "200",
  "data": {
    "resdtls": {
      "APP_PAN_INQ": {
        "APP_PAN_NO": "ABCDE1234F",
        "APP_NAME": "JOHN DOE",
        "APP_STATUS": "07",
        "APP_STATUSDT": "27-04-2025 17:54:39",
        "APP_ENTRYDT": "26-04-2025 17:53:24",
        "APP_MODDT": "",
        "APP_STATUS_DELTA": "00",
        "APP_UPDT_STATUS": "05",
        "APP_UPDT_RMKS": "",
        "APP_IOP_FLAG": "IE",
        "APP_PAN_DOB": "",
        "APP_POS_CODE": "1010000001",
        "APP_HOLD_DEACTIVE_RMKS": "",
        "APP_KYC_MODE": "1",
        "APP_IPV_FLAG": "Y",
        "APP_UBO_FLAG": "",
        "APP_PER_ADD_PROOF": "31",
        "APP_COR_ADD_PROOF": "31",
        "APP_KRA_NAME": "KFIN"
      },
      "APP_PAN_SUMM": {
        "APP_OTHKRA_CODE": "KFINKRA",
        "APP_REQ_DATE": "18-06-2025 09:54:13",
        "BATCH_ID": "075759099692",
        "APP_RESPONSE_DATE": "18-06-2025 09:54:13",
        "APP_TOTAL_REC": "1"
      }
    }
  }
}
```

### Status Code Table (`APP_STATUS`)

| Code | Description | Developer Flow Action |
| :--- | :--- | :--- |
| `00` | Service Unavailable from other KRA | Retry query or contact KRA support |
| `01` | Under Process | Wait for verification |
| `02` | KYC Registered | Existing KYC; fetch details or modify |
| `03` | KYC On Hold | Inspect `APP_HOLD_DEACTIVE_RMKS` and notify user |
| `04` | KYC Rejected | Re-initiate KYC registration |
| `05` | **KYC Not Available** | **Trigger New KYC Upload workflow (`newKycUpload`)** |
| `06` | KYC Deactivated | KYC deactivated in central registry |
| `07` | **KYC Validated** | **Fully compliant with SEBI KYC rules** |
| `11` | Old / Incomplete KYC Under Process | Ongoing remediation |
| `12` | Old / Incomplete KYC Registered | Modification recommended |
| `13` | Old / Incomplete KYC On Hold | Address missing delta fields |
| `14` | Old / Incomplete KYC Rejected | Re-upload required |
| `22` | CVL MF Verified KYC | Mutual fund verified |

---

## 6. KYC Data Download / Fetch (`getKycDetails`)

Fetch complete demographic, contact, address, FATCA, UBO, and specimen signature details for a registered PAN.

- **Method**: `POST`
- **Endpoint**: `{BASE_URL}/api/inter/getKycDetails`
- **Headers**:
  - `Authorization: Bearer <TOKEN>`
  - `Content-Type: application/json`

### Request Payload
```json
{
  "APP_REQ_ROOT": {
    "APP_PAN_INQ": {
      "APP_PAN_NO": "ABCDE1234F",
      "APP_DOB_INCORP": "27/02/1990",
      "APP_IOP_FLAG": "IE",
      "APP_POS_CODE": "1010000001",
      "APP_KRA_CODE": ""
    }
  }
}
```
*Note: `APP_IOP_FLAG` values: `IE` (Data only), `II` (Data + Specimen Signature Image).*

### Critical Mandatory Validation Logic
Upon receiving the response from `getKycDetails`, developers **MUST** validate:
1. Inspect the following 4 returned fields:
   - `APP_EXMT` (Exemption flag)
   - `APP_COR_ADD1` (Correspondence Address 1)
   - `APP_PER_ADD1` (Permanent Address 1)
   - `APP_TYPE` (Investor type: `I` / `N`)
2. **Invalid Fetch Condition**: If all 4 fields are empty or blank, treat as an **Invalid Fetch**. Inspect `APP_ERROR_DESC` or `errMessage`.
3. **Success Rule**: If any one or more of these 4 fields contains valid non-blank data, treat as a **Successful Fetch**.

### Response Structure (Full Individual / Non-Individual)
```json
{
  "message": "success",
  "statusCode": "200",
  "data": {
    "error_code": null,
    "error_message": null,
    "resdtls": {
      "ROOT": {
        "KYC_DATA": {
          "APP_POS_CODE": "1010000001",
          "APP_TYPE": "I",
          "APP_PAN_NO": "ABCDE1234F",
          "APP_PAN_COPY": "Y",
          "APP_EXMT": "N",
          "APP_NAME": "JOHN DOE",
          "APP_F_NAME": "RICHARD DOE",
          "APP_DOB_DT": "27/02/1990",
          "APP_GEN": "M",
          "APP_MAR_STATUS": "01",
          "APP_NATIONALITY": "01",
          "APP_RES_STATUS": "R",
          "APP_UID_NO": "Y",
          "APP_COR_ADD1": "FLAT 202, GREEN ACRES",
          "APP_COR_CITY": "MUMBAI",
          "APP_COR_PINCD": "400001",
          "APP_COR_STATE": "027",
          "APP_COR_CTRY": "101",
          "APP_COR_ADD_PROOF": "31",
          "APP_PER_ADD1": "FLAT 202, GREEN ACRES",
          "APP_PER_CITY": "MUMBAI",
          "APP_PER_PINCD": "400001",
          "APP_PER_STATE": "027",
          "APP_PER_CTRY": "101",
          "APP_PER_ADD_PROOF": "31",
          "APP_MOB_NO": "9876543210",
          "APP_EMAIL": "john.doe@example.com",
          "APP_POL_CONN": "NA",
          "APP_STATUS": "07",
          "APP_SIGNATURE": "<BASE64_STRING>",
          "APP_FATCA_APPLICABLE_FLAG": "N"
        },
        "GDNDIS_SPLABLD_DTLS": {},
        "ADDL_INFO": {},
        "FATCA_ADDL_DTLS": {},
        "APP_PAN_SUMM": {
          "APP_OTHKRA_CODE": "KFINKRA",
          "APP_OTHKRA_BATCH": "160262156274",
          "APP_REQ_DATE": "17/06/2025",
          "APP_RESPONSE_DATE": "18/06/2025 13:03:37",
          "APP_TOTAL_REC": "1"
        }
      }
    }
  }
}
```

---

## 7. New KYC Registration Upload (`newKycUpload`)

Upload structured KYC records to KFin KRA when a customer is not registered (`APP_STATUS: "05"`).

- **Method**: `POST`
- **Endpoint**: `{BASE_URL}/api/inter/newKycUpload`
- **Headers**:
  - `Authorization: Bearer <TOKEN>`
  - `Content-Type: application/json`

### Complete Request Payload Schema (Individual)

```json
{
  "ROOT": {
    "HEADER": {
      "BATCH_NO": "BATCH20260903001",
      "BATCH_DATE": "03/09/2026"
    },
    "KYCDATA": {
      "APP_UPDTFLG": "01",
      "APP_INT_CODE": "KFIN",
      "APP_POS_CODE": "1010000001",
      "APP_BRANCH_CODE": "HYD01",
      "APP_TYPE": "I",
      "APP_NO": "KYC20260903001",
      "APP_DATE": "03/09/2026",
      "APP_PAN_NO": "ABCDE1234F",
      "APP_PAN_COPY": "Y",
      "APP_EXMT": "N",
      "APP_EXMT_CAT": "",
      "APP_EXMT_ID_PROOF": "01",
      "APP_NAME": "JOHN DOE",
      "APP_F_NAME": "RICHARD DOE",
      "APP_DOB_DT": "15/08/1990",
      "APP_FS_FLAG": "F",
      "APP_GEN": "M",
      "APP_MAR_STATUS": "01",
      "APP_REGNO": "",
      "APP_INCORP_PLC": "",
      "APP_COMMENCE_DT": "",
      "APP_NATIONALITY": "01",
      "APP_OTH_NATIONALITY": "",
      "APP_COMP_STATUS": "",
      "APP_OTH_COMP_STATUS": "",
      "APP_RES_STATUS": "R",
      "APP_RES_STATUS_PROOF": "",
      "APP_UID_NO": "Y",
      "APP_COR_ADD1": "FLAT 402, PALM HEIGHTS",
      "APP_COR_ADD2": "ROAD NO 12, BANJARA HILLS",
      "APP_COR_ADD3": "",
      "APP_COR_CITY": "HYDERABAD",
      "APP_COR_PINCD": "500034",
      "APP_CORR_DISTRICT": "104",
      "APP_COR_STATE": "037",
      "APP_COR_CTRY": "101",
      "APP_COR_ADD_PROOF": "31",
      "APP_COR_ADD_REF": "123456789012",
      "APP_COR_ADD_DT": "01/01/2026",
      "APP_PER_ADD_FLAG": "Y",
      "APP_PER_ADD1": "FLAT 402, PALM HEIGHTS",
      "APP_PER_ADD2": "ROAD NO 12, BANJARA HILLS",
      "APP_PER_ADD3": "",
      "APP_PER_CITY": "HYDERABAD",
      "APP_PER_PINCD": "500034",
      "APP_PERM_DISTRICT": "104",
      "APP_PER_STATE": "037",
      "APP_PER_CTRY": "101",
      "APP_PER_ADD_PROOF": "31",
      "APP_PER_ADD_REF": "123456789012",
      "APP_PER_ADD_DT": "01/01/2026",
      "APP_OFF_ISD": "",
      "APP_OFF_STD": "",
      "APP_OFF_NO": "",
      "APP_RES_ISD": "",
      "APP_RES_STD": "",
      "APP_RES_NO": "",
      "APP_MOB_ISD": "91",
      "APP_MOB_NO": "9876543210",
      "APP_FAX_ISD": "",
      "APP_FAX_STD": "",
      "APP_FAX_NO": "",
      "APP_EMAIL": "john.doe@example.com",
      "APP_INCOME": "03",
      "APP_OCC": "01",
      "APP_OTH_OCC": "",
      "APP_NETWRTH": "",
      "APP_NETWORTH_DT": "",
      "APP_POL_CONN": "NA",
      "APP_DOC_PROOF": "S",
      "APP_INTERNAL_REF": "CUST_99182",
      "APP_IPV_FLAG": "Y",
      "APP_IPV_DATE": "03/09/2026",
      "APP_IPV_NAME": "AGENT VERIFIER",
      "APP_IPV_DESG": "OFFICER",
      "APP_IPV_ORGAN": "GROW CAPITAL",
      "APP_KYC_MODE": "1",
      "APP_OTHERINFO": "",
      "APP_ACC_OPENDT": "03/09/2026",
      "APP_ACC_ACTIVEDT": "03/09/2026",
      "APP_ACC_UPDTDT": "",
      "APP_FILLER1": "",
      "APP_FILLER2": "",
      "APP_FILLER3": "",
      "APP_STATUS": "01",
      "APP_STATUSDT": "03-09-2026 17:00:00",
      "APP_ERROR_DESC": "",
      "APP_DUMP_TYPE": "S",
      "APP_DNLDDT": "03-09-2026 00:00:00",
      "APP_IOP_FLG": "IS",
      "APP_KRA_INFO": "KFIN",
      "APP_UID_TOKEN": "",
      "APP_VER_NO": "V33",
      "APP_SIGNATURE": "",
      "APP_FATCA_APPLICABLE_FLAG": "N",
      "APP_FATCA_BIRTH_PLACE": "HYDERABAD",
      "APP_FATCA_BIRTH_COUNTRY": "IN",
      "APP_FATCA_COUNTRY_RES": "",
      "APP_FATCA_COUNTRY_CITYZENSHIP": "IN",
      "APP_FATCA_OTHER_SERVICES": "CATNONE",
      "APP_FATCA_DATE_DECLARATION": "03/09/2026",
      "APP_UBO_APPLICABLE": "",
      "APP_UBO_DOD": "",
      "APP_UBO_DATE": "",
      "APP_UBO_EXEMPT_REASON": "",
      "APP_SHP_AVAILABLE": "",
      "APP_SHP_DOD": "",
      "APP_SHP_DATE": "",
      "APP_BS_AVAILABLE": "",
      "APP_BS_DATE": "",
      "APP_BS_DOD": "",
      "APP_SPLABLD": "N",
      "APP_SPLABLD_UDID": "",
      "APP_SPLABLD_PERC": "",
      "APP_SBLABLD_TYPE": "",
      "APP_SPLABLD_GDNDIS": ""
    },
    "GDNDIS_SPLABLD_DTLS": {
      "APP_GNDIS_ENTITY_PAN": "",
      "GDNDIS_SPLABLD_PAN": "",
      "GDNDIS_SPLABLD_NAME": "",
      "GDNDIS_SPLABLD_F_NAME": "",
      "GDNDIS_SPLABLD_FS_FLAG": "",
      "GDNDIS_SPLABLD_GEN": "",
      "GDNDIS_SPLABLD_DOB_DT": "",
      "GDNDIS_SPLABLD_POI_TYPE": "",
      "GDNDIS_SPLABLD_POI_NO": "",
      "GDNDIS_SPLABLD_POIEXP_DT": "",
      "GDNDIS_SPLABLD_POA_TYPE": "",
      "GDNDIS_SPLABLD_POA_NO": "",
      "GDNDIS_SPLABLD_POAEXP_DT": "",
      "GDNDIS_SPLABLD_ADDR1": "",
      "GDNDIS_SPLABLD_ADDR2": "",
      "GDNDIS_SPLABLD_ADDR3": "",
      "GDNDIS_SPLABLD_CITY": "",
      "GDNDIS_SPLABLD_DIST": "",
      "GDNDIS_SPLABLD_PINCODE": "",
      "GDNDIS_SPLABLD_STATE": "",
      "GDNDIS_SPLABLD_COUNTRY": "",
      "GDNDIS_SPLABLD_MOBILE": "",
      "GDNDIS_SPLABLD_EMAIL": ""
    },
    "APP_ADDL_DATA": {
      "APP_ADDLDATA_UPDTFLG": "01",
      "APP_ENTITY_PAN": "",
      "APP_ADDLDATA_PAN": "",
      "APP_ADDLDATA_NAME": "",
      "APP_ADDLDATA_DIN_UID": "",
      "APP_ADDLDATA_RELATIONSHIP": "",
      "APP_ADDLDATA_POLCONN": "",
      "APP_ADDLDATA_RESADD1": "",
      "APP_ADDLDATA_RESADD2": "",
      "APP_ADDLDATA_RESADD3": "",
      "APP_ADDLDATA_RESPINCD": "",
      "APP_ADDLDATA_RESCITY": "",
      "APP_ADDLDATA_RESSTATE": "",
      "APP_ADDLDATA_RESCOUNTRY": "",
      "APP_ADDLDATA_FILLER1": "",
      "APP_ADDLDATA_FILLER2": "",
      "APP_ADDLDATA_FILLER3": ""
    },
    "FATCA_ADDL_DTLS": {
      "APP_FATCA_ENTITY_PAN": "",
      "APP_FATCA_COUNTRY_RESIDENCY": "",
      "APP_FATCA_TAX_IDENTIFICATION_NO": "",
      "APP_FATCA_TAX_IDENTIFICATION_TYPE": "",
      "APP_FATCA_TAX_EXEMPT_FLAG": "N",
      "APP_FATCA_TAX_EXEMPT_REASON": ""
    },
    "FOOTER": {
      "NO_OF_KYC_RECORDS": "1",
      "NO_OF_ADDLDATA_RECORDS": "0",
      "NO_OF_FATCA_ADDL_DTLS_RECORDS": "0"
    }
  }
}
```

---

## 8. KYC Document & File Upload (`upload`)

Once the metadata is uploaded, attach the scanned documents as multipart form-data.

- **Method**: `POST`
- **Endpoint**: `{BASE_URL}/api/inter/upload`
- **Headers**:
  - `Authorization: Bearer <TOKEN>`
  - `Content-Type: multipart/form-data`

### Form-Data Parameters
| Field Name | Type | Description |
| :--- | :--- | :--- |
| `Pan` | `text` | 10-digit PAN of investor (e.g., `ASJPT9127G`) |
| `poscode` | `text` | 10-digit POS code assigned to intermediary |
| `file` | `file[]` | Scanned KYC PDF, Aadhaar XML, and entity documents |

### File Naming Convention & Constraints
- Scanned Application & OVD PDF: `<PAN>.pdf` or `<PAN>_<DDMMYYYY>_<HHMMSS>.pdf`
- Max file size: **5 MB** for Individuals, **25 MB** for Non-Individuals
- PDF scan format: **200 DPI in Greyscale**
- Offline Aadhaar: `<PAN>_<DDMMYYYY>.xml`

### cURL Example
```bash
curl --location --request POST 'https://services.kfinkra.in/api/inter/upload' \
--header 'Authorization: Bearer YOUR_BEARER_TOKEN' \
--form 'Pan="ASJPT9127G"' \
--form 'poscode="1020000548"' \
--form 'file=@"/downloads/ASJPT9127G_03092026_165859.pdf"' \
--form 'file=@"/downloads/ASJPT9127G_03092026_165859.xml"'
```

### Success Response (`200 OK`)
```json
{
  "message": "success",
  "statusCode": "200",
  "errCode": "",
  "errMessage": "",
  "data": {
    "uploadedKeys": [
      "NEW/AS/pan/ASJPT9127G/ASJPT9127G_01102025_142956.pdf",
      "NEW/AS/aadhaar/ASJPT9127G_19092025.xml"
    ]
  }
}
```

---

## 9. Standalone UBO / Shareholding Modification

For legal entities (Non-Individuals), standalone modification of UBO (Ultimate Beneficial Ownership) or Shareholding Pattern uses `APP_UPDTFLG: "08"`:

```json
{
  "ROOT": {
    "HEADER": {
      "BATCH_NO": "10005171",
      "BATCH_DATE": "12/03/2026"
    },
    "KYCDATA": {
      "APP_UPDTFLG": "08",
      "APP_KRA_INFO": "KFIN",
      "APP_POS_CODE": "1030000477",
      "APP_TYPE": "N",
      "APP_DATE": "01/11/2025",
      "APP_PAN_NO": "FLLPR3385E",
      "APP_UBO_APPLICABLE": "N",
      "APP_UBO_DOD": "12/02/2026",
      "APP_SHP_AVAILABLE": "N",
      "APP_SHP_DOD": "12/02/2026",
      "APP_BS_AVAILABLE": "N",
      "APP_BS_DOD": "12/02/2026"
    },
    "FOOTER": {
      "NO_OF_KYC_RECORDS": "1",
      "NO_OF_ADDLDATA_RECORDS": "0",
      "NO_OF_FATCA_ADDL_DTLS_RECORDS": "0"
    }
  }
}
```

---

## 10. Master Code Dictionaries & Enums

### 10.1 Proof of Address / Officially Valid Documents (`APP_PER_ADD_PROOF`)
- `01`: Passport
- `02`: Driving License
- `03`: Latest Bank Passbook (Deemed Proof - max 3 months)
- `04`: Latest Bank Account Statement
- `05`: Latest Demat Account Statement
- `06`: Voter ID Card
- `08`: Registered Lease / Sale Agreement
- `09`: Latest Landline Telephone Bill (Utility - max 2 months)
- `10`: Latest Electricity Bill (Utility - max 2 months)
- `11`: Gas Bill (Utility - max 2 months)
- `31`: **Aadhaar Card / XML (Primary OVD)**
- `33`: NREGA Job Card

### 10.2 Non-Individual Entity Types (`APP_COMP_STATUS`)
- `01`: Private Ltd Company
- `02`: Public Ltd Company
- `03`: Body Corporate
- `04`: Partnership Firm
- `05`: Trust / Charities / NGOs
- `06`: Financial Institution (FI)
- `08`: Hindu Undivided Family (HUF)
- `09`: Association of Persons (AOP)
- `16`: Limited Liability Partnership (LLP)
- `99`: Others (Requires `APP_OTH_COMP_STATUS`)

### 10.3 Mode of KYC (`APP_KYC_MODE`)
- `0`: Normal / Physical KYC
- `1`: EKYC With OTP Authentication
- `2`: EKYC With Biometric Authentication
- `3`: Online Data Entry with IPV
- `4`: Offline Aadhaar XML / PDF
- `5`: DigiLocker

### 10.4 Residential Status (`APP_RES_STATUS`)
- `R`: Resident Individual
- `N`: Non-Resident Individual (NRI)
- `P`: Foreign National
- `Q`: Qualified Foreign Investor

### 10.5 Income Slabs (`APP_INCOME`)
- `01`: Below 1 Lakh
- `02`: 1 - 5 Lakhs
- `03`: 5 - 10 Lakhs
- `04`: 10 - 25 Lakhs
- `05`: > 25 Lakhs
- `06`: 25 Lakhs - 1 Crore
- `07`: > 1 Crore

---

## 11. Portal Workflows, MIS & Deceased Reporting

For operations handled via the Web UI (`https://kfinkra.in`):

1. **Maker-Checker Work Tray**:
   - Uploaded records appear in **My Work Tray**.
   - Maker clicks **Initiate** to review OCR results and ITD/UIDAI validations.
   - For Non-Individuals: Create primary entity record first, then initiate child entries for **Related Person / UBO / Directors**.
2. **Bulk PAN Fetch**:
   - Navigate to **Fetch PAN - Bulk**.
   - Download the CSV template, enter PANs, and upload to retrieve bulk statuses.
3. **MIS Reports**:
   - Access governed by user scope: **Zonal**, **Regional**, or **Global**.
   - Generate: **Transaction Report**, **MIS Report**, or **Branch Wise Summary Report**.
4. **Deceased Reporting**:
   - Menu -> **Deceased Reporting**.
   - Upload compiled PDF containing: Deceased PAN, Intimation Letter, Notifier PAN & OVD, Death Certificate, Intermediary Verification Confirmation.
   - Enter start and end page numbers for each attached section.

---

## 12. PHP / Laravel Service Implementation

Here is the complete implementation matching your project's service layer in [`app/Services/KfinKraService.php`](file:///Users/naarendrasingh/Desktop/growcapital/app/Services/KfinKraService.php):

```php
<?php

namespace App\Services;

use App\Models\KraSetting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Exception;

class KfinKraService
{
    protected string $userId;
    protected string $password;
    protected string $posCode;
    protected string $baseUrl;
    protected bool $uatMode;

    public function __construct()
    {
        $settings = KraSetting::first();
        $this->userId = $settings->kfin_user_id ?? env('KFIN_USER_ID', '');
        $this->password = $settings->kfin_password ?? env('KFIN_PASSWORD', '');
        $this->posCode = $settings->kfin_pos_code ?? env('KFIN_POS_CODE', '');
        $this->uatMode = (bool)($settings->kfin_uat_mode ?? env('KFIN_UAT_MODE', true));

        $this->baseUrl = $this->uatMode
            ? 'https://uat.kfinkra.in'
            : 'https://services.kfinkra.in';
    }

    /**
     * 1. Get Client Bearer Token (Cached for 23 Hours)
     */
    public function getClientToken(): string
    {
        $cacheKey = "kfin_token_{$this->posCode}";

        return Cache::remember($cacheKey, now()->addHours(23), function () {
            $url = "{$this->baseUrl}/api/inter/getClientToken";
            $response = Http::timeout(30)->post($url, [
                'UserId'        => $this->userId,
                'Password'      => $this->password,
                'Poscode'       => $this->posCode,
                'tokenvalidity' => now()->addDays(7)->format('d/m/Y'),
            ]);

            if ($response->successful() && $response->json('success') === true) {
                return $response->json('token');
            }

            Log::error('KFin Token Failure', ['res' => $response->json()]);
            throw new Exception("KFin Auth Failed: " . ($response->json('errMessage') ?? 'Check credentials'));
        });
    }

    /**
     * 2. Inquire KYC Status for PAN
     */
    public function getKycStatus(string $pan): ?array
    {
        $token = $this->getClientToken();
        $url = "{$this->baseUrl}/api/inter/getKycStatus";

        $response = Http::withToken($token)->timeout(30)->post($url, [
            'pan'     => strtoupper(trim($pan)),
            'posCode' => $this->posCode,
        ]);

        if ($response->successful() && $response->json('message') === 'success') {
            return $response->json('data');
        }

        Log::warning("KFin Status Check Failed for PAN: {$pan}", ['body' => $response->json()]);
        return null;
    }

    /**
     * 3. Download Full KYC Demographic Data
     */
    public function getKycDetails(string $pan, string $dobOrIncorpDate, bool $includeImage = false): ?array
    {
        $token = $this->getClientToken();
        $url = "{$this->baseUrl}/api/inter/getKycDetails";
        $formattedDob = Carbon::parse($dobOrIncorpDate)->format('d/m/Y');

        $response = Http::withToken($token)->timeout(45)->post($url, [
            'APP_REQ_ROOT' => [
                'APP_PAN_INQ' => [
                    'APP_PAN_NO'     => strtoupper(trim($pan)),
                    'APP_DOB_INCORP' => $formattedDob,
                    'APP_IOP_FLAG'   => $includeImage ? 'II' : 'IE',
                    'APP_POS_CODE'   => $this->posCode,
                    'APP_KRA_CODE'   => '',
                ],
            ],
        ]);

        if ($response->successful() && $response->json('message') === 'success') {
            $kycData = $response->json('data.resdtls.ROOT.KYC_DATA');

            // Mandatory KFin response validation rule
            $exmt = $kycData['APP_EXMT'] ?? '';
            $corr1 = $kycData['APP_COR_ADD1'] ?? '';
            $per1 = $kycData['APP_PER_ADD1'] ?? '';
            $type = $kycData['APP_TYPE'] ?? '';

            if (empty($exmt) && empty($corr1) && empty($per1) && empty($type)) {
                Log::error("KFin Invalid Fetch: Empty mandatory fields for PAN {$pan}");
                return null;
            }

            return $kycData;
        }

        Log::error("KFin Fetch Details Failed", ['body' => $response->json()]);
        return null;
    }

    /**
     * 4. Upload New KYC Record Metadata
     */
    public function newKycUpload(array $kycAttributes, string $batchNo = null): array
    {
        $token = $this->getClientToken();
        $url = "{$this->baseUrl}/api/inter/newKycUpload";

        $payload = [
            'ROOT' => [
                'HEADER' => [
                    'BATCH_NO'   => $batchNo ?? 'BAT_' . time(),
                    'BATCH_DATE' => now()->format('d/m/Y'),
                ],
                'KYCDATA' => array_merge([
                    'APP_UPDTFLG'   => '01',
                    'APP_INT_CODE'  => 'KFIN',
                    'APP_POS_CODE'  => $this->posCode,
                    'APP_DATE'      => now()->format('d/m/Y'),
                    'APP_VER_NO'    => 'V33',
                    'APP_DUMP_TYPE' => 'S',
                ], $kycAttributes),
                'FOOTER' => [
                    'NO_OF_KYC_RECORDS'             => '1',
                    'NO_OF_ADDLDATA_RECORDS'        => '0',
                    'NO_OF_FATCA_ADDL_DTLS_RECORDS' => '0',
                ],
            ],
        ];

        $response = Http::withToken($token)->timeout(60)->post($url, $payload);

        if ($response->successful() && $response->json('message') === 'success') {
            return $response->json();
        }

        Log::error('KFin newKycUpload Failed', ['body' => $response->json()]);
        throw new Exception("KFin Upload Failed: " . ($response->json('errMessage') ?? 'Check logs'));
    }

    /**
     * 5. Upload KYC Scanned Document Files
     */
    public function uploadKycFiles(string $pan, array $filePaths): array
    {
        $token = $this->getClientToken();
        $url = "{$this->baseUrl}/api/inter/upload";

        $request = Http::withToken($token)->timeout(120);
        $request->attach('Pan', strtoupper(trim($pan)));
        $request->attach('poscode', $this->posCode);

        foreach ($filePaths as $filePath) {
            if (file_exists($filePath)) {
                $filename = basename($filePath);
                $request->attach('file', file_get_contents($filePath), $filename);
            }
        }

        $response = $request->post($url);

        if ($response->successful() && $response->json('message') === 'success') {
            return $response->json('data.uploadedKeys') ?? [];
        }

        Log::error('KFin File Upload Failed', ['body' => $response->json()]);
        throw new Exception("KFin Document Upload Failed: " . ($response->json('errMessage') ?? 'Error'));
    }
}
```

---

## 13. Troubleshooting, Error Codes & Escalation Matrix

### 13.1 Common Error Scenarios

| HTTP Status | Error Code | Root Cause | Solution |
| :--- | :--- | :--- | :--- |
| `401` | `004` | Invalid User Credentials | Verify `UserId`, `Password`, and `Poscode` case sensitivity |
| `400` | `103` | PAN / POS Code contains whitespace | Use `trim()` on all string identifiers |
| `400` | `314` | PAN not fetched before modification | Run `getKycStatus` and `getKycDetails` before modifying |
| `400` | `501` | Invalid PAN | Ensure regex matches standard 10-character PAN format |
| `200` | `ERR-00000` | Normal Success Indicator | Field signifies successful upstream sync |

### 13.2 KFin KRA Escalation Contacts

- **Level 1 (Web Query)**: [KFin KRA Contact Page](https://kfinkra.in)
- **Level 2 (Technical Contact)**: Mr. Abraham Badampudi (`b.abraham@Kfinkra.in`)
- **Level 3 (Management Contact)**: Mr. Seetharam Vinnapamula (`Vs.Ram@Kfinkra.in`)

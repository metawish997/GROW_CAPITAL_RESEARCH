<?php

namespace App\Services;

use App\Models\KraSetting;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use SoapClient;
use SoapVar;
use Exception;

class NdmlKraService
{
    protected $userId;
    protected $password;
    protected $bpId;
    protected $passkey;
    protected $encryptionKey;
    protected $uatMode;

    // SFTP credentials
    protected $sftpHost;
    protected $sftpPort;
    protected $sftpUsername;
    protected $sftpPassword;

    // WSDL endpoints
    protected $okraWsdl;
    protected $okraLocation;
    protected $panWsdl;
    protected $panLocation;

    public function __construct()
    {
        $this->resolveCredentials();
        $this->initializeEndpoints();
    }

    /**
     * Resolve credentials from KraSetting table or fallback to .env variables.
     */
    protected function resolveCredentials()
    {
        $settings = null;
        try {
            $settings = KraSetting::first();
        } catch (Exception $e) {
            // Table might not exist or migration pending; fallback to .env silently
        }

        if ($settings) {
            $this->userId = $settings->ndml_user_id;
            $this->password = $settings->ndml_password;
            $this->bpId = $settings->ndml_bp_id;
            $this->passkey = $settings->ndml_passkey;
            $this->encryptionKey = $settings->ndml_encryption_key;
            $this->uatMode = (bool) $settings->ndml_uat_mode;

            $this->sftpHost = $settings->sftp_host;
            $this->sftpPort = $settings->sftp_port ?? 22;
            $this->sftpUsername = $settings->sftp_username;
            $this->sftpPassword = $settings->sftp_password;
        } else {
            // Fallback to Env
            $this->userId = env('NDML_USER_ID');
            $this->password = env('NDML_PASSWORD');
            $this->bpId = env('NDML_BP_ID');
            $this->passkey = env('NDML_PASSKEY');
            $this->encryptionKey = env('NDML_ENCRYPTION_KEY', env('NDML_PASSKEY'));
            $this->uatMode = env('NDML_UAT_MODE', true);

            $this->sftpHost = env('NDML_SFTP_HOST');
            $this->sftpPort = env('NDML_SFTP_PORT', 22);
            $this->sftpUsername = env('NDML_SFTP_USERNAME');
            $this->sftpPassword = env('NDML_SFTP_PASSWORD');
        }
    }

    /**
     * Define appropriate WSDL URLs based on UAT or Production environment mode.
     */
    protected function initializeEndpoints()
    {
        if ($this->uatMode) {
            $this->okraWsdl = 'https://pilot.kra.ndml.in/okra-iop/services/OkraServiceImpl/wsdl/OkraServiceImpl.wsdl';
            $this->okraLocation = 'https://pilot.kra.ndml.in/okra-iop/services/OkraServiceImpl';
            $this->panWsdl  = 'https://pilot.kra.ndml.in/sms-ws/PANServiceImplService/PANServiceImplService.wsdl';
            $this->panLocation = 'https://pilot.kra.ndml.in/sms-ws/PANServiceImplService';
        } else {
            $this->okraWsdl = 'https://kra.ndml.in/okra-iop/services/OkraServiceImpl/wsdl/OkraServiceImpl.wsdl';
            $this->okraLocation = 'https://kra.ndml.in/okra-iop/services/OkraServiceImpl';
            $this->panWsdl  = 'https://kra.ndml.in/sms-ws/PANServiceImplService/PANServiceImplService.wsdl';
            $this->panLocation = 'https://kra.ndml.in/sms-ws/PANServiceImplService';
        }
    }

    /**
     * Create SoapClient instance with robust local / production SSL settings.
     */
    protected function getSoapClient(string $wsdl, ?string $location = null): SoapClient
    {
        $context = stream_context_create([
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            ],
            'http' => [
                'timeout' => 30
            ]
        ]);

        $options = [
            'trace' => 1,
            'exceptions' => true,
            'cache_wsdl' => WSDL_CACHE_NONE,
            'soap_version' => SOAP_1_1,
            'stream_context' => $context
        ];

        if ($location) {
            $options['location'] = $location;
        }

        return new SoapClient($wsdl, $options);
    }

    /**
     * 1. Authentication flow for Registration (OkraServiceImpl)
     * Calls getPassword(password, passKey)
     */
    public function getRegistrationEncryptedPassword(?string $password = null, ?string $passkey = null): string
    {
        $pwd = $password ?? $this->password;
        $key = $passkey ?? $this->passkey;

        if (empty($pwd) || empty($key)) {
            throw new Exception("NDML password or passkey credentials not configured.");
        }

        try {
            $client = $this->getSoapClient($this->okraWsdl, $this->okraLocation);
            $response = $client->getPassword([
                'password' => $pwd,
                'key' => $key
            ]);
            
            Log::info("NDML Registration getPassword success.");
            if (isset($response->getPasswordReturn)) {
                return $response->getPasswordReturn;
            }
            return (string) $response;
        } catch (\Throwable $e) {
            Log::error("NDML getPassword error: " . $e->getMessage());
            throw new Exception("NDML Authentication Password fetch failed: " . $e->getMessage());
        }
    }

    /**
     * 2. Authentication flow for Inquiry/Download (PANServiceImpl)
     * Calls getPasscode(password, encryptionKey)
     */
    public function getInquiryEncryptedPassword(?string $password = null, ?string $encryptionKey = null): string
    {
        $pwd = $password ?? $this->password;
        $key = $encryptionKey ?? $this->encryptionKey;

        if (empty($pwd) || empty($key)) {
            throw new Exception("NDML password or encryptionKey credentials not configured.");
        }

        try {
            $client = $this->getSoapClient($this->panWsdl, $this->panLocation);
            
            // Note: arg0 is password, arg1 is passkey
            $params = [
                'arg0' => $pwd,
                'arg1' => substr($key, 0, 8) // Max 8 characters as per documentation
            ];

            $response = $client->getPasscode($params);
            
            if (isset($response->return)) {
                return $response->return;
            }

            return $response;
        } catch (\Throwable $e) {
            Log::error("NDML getPasscode error: " . $e->getMessage());
            throw new Exception("NDML Inquiry Password hashing failed: " . $e->getMessage());
        }
    }

    /**
     * 3. KYC Status Inquiry
     * Calls panInquiryDetailsTwo (both registration & modification status)
     */
    public function checkKycStatus(string $pan, ?string $mobile = null, ?string $dob = null): array
    {
        $cleanPan = strtoupper(trim($pan));
        $dobFormatted = $dob ? date('dmy', strtotime($dob)) : '01011900'; // fallback
        $phone = $mobile ?? '9988899999';

        $encryptedPwd = $this->getInquiryEncryptedPassword();

        // 10-digit track ID
        $reqNo = substr(time() . rand(100, 999), -10);

        // Build XML Request
        $xmlRequest = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>
<APP_REQ_ROOT>
    <APP_PAN_INQ>
        <APP_PAN_NO>{$cleanPan}</APP_PAN_NO>
        <APP_PAN_DOB>{$dobFormatted}</APP_PAN_DOB>
        <APP_MOBILE_NO>{$phone}</APP_MOBILE_NO>
        <APP_REQ_NO>{$reqNo}</APP_REQ_NO>
    </APP_PAN_INQ>
</APP_REQ_ROOT>";

        try {
            $client = $this->getSoapClient($this->panWsdl, $this->panLocation);
            
            $params = [
                'arg0' => $xmlRequest,
                'arg1' => $this->userId,
                'arg2' => $encryptedPwd,
                'arg3' => substr($this->encryptionKey, 0, 8)
            ];

            // Method 2 (InquiryDetailsTwo) is recommended as per manual
            $response = $client->panInquiryDetailsTwo($params);
            
            $xmlString = htmlspecialchars_decode($response->return ?? $response);
            $xmlObject = simplexml_load_string($xmlString);
            
            Log::info("NDML KRA panInquiryDetailsTwo executed successfully for PAN: " . $cleanPan);

            return [
                'success' => true,
                'raw_xml' => $xmlString,
                'parsed'  => json_decode(json_encode($xmlObject), true)
            ];

        } catch (\Throwable $e) {
            Log::error("NDML panInquiryDetailsTwo failed: " . $e->getMessage());
            return [
                'success' => false,
                'error'   => $e->getMessage()
            ];
        }
    }

    /**
     * 4. KYC Download Details
     * Calls panDownloadDetails
     */
    public function downloadKycDetails(string $pan, string $dob, ?string $mobile = null): array
    {
        $cleanPan = strtoupper(trim($pan));
        $dobFormatted = date('dmy', strtotime($dob));
        $phone = $mobile ?? '9967751714';

        $encryptedPwd = $this->getInquiryEncryptedPassword();
        $reqNo = substr(time() . rand(100, 999), -10);

        $xmlRequest = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>
<APP_REQ_ROOT>
    <APP_PAN_DOWN>
        <APP_PAN_NO>{$cleanPan}</APP_PAN_NO>
        <APP_PAN_DOB>{$dobFormatted}</APP_PAN_DOB>
        <APP_MOBILE_NO>{$phone}</APP_MOBILE_NO>
        <APP_REQ_NO>{$reqNo}</APP_REQ_NO>
    </APP_PAN_DOWN>
</APP_REQ_ROOT>";

        try {
            $client = $this->getSoapClient($this->panWsdl, $this->panLocation);
            
            $params = [
                'arg0' => $xmlRequest,
                'arg1' => $this->userId,
                'arg2' => $encryptedPwd,
                'arg3' => substr($this->encryptionKey, 0, 8)
            ];

            $response = $client->panDownloadDetails($params);
            
            $xmlString = htmlspecialchars_decode($response->return ?? $response);
            $xmlObject = simplexml_load_string($xmlString);

            return [
                'success' => true,
                'raw_xml' => $xmlString,
                'parsed'  => json_decode(json_encode($xmlObject), true)
            ];

        } catch (\Throwable $e) {
            Log::error("NDML panDownloadDetails failed: " . $e->getMessage());
            return [
                'success' => false,
                'error'   => $e->getMessage()
            ];
        }
    }

    /**
     * 5. KYC Registration (Upload New KYC Details)
     * Calls registration on OkraServiceImpl
     */
    public function registerKyc($user, $kycVerification): array
    {
        $xmlRequest = $this->buildRegistrationXml($user, $kycVerification, 'IE');
        
        $encryptedPwd = $this->getRegistrationEncryptedPassword();

        try {
            $client = $this->getSoapClient($this->okraWsdl, $this->okraLocation);
            
            // byte[] is passed as dynamic SoapVar or raw base64 Binary content
            $xmlBytes = new SoapVar($xmlRequest, XSD_STRING);

            $response = $client->registration([
                'input' => $xmlBytes, 
                'userId' => $this->userId, 
                'userPassword' => $encryptedPwd, 
                'passKey' => $this->passkey, 
                'okraCdOrMiId' => $this->bpId
            ]);

            // Response comes in bytes or string
            $xmlString = is_string($response) ? $response : (isset($response->registrationReturn) ? $response->registrationReturn : $response);
            $xmlObject = simplexml_load_string($xmlString);

            Log::info("NDML KRA registerKyc successfully invoked for user ID: " . $user->id);

            return [
                'success' => true,
                'raw_xml' => $xmlString,
                'parsed'  => json_decode(json_encode($xmlObject), true)
            ];

        } catch (\Throwable $e) {
            Log::error("NDML KRA registerKyc failed: " . $e->getMessage());
            return [
                'success' => false,
                'error'   => $e->getMessage()
            ];
        }
    }

    /**
     * 6. KYC Modification (Upload modified KYC details)
     * Calls processModification on PANServiceImpl WSDL
     */
    public function modifyKyc($user, $kycVerification): array
    {
        $xmlRequest = $this->buildRegistrationXml($user, $kycVerification, 'II');
        
        $encryptedPwd = $this->getInquiryEncryptedPassword();

        try {
            $client = $this->getSoapClient($this->panWsdl, $this->panLocation);
            
            $params = [
                'arg0' => $xmlRequest,
                'arg1' => $this->userId,
                'arg2' => $encryptedPwd,
                'arg3' => substr($this->encryptionKey, 0, 8),
                'arg4' => $this->bpId
            ];

            $response = $client->processModification($params);
            
            $xmlString = htmlspecialchars_decode($response->return ?? $response);
            $xmlObject = simplexml_load_string($xmlString);

            Log::info("NDML KRA modifyKyc successfully invoked for user ID: " . $user->id);

            return [
                'success' => true,
                'raw_xml' => $xmlString,
                'parsed'  => json_decode(json_encode($xmlObject), true)
            ];

        } catch (\Throwable $e) {
            Log::error("NDML KRA modifyKyc failed: " . $e->getMessage());
            return [
                'success' => false,
                'error'   => $e->getMessage()
            ];
        }
    }

    /**
     * 7. SFTP Zero-dependency Document Upload via Curl
     */
    public function uploadKycDocumentsToSftp(string $pan, string $pdfFilePath): bool
    {
        $cleanPan = strtoupper(trim($pan));
        
        if (empty($this->sftpHost) || empty($this->sftpUsername) || empty($this->sftpPassword)) {
            Log::warning("SFTP credentials are not fully configured. Document upload skipped.");
            return false;
        }

        if (!file_exists($pdfFilePath)) {
            Log::error("Local PDF document not found for KRA SFTP upload: " . $pdfFilePath);
            return false;
        }

        $dateFolder = date('Y-m-d');
        $sftpUrl = "sftp://{$this->sftpHost}:{$this->sftpPort}/Image%20Upload/{$dateFolder}/{$cleanPan}.pdf";

        Log::info("Initiating NDML SFTP Document Upload", [
            'pan' => $cleanPan,
            'sftp_url' => "sftp://{$this->sftpHost}:{$this->sftpPort}/Image Upload/{$dateFolder}..."
        ]);

        try {
            // First we need to make sure the target YYYY-MM-DD folder exists.
            // Under standard SFTP with cURL, creating directories dynamically is supported 
            // via FTP_CREATE_DIR or postquote commands.
            
            $ch = curl_init();
            $fp = fopen($pdfFilePath, 'r');
            
            curl_setopt($ch, CURLOPT_URL, $sftpUrl);
            curl_setopt($ch, CURLOPT_USERPWD, "{$this->sftpUsername}:{$this->sftpPassword}");
            curl_setopt($ch, CURLOPT_UPLOAD, 1);
            curl_setopt($ch, CURLOPT_INFILE, $fp);
            curl_setopt($ch, CURLOPT_INFILESIZE, filesize($pdfFilePath));
            curl_setopt($ch, CURLOPT_FTP_CREATE_MISSING_DIRS, 1); // Auto-create directory structure
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($ch, CURLOPT_TIMEOUT, 60);

            $result = curl_exec($ch);
            $error = curl_error($ch);
            $info = curl_getinfo($ch);
            
            curl_close($ch);
            fclose($fp);

            if ($result) {
                Log::info("NDML SFTP upload success for PAN: " . $cleanPan);
                return true;
            } else {
                Log::error("NDML SFTP upload failed: " . $error, ['http_code' => $info['http_code']]);
                throw new Exception("SFTP upload failed: " . $error . " (cURL code/HTTP: " . $info['http_code'] . ")");
            }
        } catch (\Throwable $e) {
            Log::error("NDML SFTP upload exception: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Build the standard registration & modification request XML payload.
     */
    protected function buildRegistrationXml($user, $kycVerification, string $flag): string
    {
        $pan = strtoupper(trim($user->pan_card));
        $name = strtoupper(trim($user->pan_card_name ?? $user->name));
        $fatherName = strtoupper(trim($user->father_name ?? ''));
        
        $dob = $user->dob ? $user->dob->format('d-m-Y') . ' 00:00:00' : '01-01-1900 00:00:00';
        
        $mobile = substr(trim($user->phone), -10);
        $email = strtoupper(trim($user->email));

        // Digio KYC specifics mapping if available
        $kycDetails = $kycVerification->kyc_details ?? [];
        $aadhaarDetails = $kycVerification->aadhaar_details ?? [];

        $gender = ($user->gender === 'female' || ($aadhaarDetails['gender'] ?? '') === 'female') ? 'F' : 'M';
        $marital = $user->marital_status === 'married' ? '02' : '01'; // 01 Single, 02 Married
        
        $address1 = strtoupper(trim(substr($user->address ?? 'N/A', 0, 80)));
        $address2 = strtoupper(trim(substr($user->address ?? '', 80, 80)));
        $address3 = strtoupper(trim(substr($user->address ?? '', 160, 80)));
        $city = strtoupper(trim($user->city ?? 'N/A'));
        $state = '009'; // Defaulting to Maharashtra/standard state code, can be mapped from sheet
        $pincode = trim($user->pincode ?? '400001');

        $reqDate = date('d-m-Y H:i:s');
        $batchId = 'ACC' . substr(time() . rand(10, 99), -12);

        $xml = "<APP_REQ_ROOT>
    <APP_PAN_INQ>
        <APP_IOP_FLG>{$flag}</APP_IOP_FLG>
        <APP_POS_CODE>{$this->bpId}</APP_POS_CODE>
        <APP_TYPE>I</APP_TYPE>
        <APP_NO></APP_NO>
        <APP_DATE></APP_DATE>
        <APP_PAN_NO>{$pan}</APP_PAN_NO>
        <APP_PANEX_NO></APP_PANEX_NO>
        <APP_PAN_COPY>Y</APP_PAN_COPY>
        <APP_EXMT>N</APP_EXMT>
        <APP_EXMT_CAT></APP_EXMT_CAT>
        <APP_KYC_MODE>5</APP_KYC_MODE> <!-- 5 = Digilocker KYC -->
        <APP_EXMT_ID_PROOF>02</APP_EXMT_ID_PROOF>
        <APP_IPV_FLAG>Y</APP_IPV_FLAG>
        <APP_IPV_DATE>{$reqDate}</APP_IPV_DATE>
        <APP_GEN>{$gender}</APP_GEN>
        <APP_NAME>{$name}</APP_NAME>
        <APP_F_NAME>{$fatherName}</APP_F_NAME>
        <APP_REGNO></APP_REGNO>
        <APP_DOB_DT>{$dob}</APP_DOB_DT>
        <APP_DOI_DT>{$dob}</APP_DOI_DT>
        <APP_COMMENCE_DT></APP_COMMENCE_DT>
        <APP_NATIONALITY>01</APP_NATIONALITY>
        <APP_OTH_NATIONALITY></APP_OTH_NATIONALITY>
        <APP_COMP_STATUS></APP_COMP_STATUS>
        <APP_OTH_COMP_STATUS></APP_OTH_COMP_STATUS>
        <APP_RES_STATUS>R</APP_RES_STATUS>
        <APP_RES_STATUS_PROOF></APP_RES_STATUS_PROOF>
        <APP_UID_NO></APP_UID_NO>
        <APP_COR_ADD1>{$address1}</APP_COR_ADD1>
        <APP_COR_ADD2>{$address2}</APP_COR_ADD2>
        <APP_COR_ADD3>{$address3}</APP_COR_ADD3>
        <APP_COR_CITY>{$city}</APP_COR_CITY>
        <APP_COR_PINCD>{$pincode}</APP_COR_PINCD>
        <APP_COR_STATE>{$state}</APP_COR_STATE>
        <APP_COR_CTRY>101</APP_COR_CTRY>
        <APP_OFF_NO></APP_OFF_NO>
        <APP_RES_NO></APP_RES_NO>
        <APP_MOB_NO>91{$mobile}</APP_MOB_NO>
        <APP_FAX_NO></APP_FAX_NO>
        <APP_EMAIL>{$email}</APP_EMAIL>
        <APP_COR_ADD_PROOF>26</APP_COR_ADD_PROOF>
        <APP_COR_ADD_REF></APP_COR_ADD_REF>
        <APP_COR_ADD_DT></APP_COR_ADD_DT>
        <APP_PER_ADD1>{$address1}</APP_PER_ADD1>
        <APP_PER_ADD2>{$address2}</APP_PER_ADD2>
        <APP_PER_ADD3>{$address3}</APP_PER_ADD3>
        <APP_PER_CITY>{$city}</APP_PER_CITY>
        <APP_PER_PINCD>{$pincode}</APP_PER_PINCD>
        <APP_PER_STATE>{$state}</APP_PER_STATE>
        <APP_PER_CTRY>101</APP_PER_CTRY>
        <APP_PER_ADD_PROOF>26</APP_PER_ADD_PROOF>
        <APP_PER_ADD_REF></APP_PER_ADD_REF>
        <APP_PER_ADD_DT></APP_PER_ADD_DT>
        <APP_INCOME>05</APP_INCOME>
        <APP_OCC>04</APP_OCC>
        <APP_OTH_OCC></APP_OTH_OCC>
        <APP_POL_CONN>NA</APP_POL_CONN>
        <APP_DOC_PROOF>E</APP_DOC_PROOF>
        <APP_INTERNAL_REF></APP_INTERNAL_REF>
        <APP_BRANCH_CODE></APP_BRANCH_CODE>
        <APP_MAR_STATUS>{$marital}</APP_MAR_STATUS>
        <APP_NETWRTH></APP_NETWRTH>
        <APP_NETWORTH_DT></APP_NETWORTH_DT>
        <APP_INCORP_PLC></APP_INCORP_PLC>
        <APP_OTHERINFO></APP_OTHERINFO>
        <APP_FILLER1></APP_FILLER1>
        <APP_FILLER2></APP_FILLER2>
        <APP_FILLER3></APP_FILLER3>
        <APP_DUMP_TYPE></APP_DUMP_TYPE>
        <APP_KRA_INFO></APP_KRA_INFO>
        <APP_SIGNATURE></APP_SIGNATURE>
        <APP_FATCA_APPLICABLE_FLAG>N</APP_FATCA_APPLICABLE_FLAG>
        <APP_FATCA_BIRTH_PLACE></APP_FATCA_BIRTH_PLACE>
        <APP_FATCA_BIRTH_COUNTRY></APP_FATCA_BIRTH_COUNTRY>
        <APP_FATCA_COUNTRY_RES></APP_FATCA_COUNTRY_RES>
        <APP_FATCA_COUNTRY_CITYZENSHIP></APP_FATCA_COUNTRY_CITYZENSHIP>
        <APP_FATCA_DATE_DECLARATION></APP_FATCA_DATE_DECLARATION>
    </APP_PAN_INQ>
    <APP_SUMM_REC>
        <APP_REQ_DATE>" . date('d-m-Y') . "</APP_REQ_DATE>
        <APP_OTHKRA_BATCH>{$batchId}</APP_OTHKRA_BATCH>
        <APP_OTHKRA_CODE>{$this->bpId}</APP_OTHKRA_CODE>
        <APP_RESPONSE_DATE></APP_RESPONSE_DATE>
        <APP_TOTAL_REC>1</APP_TOTAL_REC>
        <NO_OF_FATCA_ADDL_DTLS_RECORDS>0</NO_OF_FATCA_ADDL_DTLS_RECORDS>
    </APP_SUMM_REC>
</APP_REQ_ROOT>";

        return $xml;
    }
}

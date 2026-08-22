<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log; // Logging ke liye
use SoapClient;
use Exception;

class KraServiceController extends Controller
{
    private $userId;
    private $password;
    private $bpId;
    private $statusWsdl;

    public function __construct()
    {
        $this->userId = env('NDML_USER_ID');
        $this->password = env('NDML_PASSWORD');
        $this->bpId = env('NDML_BP_ID');
        $this->statusWsdl = env('NDML_WSDL_STATUS');
    }


  

    // public function getKycStatus(Request $request, $pan) 
    // {
    //         try {
    //             // 1. SSL Bypass Context (Localhost par connection error se bachne ke liye)
    //             $context = stream_context_create([
    //                 'ssl' => [
    //                     'verify_peer' => false,
    //                     'verify_peer_name' => false,
    //                     'allow_self_signed' => true
    //                 ]
    //             ]);

    //             // 2. Client Initialize karna
    //             $client = new \SoapClient($this->statusWsdl, [
    //                 'trace' => 1,
    //                 'exceptions' => true,
    //                 'stream_context' => $context // SSL settings apply ki
    //             ]);

    //             // 3. --- SARE FUNCTIONS DEKHNE KE LIYE ---
    //             // Ye line screen par list print kar degi aur code yahi ruk jayega
    //             dd($client->__getFunctions()); 

    //             // Niche ka code abhi nahi chalega jab tak upar wala dd() active hai
    //             $params = [
    //                 'userId'   => $this->userId,
    //                 'password' => $this->password,
    //                 'bpId'     => $this->bpId,
    //                 'pan'      => strtoupper($pan)
    //             ];

    //             $response = $client->getPANStatus($params); 

    //         } catch (\Exception $e) {
    //             dd("Error: " . $e->getMessage());
    //         }
    // }



    /**
     * PAN ka KYC Status check karne ke liye main function
     */
// public function getKycStatus(Request $request, $pan)
// {
//     try {
//         // 1. SSL Bypass Context
//         $context = stream_context_create([
//             'ssl' => [
//                 'verify_peer' => false,
//                 'verify_peer_name' => false,
//                 'allow_self_signed' => true
//             ],
//             'http' => [
//                 'protocol_version' => 1.1,
//                 'connection_timeout' => 30,
//             ]
//         ]);

//         // 2. SoapClient Setup (No manual location override)
//         $client = new \SoapClient($this->statusWsdl, [
//             'trace' => 1,
//             'exceptions' => true,
//             'connection_timeout' => 30,
//             'stream_context' => $context,
//             'cache_wsdl' => WSDL_CACHE_NONE,
//             'soap_version' => SOAP_1_1
//         ]);

//         // 3. Parameters Structure (As per WSDL requirement)
//         // NDML Inquiry function mangta hai ek 'parameters' object
//         $params = [
//             'parameters' => [
//                 'userId'   => $this->userId,
//                 'password' => $this->password,
//                 'bpId'     => $this->bpId,
//                 'pan'      => strtoupper($pan)
//             ]
//         ];

//         // 4. API Call
//         $response = $client->panInquiryDetails($params);

//         return response()->json([
//             'success' => true,
//             'message' => 'Status retrieved successfully',
//             'data' => $response
//         ]);

//     } catch (\Exception $e) {
//         return response()->json([
//             'success' => false,
//             'message' => 'Error: ' . $e->getMessage(),
//             'debug_info' => [
//                 'last_request' => isset($client) ? $client->__getLastRequest() : 'N/A',
//                 'last_response' => isset($client) ? $client->__getLastResponse() : 'N/A'
//             ]
//         ], 500);
//     }
// }



public function getKycStatus(Request $request, $pan)
{
    // 1. Production URLs
    $wsdl = "https://kra.ndml.in/sms-ws/PANServiceImplService/PANServiceImplService.wsdl";
    $location = "https://kra.ndml.in/sms-ws/PANServiceImplService";

    // 2. Live Diagnostics (Dynamic)
    $ndmlHost = parse_url($location, PHP_URL_HOST);
    $ndmlLiveIp = gethostbyname($ndmlHost); // NDML ki Live IP detect karega
    
    // Aapke khud ke server ki Public IP detect karne ke liye
    $myServerIp = file_get_contents('https://api.ipify.org') ?: 'Could not detect';

    try {
        $context = stream_context_create([
            'ssl' => [
                'verify_peer' => false, // Production mein agar SSL error aaye toh false rakhein, warna true karein
                'verify_peer_name' => false,
                'allow_self_signed' => true
            ],
            'http' => ['timeout' => 30]
        ]);

        // 3. SoapClient with Production Settings
        $client = new \SoapClient($wsdl, [
            'location' => $location, // Force Production HTTPS Endpoint
            'trace' => 1,
            'exceptions' => true,
            'stream_context' => $context,
            'soap_version' => SOAP_1_1,
            'cache_wsdl' => WSDL_CACHE_NONE,
        ]);

        $params = [
            'parameters' => [
                'userId'   => env('NDML_USER_ID'),
                'password' => env('NDML_PASSWORD'),
                'bpId'     => env('NDML_BP_ID'),
                'pan'      => strtoupper($pan)
            ]
        ];

        // 4. API Call
        $result = $client->panInquiryDetailsThree($params);

        // 5. XML Parsing
        $cleanXml = htmlspecialchars_decode($result->return);
        $xmlObject = simplexml_load_string($cleanXml);

        return response()->json([
            'success' => true,
            'network_info' => [
                'ndml_production_host' => $ndmlHost,
                'ndml_live_ip' => $ndmlLiveIp,
                'your_server_ip' => $myServerIp, // Ye aapke server ki current public IP dikhayega
                'connection_type' => 'HTTPS (Port 443)'
            ],
            'kyc_data' => $xmlObject
        ]);

    } catch (\SoapFault $e) {
        return response()->json([
            'success' => false,
            'message' => 'Production Connection Failed',
            'error_detail' => $e->getMessage(),
            'network_info' => [
                'ndml_live_ip' => $ndmlLiveIp,
                'your_server_ip' => $myServerIp
            ]
        ], 500);
    } catch (\Exception $e) {
        return response()->json(['success' => false, 'message' => $e->getMessage()]);
    }
}




public function testRawCredentials()
{
    // NDML Pilot Inquiry ka sabse common endpoint ye hota hai
    // Humne .wsdl aur duplicate name hata diya hai
    $url = "https://pilot.kra.ndml.in/sms-ws/PANServiceImplService"; 
    
    $userId = env('NDML_USER_ID');
    $password = env('NDML_PASSWORD');
    $miId = "B1465"; // Aapki MI ID yahan direct de raha hoon test ke liye
    $pan = "BQPPS7740A"; // Valid Test PAN

    // XML structure jisme MI ID 'bpId' wale tag mein jayegi
    $xmlRequest = '<?xml version="1.0" encoding="UTF-8"?>
    <soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ser="http://service.webservice.pan.kra.ndml.com/">
       <soapenv:Header/>
       <soapenv:Body>
          <ser:panInquiryDetails>
             <parameters>
                <bpId>' . $miId . '</bpId>
                <pan>' . $pan . '</pan>
                <password>' . $password . '</password>
                <userId>' . $userId . '</userId>
             </parameters>
          </ser:panInquiryDetails>
       </soapenv:Body>
    </soapenv:Envelope>';

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $xmlRequest);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: text/xml; charset=utf-8',
        'SOAPAction: ""'
    ]);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 20);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    return response()->json([
        'http_status' => $httpCode,
        'mi_id_used' => $miId,
        'endpoint_hit' => $url,
        'raw_response' => $response, // Agar data aaya toh yahan XML dikhega
    ]);
}
}
<?php 

require(dirname(__FILE__) . '/../../../wp-config.php');

require './vendor/autoload.php';

use OrderDetect\Helper;

// define('ORDERDETECT_ENCRYPTION_KEY', 'yE7VLwfyweOTwWyxQgjNcxgArStNUARmkHVvsF3j4eU=');
// define('ORDERDETECT_IV', 'sq/gQejtmYczi99rYa61hA==');

// $data = "4cc94dc94ade8a1c5f996d5f744ec842";
// $data = "lifetime";

// echo $enc = Helper::encrypt_data($data, ORDERDETECT_ENCRYPTION_KEY, ORDERDETECT_IV);
// echo "<br>";
// echo $dsc = Helper::decrypt_data($enc, ORDERDETECT_ENCRYPTION_KEY, ORDERDETECT_IV);

$api_key = '350|D930ek0Gb5sTsgBC9cx2HZygNbBunz1OAEu5Uk2I ';
$sender_id = get_option('esms_notify_sender_id');

// Set up the cURL request
$url = 'https://login.esms.com.bd/api/v3/sms/send';
$data = [
    'recipient' =>  '8801745468682',
    'sender_id' => 8809601001337,
    'type' => 'plain',
    'message' => 'This is a test message',
];

$headers = [
    'Accept: application/json',
    'Authorization: Bearer ' . $api_key,
    'Content-Type: application/json',
];

$curl = curl_init($url);
curl_setopt($curl, CURLOPT_POST, true);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));

// For debug only, disable SSL verification (not recommended for production)
curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

$response = curl_exec($curl);

// Log the request and response for debugging
error_log('SMS request data: ' . print_r($data, true));
error_log('SMS response: ' . print_r($response, true));

if (curl_errno($curl)) {
    error_log('SMS send error: ' . curl_error($curl));
} else {
    error_log('SMS sent successfully: ' . $response);
}

curl_close($curl);

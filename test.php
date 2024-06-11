<?php 

require(dirname(__FILE__) . '/../../../wp-config.php');

require './vendor/autoload.php';

use OrderDetect\Helper;

// define('ORDERDETECT_ENCRYPTION_KEY', 'yE7VLwfyweOTwWyxQgjNcxgArStNUARmkHVvsF3j4eU=');
// define('ORDERDETECT_IV', 'sq/gQejtmYczi99rYa61hA==');

$data = "4cc94dc94ade8a1c5f996d5f744ec842";

 echo $enc = Helper::encrypt_data($data, ORDERDETECT_ENCRYPTION_KEY, ORDERDETECT_IV);
// echo "<br>";\
$dsc = Helper::decrypt_data($enc, ORDERDETECT_ENCRYPTION_KEY, ORDERDETECT_IV);
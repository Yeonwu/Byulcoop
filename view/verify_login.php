<?php
//sudo chmod 775 -R google-api-php-client--PHP7.0

header('Content-Type: application/json; charset=utf8');

require_once "./config.php";
require_once "./google-api-php-client--PHP7.0/vendor/autoload.php";

$id_token = $_POST['idtoken'];

$client = new Google_Client(['client_id' => GOOGLE_CLIENT_ID]);  // Specify the CLIENT_ID of the app that accesses the backend
$payload = $client -> verifyIdToken($id_token);


if ($payload) {
	$verify_token = hash('sha256', $payload['email']);
	$json = array('success' => TRUE, 'verify_token' => $verify_token);
} else {
    $json = array('success' => false);
}

echo json_encode($json, JSON_UNESCAPED_UNICODE);

?>
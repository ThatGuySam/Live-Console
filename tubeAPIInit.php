<?php
/*
 * You can acquire an OAuth 2.0 client ID and client secret from the
 * {{ Google Cloud Console }} <{{ https://cloud.google.com/console }}>
 * For more information about using OAuth 2.0 to access Google APIs, please see:
 * <https://developers.google.com/youtube/v3/guides/authentication>
 * Please ensure that you have enabled the YouTube Data API for your project.
 */

include('config.php');

$client = new Google_Client();
$client->setClientId($OAUTH2_CLIENT_ID);
$client->setClientSecret($OAUTH2_CLIENT_SECRET);
$client->setScopes('https://www.googleapis.com/auth/youtube');
$client->setApplicationName("Guts Live Console");//
$redirect = filter_var('http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'],
  FILTER_SANITIZE_URL);
$client->setRedirectUri($redirect);
$client->setAccessType('offline');//

// Define an object that will be used to make all API requests.
$youtube = new Google_Service_YouTube($client);

if (isset($_GET['code'])) {
  if (strval($_SESSION['state']) !== strval($_GET['state'])) {
    die('The session state did not match.');
  }

  $client->authenticate($_GET['code']);
  $_SESSION['token'] = $client->getAccessToken();
  header('Location: ' . $redirect);
}

if (isset($_SESSION['token'])) {
	
	$client->setAccessToken($_SESSION['token']);
	
/*
	if ($client->isAccessTokenExpired()) {
	    $currentTokenData = json_decode($_SESSION['token']);
	    if (isset($currentTokenData->refresh_token)) {
	        $client->refreshToken($tokenData->refresh_token);
	    }
	}
*/
	
//	$sessionToken = json_decode($_SESSION['token']);
	
/*
	$this->Cookie->write('token', $sessionToken->refresh_token, false, '1 month');
	
	$cookie = $this->Cookie->read('token');

	if(!empty($cookie)){
	    $client->refreshToken($this->Cookie->read('token'));
	}
*/
}





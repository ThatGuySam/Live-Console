<?php

//Load Testing
//sleep(10);

include('functions.php');

if( !isset( $_POST['broadcast-date'] ) ){
	die('Please add a start time');
}

	$raw_date = $_POST['broadcast-date']." ".$_POST['broadcast-time'];
	
	$start = strtotime( $raw_date );
	
	//die();
	
if( !isset( $_POST['broadcast-title'] ) || $_POST['broadcast-title'] == "" ){
	$_POST['broadcast-title'] = date("l F jS ga", $start)." | ".$church_name;
}

// Call set_include_path() as needed to point to your client library.
require_once 'Google/Client.php';
require_once 'Google/Service/YouTube.php';
session_start();


$client = new Google_Client();
$client->setClientId($OAUTH2_CLIENT_ID);
$client->setClientSecret($OAUTH2_CLIENT_SECRET);
$client->setScopes('https://www.googleapis.com/auth/youtube');
$redirect = filter_var('http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'],
    FILTER_SANITIZE_URL);
$client->setRedirectUri($redirect);

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
}

// Check to ensure that the access token was successfully acquired.
if ($client->getAccessToken()) {
  try {
    // Create an object for the liveBroadcast resource's snippet. Specify values
    // for the snippet's title, scheduled start time, and scheduled end time.
    $broadcastSnippet = new Google_Service_YouTube_LiveBroadcastSnippet();
    $broadcastSnippet->setTitle($_POST['broadcast-title']);
    $broadcastSnippet->setScheduledStartTime(date("Y-m-d", $start).'T'.date("H:i:00", $start).'.000Z');
	
	
/*
	$thumbnails = new Google_Service_YouTube_ThumbnailDetails();
	
	$dropbox_thumbnail = new Google_Service_YouTube_Thumbnail();
	
	$dropbox_thumbnail->setUrl("https://dl.dropboxusercontent.com/1/view/6jptgnv8yplnszu/Slides/Giving%20Slates/0714_GUTS_GivingToChangedLives_NY_Final.jpg");
	
	$dropbox_thumbnail->setWidth("1280");
	
	$dropbox_thumbnail->setHeight("1280");
	
	$thumbnails->setHigh($dropbox_thumbnail);
	
	
	$broadcastSnippet->setThumbnails($thumbnails);
*/
	
	
    //$broadcastSnippet->setDescription($_POST['broadcast-description']);
    //$broadcastSnippet->setScheduledEndTime('2014-05-31T00:00:00.000Z');

    // Create an object for the liveBroadcast resource's status, and set the
    // broadcast's status to "private".
    $status = new Google_Service_YouTube_LiveBroadcastStatus();
    $status->setPrivacyStatus($_POST['broadcast-privacy']);

    // Create the API request that inserts the liveBroadcast resource.
    $broadcastUpdate = new Google_Service_YouTube_LiveBroadcast();
    $broadcastUpdate->setId( $_POST['broadcast-id'] );
    $broadcastUpdate->setSnippet($broadcastSnippet);
    $broadcastUpdate->setStatus($status);
    $broadcastUpdate->setKind('youtube#liveBroadcast');

    // Execute the request and return an object that contains information
    // about the new broadcast.
    $broadcastsResponse = $youtube->liveBroadcasts->update('id,snippet,status',
        $broadcastUpdate, array());
        
    //debug( $broadcastsResponse );

    // Create an object for the liveStream resource's snippet. Specify a value
    // for the snippet's title.
/*
    $streamSnippet = new Google_Service_YouTube_LiveStreamSnippet();
    $streamSnippet->setTitle('New Stream');
*/

    // Create an object for content distribution network details for the live
    // stream and specify the stream's format and ingestion type.
/*
    $cdn = new Google_Service_YouTube_CdnSettings();
    $cdn->setFormat("1080p");
    $cdn->setIngestionType('rtmp');
*/

    // Create the API request that inserts the liveStream resource.
/*
    $streamInsert = new Google_Service_YouTube_LiveStream();
    $streamInsert->setSnippet($streamSnippet);
    $streamInsert->setCdn($cdn);
    $streamInsert->setKind('youtube#liveStream');
*/

    // Execute the request and return an object that contains information
    // about the new stream.
/*
    $streamsResponse = $youtube->liveStreams->insert('snippet,cdn',
        $streamInsert, array());
*/

    // Bind the broadcast to the live stream.
/*
    $bindBroadcastResponse = $youtube->liveBroadcasts->bind(
        $broadcastsResponse['id'],'id,contentDetails',
        array(
            'streamId' => $streamsResponse['id'],
        ));
*/

    $htmlBody .= "<h3>Added Broadcast</h3><ul>";
    $htmlBody .= sprintf('<li>%s published at %s (%s)</li>',
        $broadcastsResponse['snippet']['title'],
        $broadcastsResponse['snippet']['publishedAt'],
        $broadcastsResponse['id']);
    $htmlBody .= '</ul>';

/*
    $htmlBody .= "<h3>Added Stream</h3><ul>";
    $htmlBody .= sprintf('<li>%s (%s)</li>',
        $streamsResponse['snippet']['title'],
        $streamsResponse['id']);
    $htmlBody .= '</ul>';

    $htmlBody .= "<h3>Bound Broadcast</h3><ul>";
    $htmlBody .= sprintf('<li>Broadcast (%s) was bound to stream (%s).</li>',
        $bindBroadcastResponse['id'],
        $bindBroadcastResponse['contentDetails']['boundStreamId']);
    $htmlBody .= '</ul>';
*/

  } catch (Google_ServiceException $e) {
    $htmlBody .= sprintf('<p>A service error occurred: <code>%s</code></p>',
        htmlspecialchars($e->getMessage()));
  } catch (Google_Exception $e) {
    $htmlBody .= sprintf('<p>An client error occurred: <code>%s</code></p>',
        htmlspecialchars($e->getMessage()));
  }

  $_SESSION['token'] = $client->getAccessToken();
} else {
  // If the user hasn't authorized the app, initiate the OAuth flow
  $state = mt_rand();
  $client->setState($state);
  $_SESSION['state'] = $state;

  $authUrl = $client->createAuthUrl();
  $htmlBody = <<<END
  <h3>Authorization Required</h3>
  <p>You need to <a href="$authUrl">authorize access</a> before proceeding.<p>
END;
}
?>
  <?=$htmlBody?>
  <div class="panel-group" id="accordion">
      <div class="panel-default">
          	<div class="fieldgroup">
	            <div class="topcoat-button-bar">
					<div class="topcoat-button-bar__item">
						<a href="#collapseOne" data-toggle="collapse" data-parent="#accordion" class="topcoat-button-bar__button">Raw</a>
					</div>
				</div>
          	</div>

        <div id="collapseOne" class="panel-collapse collapse">
          <div class="panel-body">
            <pre>
            <?php print_r($_POST); ?>
            </pre>
          </div>
        </div>
      </div>
</div>
<?php

function debug($str) {
	echo "<pre>";
	print_r($str);
	echo "</pre>";
}


//Let's get some info
include('config.php');

function aasort (&$array, $key) {
    $sorter=array();
    $ret=array();
    reset($array);
    foreach ($array as $ii => $va) {
        $sorter[$ii]=$va[$key];
    }
    asort($sorter);
    foreach ($sorter as $ii => $va) {
        $ret[$ii]=$array[$ii];
    }
    $array=$ret;
}


function tubeIncludes() {
	// Call set_include_path() as needed to point to your client library.
	require_once 'Google/Client.php';
	require_once 'Google/Service/YouTube.php';
}


function parseAPIItems( $broadcastsResponse , $time_offset ) {

	$htmlBody = "";
	
	
	//Put into a managable array
	$broadcasts = array();
	
	$i = 0;
	foreach ($broadcastsResponse['items'] as $broadcastItem ) {
		
//		if( $broadcastItem['id'] == "UoAbkVjvgxY" || $broadcastItem['id'] == "YVsBEY_bLys" ) debug( $broadcastItem );
		
		//Setup	data
		$broadcast_info = array(
			"id"  => $broadcastItem['id'],
			"title" => $broadcastItem['snippet']['title'],
			"description" => $broadcastItem['snippet']['description'],
			"start" => date( "U" , strtotime( $broadcastItem['snippet']['scheduledStartTime'] ) ),
			"status" => $broadcastItem['status']['lifeCycleStatus']
		);
		
		//Put into array
		$broadcasts[$i]['title'] = $broadcastItem['snippet']['title'];
		
		$broadcasts[$i]['start'] = date( "U" , strtotime( $broadcastItem['snippet']['scheduledStartTime'] )+$time_offset );
		
		$broadcasts[$i]['id'] = $broadcastItem['id'];
		
		$broadcasts[$i]['jsonData'] = htmlentities( str_replace('"',"'", json_encode($broadcast_info) ) );
		
		$i++;
	}
	
	
	//Chronologize that sh!
	usort($broadcasts, function($a, $b) {
	    return $a['start'] - $b['start'];
	});
	
	
	//Put some HTML on it
	foreach ( $broadcasts as $broadcast ) {
    

    	
	ob_start(); ?>
	
      	<li>
      		
      		<div class="topcoat-button-bar edit-bar" data-info="<?php echo $broadcast['jsonData']; ?>">
      		  
      		  
      		  <div class="topcoat-button-bar__item">
			    <a href="" class="topcoat-button-bar__button info hint" data-hint="Info" alt="Info">
			    	<i class="fa fa-info-circle"></i>
			    </a>
			  </div>
			  
			  <div class="topcoat-button-bar__item">
			    <a href="//www.youtube.com/live_event_analytics?v=<?php echo $broadcast['id']; ?>" target="_blank" class="topcoat-button-bar__button hint" data-hint="Live Control Room" alt="Live Control Room">
			    	<i class="fa fa-youtube-play fa-lg"></i>
			    </a>
			  </div>
      		  
      		  <div class="topcoat-button-bar__item">
			    <button class="topcoat-button-bar__button hint" data-hint="<?php echo date("D, M jS ga" ,  $broadcast['start'] ); ?>">
			    	<span class="hidden-xs"><?php echo $broadcast['title']; ?></span>
			    	<span class="visible-xs"><?php echo date("D n-j ga" ,  $broadcast['start'] ); ?></span>
			    </button>
			  </div>
			  
			  <div class="topcoat-button-bar__item">
			    <a href="" class="topcoat-button-bar__button duplicate hint" data-hint="Duplicate" alt="Duplicate">
			    	<i class="fa fa-files-o"></i>
			    </a>
			  </div>
			  
			  <div class="topcoat-button-bar__item">
			    <a href="" class="topcoat-button-bar__button edit hint" data-hint="Edit" alt="Edit">
			    	<i class="fa fa-pencil-square-o"></i>
			    </a>
			  </div>
			  
			  <div class="topcoat-button-bar__item">
		    	<a href="" class="set-link topcoat-button-bar__button action hint" data-hint="Set Link" data-default-url="https://www.youtube.com/watch?v=<?php echo $broadcast['id'] ?>" >
	      			Set
	      		</a>
			  </div>
			  
			</div>
		</li>
		
	<?php $htmlBody .= ob_get_clean();
          
    }
    
    return $htmlBody;
	
}



function broadcasts() {
	
	//Do all this API stuff so I don't have to
	
	include('tubeAPIInit.php');

// Check to ensure that the access token was successfully acquired.

if ($client->getAccessToken()) {
  try {
    // Execute an API request that lists broadcasts owned by the user who
    // authorized the request.
    
    //Get Currently Live Events
    $liveBroadcasts = $youtube->liveBroadcasts->listLiveBroadcasts(
        'id,snippet,contentDetails,status',
        array(
            'broadcastStatus' => 'active',
            'maxResults' => 10
        ));
    
    //Get Upcoming Events
    $upcomingBroadcasts = $youtube->liveBroadcasts->listLiveBroadcasts(
        'id,snippet,contentDetails,status',
        array(
            'broadcastStatus' => 'upcoming',
            'maxResults' => 10
        ));
        
//	$token = $client->getAccessToken();
    
//	$token = json_decode( $token , true );
    
//	$htmlBody .= $token['access_token'];
    
//	$htmlBody .= '<a href="https://accounts.google.com/o/oauth2/revoke?token='.$token['access_token'].'">Logout Youtube</a>';
        
    
    //Dress Currently Live Events with HTML and add them to $htmlBody
    $htmlBody .= parseAPIItems( $liveBroadcasts , $time_offset );
    
    $htmlBody .= parseAPIItems( $upcomingBroadcasts , $time_offset );
    

    

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
  <a href="$authUrl" class="topcoat-button--cta">Authorize</a>
END;
}

/*
if($client->isAccessTokenExpired()) {

    $authUrl = $client->createAuthUrl();
    header('Location: ' . filter_var($authUrl, FILTER_SANITIZE_URL));

}
*/

	return $htmlBody;
}




function streams() {
	
	//Do all this API stuff so I don't have to
	
	include('tubeAPIInit.php');

// Check to ensure that the access token was successfully acquired.

if ($client->getAccessToken()) {
  try {
    // Execute an API request that lists broadcasts owned by the user who
    // authorized the request.
    
    
    
    //Get Currently Live Events
/*
    $liveBroadcasts = $youtube->liveBroadcasts->listLiveBroadcasts(
        'id,snippet,contentDetails,status',
        array(
            'broadcastStatus' => 'active',
            'maxResults' => 50
        ));
*/
    
    //Get Upcoming Events
    $upcomingBroadcasts = $youtube->liveBroadcasts->listLiveBroadcasts(
        'id,snippet,contentDetails,status',
        array(
            'broadcastStatus' => 'upcoming',
            'maxResults' => 50,
            'pageToken' => 'CAUQAA'
        ));
    
    
    $streamsResponse = $youtube->liveStreams->listLiveStreams('id,snippet', array(
        'mine' => 'true',
        'maxResults' => 50,
    ));

/*
    $htmlBody .= "<h3>Live Streams</h3><ul>";
    foreach ($streamsResponse['items'] as $streamItem) {
      $htmlBody .= sprintf('<li>%s (%s)</li>', $streamItem['snippet']['title'],
          $streamItem['id']);
    }
*/


    $htmlBody = $streamsResponse;

/*
  } catch (Google_ServiceException $e) {
    $htmlBody .= sprintf('<p>A service error occurred: <code>%s</code></p>',
        htmlspecialchars($e->getMessage()));
  } catch (Google_Exception $e) {
    $htmlBody .= sprintf('<p>An client error occurred: <code>%s</code></p>',
        htmlspecialchars($e->getMessage()));
  }
*/



        
//	$token = $client->getAccessToken();
    
//	$token = json_decode( $token , true );
    
//	$htmlBody .= $token['access_token'];
    
//	$htmlBody .= '<a href="https://accounts.google.com/o/oauth2/revoke?token='.$token['access_token'].'">Logout Youtube</a>';
        
    
    //Dress Currently Live Events with HTML and add them to $htmlBody
/*
    $htmlBody .= parseAPIItems( $liveBroadcasts , $time_offset );
    
    $htmlBody .= parseAPIItems( $upcomingBroadcasts , $time_offset );
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
  <a href="$authUrl" class="topcoat-button--cta">Authorize</a>
END;
}

/*
if($client->isAccessTokenExpired()) {

    $authUrl = $client->createAuthUrl();
    header('Location: ' . filter_var($authUrl, FILTER_SANITIZE_URL));

}
*/

	return $htmlBody;
}




function streamsOld() {
	
	//Do all this API stuff so I don't have to
	
	include('tubeAPIInit.php');

// Check to ensure that the access token was successfully acquired.
if ($client->getAccessToken()) {
  try {
    // Execute an API request that lists the streams owned by the user who
    // authorized the request.
    $streamsResponse = $youtube->liveStreams->listLiveStreams('id,snippet', array(
        'mine' => 'true',
    ));

/*
    $htmlBody .= "<h3>Live Streams</h3><ul>";
    foreach ($streamsResponse['items'] as $streamItem) {
      $htmlBody .= sprintf('<li>%s (%s)</li>', $streamItem['snippet']['title'],
          $streamItem['id']);
    }
    $htmlBody .= '</ul>';
*/

	 $htmlBody = $streamsResponse;

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
  <a href="$authUrl" class="topcoat-button--cta">Authorize</a>
END;
}
	
	
//	$htmlBody = "Allo'";

	return $htmlBody;
	
}
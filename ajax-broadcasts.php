<?php 

include('functions.php');

//Get required Google libraries
tubeIncludes();

//Ya'll ready for this
session_start();


//Output for Youtube Videos
$htmlBody = broadcasts();

//$htmlBody = streams();

date_default_timezone_set($church_timezone);

//debug( $htmlBody );

echo $htmlBody;


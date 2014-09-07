<?php

function isMobile($user_agent=NULL) {
    if(!isset($user_agent)) {
        $user_agent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
    }
    return (
    	strpos($user_agent, 'Android') !== FALSE 
    	&& strpos($user_agent, 'Mobile') !== FALSE 
    	|| strpos($user_agent, 'iPhone') !== FALSE 
    	|| strpos($user_agent, 'iPad') !== FALSE
    	|| strpos($user_agent, 'iPod') !== FALSE 
    );
}

$qs = array();
$embed_data = file_get_contents("data.json");
$embed_data = json_decode($embed_data, TRUE);
$default_link = $embed_data['default-url'];
$mobile_link = $embed_data['mobile-url'];

if( $_SERVER['QUERY_STRING'] ) {
	parse_str($_SERVER['QUERY_STRING'], $qs);
}

if ( $embed_data['type'] == 'youtube' ) {
	
	if ( array_key_exists('campusmode', $qs) ) {
		$embed_data['params']['theme'] = 'dark';
		$default_link = 'http://youtube.com/embed/'.$embed_data['id'].'?'.http_build_query($embed_data['params']);
	} if ( array_key_exists('monitormode', $qs) ) {
		$embed_data['params']['theme'] = 'dark';
		$embed_data['params']['autohide'] = 0;
		$embed_data['params']['showinfo'] = 1;
		$default_link = 'http://youtube.com/embed/'.$embed_data['id'].'?'.http_build_query($embed_data['params']);
	} else {
		$default_link = $embed_data['youtube-url'];
	}
}

if( !isMobile() || $mobile_link == "" ) {
	header( 'Location: '.$default_link );
	//echo $default_link;
} else {
	header( 'Location: '.$mobile_link );
}
?>
<?php 
	include_once('///var/www/vhosts/nightmaretulsa.com/httpdocs/gutschurch/static/libraries/simplepie/autoloader.php');
	include_once('///var/www/vhosts/nightmaretulsa.com/httpdocs/gutschurch/static/libraries/simplepie/idna_convert.class.php');
	
	// We'll process this feed with all of the default options.
	$feed = new SimplePie('http://vimeo.com/album/2238693/rss');
	
	// Set which feed to process.
	 
	// Run SimplePie.
	$feed->init();
	 
	// This makes sure that the content is sent to the browser as text/html and the UTF-8 character set (since we didn't change it).
	$feed->handle_content_type();
	 
	// Let's begin our XHTML webpage code.  The DOCTYPE is supposed to be the very first thing, so we'll keep it on the same line as the closing-PHP tag.
	foreach ($feed->get_items() as $item):
 
		$link = $item->get_link();
		$url = explode("/",$link);
		$message_url = "//player.vimeo.com/video/".$url[3]."?byline=0&amp;portrait=0&amp;badge=0&amp;color=be2810";
		break;	

	endforeach;
	
	header( 'Location: '.$message_url ) ;

?>
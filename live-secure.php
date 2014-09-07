<?php

$embed_data = array();

/* Get links from post */
$embed_data['default-url'] = $_POST["default-url"];
$embed_data['mobile-url'] = $_POST["mobile-url"];

/* Does it have Youtubes? */
if ( $_POST["default-url"] ) {
	
	//Parse Youtubes
	//www.youtube.com/watch?v=f9RoGRPbUCU
	if (preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $embed_data['default-url'], $match)) {
		$embed_data['type'] = 'youtube';
		$embed_data['id'] = $match[1];
		
		
		//developers.google.com/youtube/player_parameters#Parameters
		$embed_data['params'] = array(
			'autoplay'=>'1',
			'rel'=>'0',
			'showinfo'=>'0',
			'modestbranding'=>'1',
			'theme'=>'light',
			'autohide'=>'1'
		);
		$embed_data['youtube-url'] = '//youtube.com/embed/'.$embed_data['id'].'?'.http_build_query($embed_data['params']);
	}
	
	/* Write that sucker to file */
	file_put_contents("data.json", json_encode($embed_data));
	
}

//Get info from file
$embed_data = file_get_contents("data.json");
$embed_data = json_decode($embed_data, TRUE);


//	$tube_streams = streams();
	
/*
	foreach( $tube_streams['data']['items'] as $tube_stream ){
	
		//if( $tube_stream['id'] == "S78ff45CIaae5P2rfBYp2A1403720796327704" ){
			
			debug( $tube_stream );
			
		//}
		
	}
*/

//debug( $tube_streams );

?>
<div class="container">
	
	<h1 class="hidden-xs">Guts Live Console</h1>
	
	<div class="row">
	
	<nav class="navbar navbar-default" role="navigation">
	  <div class="container-fluid">
	    <!-- Brand and toggle get grouped for better mobile display -->
	    <div class="navbar-header">
	      <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
	        <span class="sr-only">Toggle navigation</span>
	      </button>
	      <a class="navbar-brand visible-xs" href="#">Guts Live Console</a>
	    </div>
	
	    <!-- Collect the nav links, forms, and other content for toggling -->
	    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
	      <ul class="nav navbar-nav">
			  <li>
			  	<a href="#"></a>
			  </li>
	      </ul>
	
	      <ul class="nav navbar-nav navbar-right">
	        <li><a href="live-console.php?logout=1" >Logout</a></li>
	      </ul>
	    </div><!-- /.navbar-collapse -->
	  </div><!-- /.container-fluid -->
	</nav>
	
	</div><!--  .row  -->
	
	<div class="row">
		
		<div class="col-sm-6">
			<h4>Current Link</h4>
			<div class="videoWrapper link-preview">
				<iframe width="100%" height="100%" src="/embed/live.php?monitormode" frameborder="0" volume="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>
				<!--  -->
			</div>
			
			<h4>Info</h4>
			<hr>
			
			<div class="row">
				
				<div class="info-bar">
					
						
						<!-- Info Bar -->
						
						<div class="topcoat-button-bar">
							
							<div class="topcoat-button-bar__item">
								<a href="#raw-output" class="topcoat-button-bar__button">Raw</a>
							</div>
							
						</div>
						
						<ul class="info-list">
							  
						  <!-- If it's youtube quick link to youtube analytics -->
						  <?php if( $embed_data['type'] == "youtube" ): ?>
						  	<li>
							  	<a href="https://www.youtube.com/live_event_analytics?v=<?php echo $embed_data['id']; ?>" target="_blank" class=""><i class="fa fa-youtube-play fa-lg"></i></a>
						  	</li>
						  <?php endif; ?>
							  
						  <li>
							  <a href="<?php echo $embed_data['default-url']; ?>" target="_blank" class=""><i class="fa fa-desktop fa-lg"></i> <?php echo $embed_data['default-url']; ?></a>
						  </li>
							  
						  <!-- If it has a special mobile link show that too -->
						  <?php if( $embed_data['mobile-url'] && $embed_data['mobile-url'] !== "" ): ?>
						  	<li>
								<a href="<?php echo $embed_data['mobile-url']; ?>" target="_blank" class=""><i class="fa fa-mobile fa-lg"></i> <?php echo $embed_data['mobile-url']; ?></a>
						  	</li>
						  <?php endif; ?>
						  
						</ul><!-- .info-list -->
					
				</div><!-- .info-bar -->
				
			</div><!-- .row -->
			
		</div><!--  .col-sm-6  -->
		
		
		<div class="col-sm-6">
			
			
			
			<h4>Set Link</h4>
			<hr>
			
			<!-- Quick Set Link -->
				
				<div class="topcoat-button-bar quick-bar">
				
					<div class="topcoat-button-bar__item">
						<a href="" class="set-link topcoat-button-bar__button action" data-default-url="http://gutschurch.com/embed/latest-message.php">
							<i class="fa fa-vimeo-square fa-lg"></i> Latest<span class="hidden-xs"> Message</span>
						</a>
					</div>
					
					<div class="topcoat-button-bar__item">
						<a href="" class="set-link topcoat-button-bar__button action" data-default-url="http://player.piksel.com/playerlive.php?s=j48v4224&doResize=false" data-mobile-url="http://227493-lh.akamaihd.net/i/GUTSChurch_iOS@118651/master.m3u8">
							<i class="fa fa-empire fa-lg"></i> 316<span class="hidden-xs"> Mode</span>
						</a>
					</div>
					
					<div class="topcoat-button-bar__item">
						<a href="#custom-link" class="topcoat-button-bar__button">
							<i class="fa fa-link fa-lg"></i> Custom<span class="hidden-xs"> Link</span>
						</a>
					</div>
					
				</div>
				
				<h4>Youtube Broadcasts</h4>
				<hr>
				
				
				<div class="topcoat-button-bar edit-bar">
				
					<div class="topcoat-button-bar__item">
						<a href="#broadcast-bar" class="topcoat-button-bar__button hint" data-hint="New Broadcast" ><i class="fa fa-plus fa-lg"></i></a>
					</div>
					
					<div class="topcoat-button-bar__item">
						<button class="topcoat-button-bar__button refresh-broadcasts hint" data-hint="Refresh Broadcasts List" ><i class="fa fa-refresh fa-lg"></i></button>
					</div>
					
				</div>
				
				<ul class="tube-broadcasts">
					
					<?=$htmlBody?>
				
				</ul>
			
		</div><!--  .col-sm-6  -->
		
	</div><!--  .row  -->

</div><!--  .container  -->




	<!-- Slide Out Bars -->
	
	
	
	<div id="tooltips">
	
		<div id="raw-output" class="tip">
			<div>
				<div>
					<a href="#" class="close">Close</a>
					<h4>Raw URL Output</h4>
					<hr>
					<h5>Church: <?php echo $church_name; ?></h5>
					<h5>Time: <?php echo $church_timezone; ?></h5>
					<?php debug( $embed_data ); ?>
					<h4>Current Post Output</h4>
					<?php debug( $_POST ); ?>
				</div>
			</div>
		</div>
		
		<div id="video-info" class="tip">
			<div>
				<div>
					<a href="#" class="close">Close</a>
					<h4>Youtube Stream</h4>
					<hr>
					<div class="videoWrapper">
						<iframe class="video-info-frame" width="100%" height="100%" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen ></iframe>
					</div>
					<h4 class="video-title"></h4>
					<h4 class="video-date"></h4>
					<p class="video-description"></p>
				</div>
			</div>
		</div>
				
		<ul id="broadcast-bar" class="tip">
			<div>
				<div>
					<a href="#" class="close">Close</a>
					
					<h4>New Broadcast</h4>
					<hr>
					<form id="broadcast-form" method="post">
					
						<div class="fieldgroup">
							<label>Title</label>
							<input class="topcoat-text-input" type="text" id="broadcast-title" name="broadcast-title" placeholder="<?php echo date("l F jS ga")." | ".$church_name; ?>" value="" >
						</div>
						
						<div class="fieldgroup">
							<label>Date</label>
			            	<input class="topcoat-text-input" type="date" id="broadcast-date" name="broadcast-date" placeholder="Broadcast Date" value="<?php echo date("Y-m-d"); ?>" required />
						</div>
						
						<div class="fieldgroup">
							<label>Time</label>
			            	<input class="topcoat-text-input" type="time" id="broadcast-time" name="broadcast-time" placeholder="Broadcast Time" value="<?php echo date("H:00:00"); ?>" required />
			            	<input class="topcoat-text-input" type="hidden" id="broadcast-timezone" name="broadcast-timezone" required />
						</div>
						
						<div class="fieldgroup">
							<label>Description</label>
							<textarea class="topcoat-textarea" name="broadcast-description" rows="6" cols="36" placeholder="Description"></textarea>
						</div>
						
						<div class="fieldgroup">
							<select class="docNav" name="broadcast-privacy">
					            <option value="public" selected="selected">Public</option>
					            <option value="private">Private</option>
					            <option value="unlisted">Unlisted</option>
				            </select>
						</div>
						
						
						<div class="fieldgroup broadcast-thumbnail">
							<label>Thumbnail</label>
							<br>
							<div class="thumb-preview">
								<a href="#" class="topcoat-button dropbox-upload"><i class="fa fa-dropbox fa-lg"></i>Upload from Dropbox</a>
							</div>
							
							<div class="topcoat-button-bar thumb-bar hideme">
							
								<div class="topcoat-button-bar__item">
									<a href="#" class="topcoat-button-bar__button dropbox-upload hint" data-hint="Change Image" ><i class="fa fa-dropbox fa-lg"></i></a>
								</div>
								
								<div class="topcoat-button-bar__item">
									<a href="#" class="topcoat-button-bar__button clear-thumb hint" data-hint="Clear Image" ><i class="fa fa-times fa-lg"></i></a>
								</div>
								
							</div>
							
							<input class="topcoat-text-input" type="hidden" id="broadcast-thumb-link" name="broadcast-thumb-link" value="" />
							
						</div>
						
						<!-- Other Hidden Fields -->
						<div class="fieldgroup hideme">
			            	<input class="topcoat-text-input" type="hidden" id="broadcast-id" name="broadcast-id" />
						</div>
						
						<div class="fieldgroup">
							<input class="topcoat-button--cta" type="submit" value="Create">
							<input class="topcoat-button close-mm" type="button" value="Cancel">
						</div>
						
					</form>
					
					<div id="broadcast-result">
						<div class="result">
						
						</div>
						<div class="fieldgroup hideme">
							<input id="broadcast-done" class="topcoat-button close-mm" type="button" value="Done">
							<input class="topcoat-button topofbar broadcast-new" type="button" value="New">
						</div>
					</div>
					
					
				</div>
			</div>
		</ul><!-- #broadcast-bar -->		
		
		
		
		
		<!-- Custom Link -->
		
		<div id="custom-link" class="tip">
			<div>
				<div>
					<a href="#" class="close">Close</a>
					
					<h4>Custom Link</h4>
			
					<form action="live-console.php" method="post">
					
						<div class="fieldgroup">
							<input class="topcoat-text-input" id="default-url" type="text" name="default-url" size="40" placeholder="Default Embed" value="<?php echo $embed_data['default-url']; ?>" >
						</div>
						
						<div class="fieldgroup">
							<input class="topcoat-text-input" id="mobile-url" type="text" name="mobile-url" size="40" placeholder="Mobile Embed" value="<?php echo $embed_data['mobile-url']; ?>" >
						</div>
						
						<div class="fieldgroup">
								<input class="topcoat-button warning" type="submit" value="Set">
								<input class="topcoat-button close-mm" type="button" value="Cancel">
						</div>
						
					</form>
					
				</div>
			</div>
		</div><!-- #custom-link -->
		
	</div><!-- #tooltips -->
				
</div><!--  .container  -->
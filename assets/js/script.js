$(function() {
	
	$("#passlogin").focus();
	
	
	//Bar Close Handler
	$('a.close, .close-mm').click(function() {
		$(this).closest( '.mm-menu' ).trigger( 'close' );
	});
	
	//General
	var options = {
		classes			: 'mm-light',
	};
	
	var config = {
		transitionDuration : 1000,
	};
	
	options.position = 'left';
	options.zposition = 'next';
	
	function refreshBroadcasts() {
	
		$this = $('.tube-broadcasts');
		
		$this.addClass("loading");
		
		$.get('ajax-broadcasts.php', function(data) {
			
	    	//$this.append(data);
	    	$this.html(data);
	    })
	    .done(function() { 
	    	$this.removeClass("loading"); 
	    });
	    
	}
	
	//Output Bar Handler
	$('#raw-output').mmenu( options );
	
	// Video info Bar handler
	$('#video-info').mmenu( options , config )
	.on( "opened.mm", function(){
		$("#video-info .video-info-frame").attr("src", $("#video-info .video-info-frame").data("src") );
	})
	.on( "close.mm", function(){
		//Clean it up
		$("#video-info .video-info-frame").attr("src", "");
		$("#video-info .video-info-frame").data("src", "");
	});
	
	
	$('.refresh-broadcasts').click(function() { 
	    refreshBroadcasts();
	});
	
	//Video Info button handler
	$('.tube-broadcasts').on('click', '.info', function(event) {
		//Put data-info into an array
		var videoInfo = $.parseJSON( $(this).closest(".edit-bar").attr( "data-info" ).replace(/'/g, '"') );
		//Get time and format it
		var start = moment( videoInfo["start"], "X" ).format('MMMM Do YYYY, h:mm a');//YYYY-MM-DD HH:mm Z
		
		//Iframe populator
		$("#video-info iframe.video-info-frame").data("src", "https://www.youtube.com/embed/"+ videoInfo["id"] +"?autoplay=1&rel=0&showinfo=0&modestbranding=1&theme=light&autohide=1&wmode=transparent");
		//Title populator
		$("#video-info .video-title").text( videoInfo["title"] );
		//Start time populator
		$("#video-info .video-date").text(start);
		//description populator
		$("#video-info .video-description").text( videoInfo["description"] );
		
		$('#video-info').trigger( "open.mm" );
		
		event.preventDefault();
	});
	
	//Video duplicate button handler
	$('.tube-broadcasts').on('click', '.duplicate', function(event) {
		$("#broadcast-form").data( "action", "duplicate" );
		//Put data-info into an array
		var videoInfo = $.parseJSON( $(this).closest(".edit-bar").data( "info" ).replace(/'/g, '"') );
		//Get time and format it
		var date = moment( videoInfo["start"], "X").format('YYYY-MM-DD');
		var time = moment( videoInfo["start"], "X").format('HH:mm:ss');
		
		
		//Title populator
		$("#broadcast-title").val( videoInfo["title"] );
		//Start date populator
		$("#broadcast-date").val(date);
		//Start time populator
		$("#broadcast-time").val(time);
		//description populator
		$("#broadcast-description").val( videoInfo["description"] );
		
		$('#broadcast-bar').trigger( "open.mm" );
		
		event.preventDefault();
	});
	
	$('.tube-broadcasts').on('click', '.edit', function(event) {
		$("#broadcast-form").data( "action", "edit" );
		//Put data-info into an array
		var videoInfo = $.parseJSON( $(this).closest(".edit-bar").data( "info" ).replace(/'/g, '"') );
		//Get time and format it
		var date = moment( videoInfo["start"], "X").format('YYYY-MM-DD');
		var time = moment( videoInfo["start"], "X").format('HH:mm:ss');
		
		
		//Title populator
		$("#broadcast-title").val( videoInfo["title"] );
		//Start date populator
		$("#broadcast-date").val(date);
		//Start time populator
		$("#broadcast-time").val(time);
		//Description populator
		$("#broadcast-description").val( videoInfo["description"] );
		//Id Populator
		$("#broadcast-id").val( videoInfo["id"] );
		
		$('#broadcast-bar').trigger( "open.mm" );
		
		event.preventDefault();
	});
	
	$('#custom-link').mmenu({
		 position: "right",
		 zposition: "next"
	});
	
	
	
	//Set link via button
	
	$(document).on('click', '.set-link', function(event) {
		event.preventDefault();
		$.post( "live-console.php", { "default-url": $(this).data( "default-url" ), "mobile-url": $(this).data( "mobile-url" )  } )
			.done(function() { location.reload(); });
	});
	
	
	
	//New Broadcast Bar Handler
	
	$('#broadcast-bar').mmenu({
		 position: "right",
		 zposition: "next"
	})
	.on( "opened.mm", function(){
		//TODO: Set Date to Today
	     $("#broadcast-title").focus();
	})
	.on( "closed.mm", function() {
         $( "#broadcast-result > .result" ).html("");
         if( !$("#broadcast-result > .fieldgroup").hasClass("hideme") ) {
         	$("#broadcast-result > .fieldgroup").addClass("hideme");
         }
         $("#broadcast-form").attr("data-action" , "");
    });
	
	
	
	//New Broadcast Form Stuff
	
	
	
	//Display Date as title when title is empty
	$( "#broadcast-date, #broadcast-time" ).change(function() {
		
		var currentStartTime = moment( $("#broadcast-date").val()+" "+$("#broadcast-time").val() ).format("dddd MMMM Do ha");
		var fillerTitle = currentStartTime+" | "+$("html").data("church-name");
		
		if( !$("#broadcast-title").val() ) {
			//alert("The times they are a changin");
			$("#broadcast-title").attr('placeholder', fillerTitle );
		} else {
			var title = $("#broadcast-title").val().split(" | ")[0];
			if( moment( title , "dddd MMMM Do ha" ).isValid() || title === "Invalid date" )
			$("#broadcast-title").val( fillerTitle );
		}
	});
	
	$("#broadcast-timezone").val( moment().format('Z') );
	
	$( "#broadcast-thumb-link" ).change(function() {
		$('.thumb-preview').empty();
		//console.log( $(this).val() );
		if( $(this).val() ){
			$('.thumb-preview').prepend('<img src="'+$(this).val()+'" ><br>');
			
			$('.thumb-bar').removeClass("hideme");
		} else {
			$('.thumb-preview').prepend('<a href="#" class="topcoat-button dropbox-upload"><i class="fa fa-dropbox fa-lg"></i>Upload from Dropbox</a>');
			$('.thumb-bar').addClass("hideme");
		}
	});
	
	$(".broadcast-thumbnail").on('click', '.dropbox-upload', function(event) {
		event.preventDefault();
		
		options = {
		    success: function(files) {
		        $( "#broadcast-thumb-link" )
		        	.val( files[0].link )
		        	.trigger('change');
		    },
		    linkType: "direct", // or "preview"
		    multiselect: false, // or true
		    extensions: ['.jpg', '.png', '.gif', '.bmp'],
		};
		
		Dropbox.choose(options);
		
	});
	
	$(".clear-thumb").on('click', function(event) {
		event.preventDefault();
		
		$( "#broadcast-thumb-link" )
	    	.val( "" )
	    	.trigger('change');
		
	});
	
	
	$("#broadcast-form").submit(function( event ) {
		
		var action = $("#broadcast-form").data("action");
		
		var postScript = "create.php";
		
		if ( action == "edit" ) {
			postScript = "edit.php";
		}
		
		//testing thumbnails
//		postScript = "thumbnail.php";
		
		$("#broadcast-bar").addClass('loading');
		
		if( !$("#broadcast-title").val() ) {
			$("#broadcast-title").val( $("#broadcast-title").attr('placeholder') );
		}
		
		var values = $(this).serializeArray();
		
		
		
		$.post( postScript , values )
			.done(function( data ) {
				$("#broadcast-bar").removeClass('loading');
				$( "#broadcast-result > .result" ).html( data );
				$("#broadcast-result > .hideme").removeClass("hideme");
				
				$('.mm-current').animate({
					scrollTop: $("#broadcast-result").offset().top
				}, 1000);
				$("#broadcast-done").focus();
				refreshBroadcasts();
			});
			
			
		//Clean it up
		$("#broadcast-id").val( "" );
			
		event.preventDefault();
	});
	
	$(".topofbar").click( function(event) {
		$('.mm-current').animate({
	        scrollTop: $("#broadcast-form").offset().top
	    }, 1000);
	    $("#broadcast-title").focus();
	});
	
	
	//Success Message Bar
	$('#success-message').mmenu({
		 position: "right",
		 zposition: "next"
	}).on( "opened.mm", function(){
	     
	  }
	);
	
});
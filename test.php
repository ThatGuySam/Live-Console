<?php
	$raw_date = $_POST['broadcast-date']."T".$_POST['broadcast-time']."Z".$_POST['broadcast-timezone'];
	
	$start = strtotime( $raw_date );
	
	echo $raw_date;
	echo "<br>";
	echo date( 'r', $start);
	echo '<pre>';
	print_r($_POST);
	echo '</pre>';
	
	
	
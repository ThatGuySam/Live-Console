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

parse_str( $_SERVER['QUERY_STRING'] , $qs);

if( isset($_POST['pass']) && $_POST['pass'] != ""){
	if( !isset($_SESSION['pass']) || $_SESSION['pass'] != $_POST['pass'] ) {
		$_SESSION['pass'] = $_POST['pass'];
	}
}

if( $qs['logout'] ){
	$_POST['pass'] = "";
	$_SESSION['pass'] = "";
}

?>
<!DOCTYPE HTML>
<html data-church-name="<?php echo $church_name; ?>" > 
<?php include('head.php') ?>
<body>

<?php

if($_SESSION['pass'] == $goodpass) {
	include("live-secure.php");
}
else
{
    if(isset($_POST))
    {?>
        <form class="login" method="POST" action="live-console.php">
            <input id="passlogin" class="topcoat-text-input" type="password" name="pass" placeholder="Password"></input>
            <input class="topcoat-button" type="submit" name="submit" value="Go"></input>
        </form>
    <?}
}
?>

<?php include('footer.php') ?>
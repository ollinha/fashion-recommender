<?php
include('connect.php');

$site = "http://master.yanenko.de/shop/";

header('Expires: Mon, 20 Dec 1998 01:00:00 GMT');
header('Last-Modified: '.gmdate("D, d M Y H:i:s").' GMT');
header('Cache-Control: no-store, no-cache, must-revalidate');
header('Cache-Control: post-check=0, pre-check=0', FALSE); 
header('Pragma: no-cache');

if ($_COOKIE['user_id'] == '' || $_COOKIE['session_id'] == '') {
	header('Location: '.$site.'index.php');
}
?>
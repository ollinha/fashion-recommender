<?php
include('connect.php');

$site = "http://master.yanenko.de/shop/knowledge/";

Header('Cache-Control: no-cache');
Header('Pragma: no-cache');

if ($_COOKIE['master_id'] != '0934222320017620106103') {
	header('Location: '.$site.'index.php');
}
?>
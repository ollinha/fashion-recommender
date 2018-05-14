<?php
include('auth.php');

$user_id = $_COOKIE['user_id'];

$result = mysql_query('SELECT * FROM users WHERE id='.$user_id.'');
$row = mysql_fetch_array($result);

$action = $_GET['action'];
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Shopping Experiment</title>
<link href="testshop.css" rel="stylesheet" type="text/css" media="screen" />
</head>

<body>
<div id="page">
	<div id="head">
    	<div id="logo">
        	Experiment<br /><span>Shopping</span>
        </div>
        <div id="credit">&nbsp;</div>
        <div id="nav_main">
        	<br /><br /><br /><br />
        	<a href="quit.php">Beenden</a><br />
        </div>
        <div class="clearing">&nbsp;</div>
    </div>
    <div id="main">
    	<div id="cart"></div>
<?php
if ($action == '') {
	include('voting.php');
}
?>
        <div class="clearing">&nbsp;</div>
    </div>
    <div id="footer">Das Copyright für alle Bilder und Produktbeschreibungen liegt bei <a href="http://www.luisaviaroma.com/">LUISAVIAROMA</a>.</div>
</div>
</body>
</html>

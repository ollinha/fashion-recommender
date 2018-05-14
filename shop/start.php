<?php
include('auth.php');
include('functions.php');

$user_id = $_COOKIE['user_id'];

$result = mysql_query('SELECT * FROM users WHERE id='.$user_id.'');
$row = mysql_fetch_array($result);
$credit = $row['credit'];
$savings = $row['savings'];

$action = $_GET['action'];

$guthaben = priceToShow($credit);
$ersparnisse = priceToShow($savings);
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
        <div id="credit">Guthaben:<br /><span class="money"><?=$guthaben?></span><br /><br />Ersparnisse:<br /><strong><?=$ersparnisse?></strong></div>
        <div id="nav_main">
        	<a href="shop.php">zum Shop</a><br /><br />
            <a href="start.php?action=rules">Spielregeln</a><br />
            <a href="start.php?action=history">meine Einkäufe</a><br />
        	<a href="quit.php">Beenden</a><br />
        </div>
        <div class="clearing">&nbsp;</div>
    </div>
    <div id="main">
    	<div id="cart"></div>
<?php
if ($action == '') {
	include('rules.php');
} elseif ($action == 'rules') {
	include('rules.php');
} elseif ($action == 'history') {
	include('history.php');
} elseif ($action == 'gallery') {
	include('gallery.php');
}
?>
        <div class="clearing">&nbsp;</div>
    </div>
    <div id="footer">Das Copyright für alle Bilder und Produktbeschreibungen liegt bei <a href="http://www.luisaviaroma.com/">LUISAVIAROMA</a>.</div>
</div>
</body>
</html>

<?php
include('auth.php');
include('functions.php');

$user_id = $_COOKIE['user_id'];
$session_id = $_COOKIE['session_id'];
$action = $_GET['action'];

$result = mysql_query('SELECT * FROM users WHERE id='.$user_id.'');
$row = mysql_fetch_array($result);
$credit = $row['credit'];
$permutation = $row['permutation'];
$savings = $row['savings'];

$guthaben = priceToShow($credit);
$ersparnisse = priceToShow($savings);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="expires" content="0">
<meta http-equiv="cache-control" content="no-cache">
<meta http-equiv="pragma" content="no-cache">
<title>Shopping Experiment</title>
<link href="testshop.css" rel="stylesheet" type="text/css" media="screen" />
</head>

<body>
<div id="page">
	<div id="head">
    	<div id="logo">
        	Experiment<br /><span>Shop</span>
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
    	<div id="cart"><a href="cart.php"><span>zum Warenkorb</span> <img src="images/shopping_cart.png" alt="Warenkorb" /></a></div>
<?php
if ($action == 'details') {
	include('product_detail.php');
} elseif ($action == 'products') {
	include('product.php');
} else {
	include('overview.php');
}
?>
        <div class="clearing">&nbsp;</div>
    </div>
    <div id="footer">Das Copyright für alle Bilder und Produktbeschreibungen liegt bei <a href="http://www.luisaviaroma.com/">LUISAVIAROMA</a>.</div>
</div>
</body>
</html>

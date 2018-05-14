<?php
include('auth.php');
include('functions.php');

$user_id = $_COOKIE['user_id'];
$session_id = $_COOKIE['session_id'];
$action = $_GET['action'];
$rec = $_GET['rec'];

if ($rec != 1) {
	$rec = 0;
}

$result = mysql_query('SELECT * FROM users WHERE id='.$user_id.'');
$row = mysql_fetch_array($result);
$credit = $row['credit'];
$savings = $row['savings'];

if ($action == 'add') {
	$prod_id = $_GET['id'];
	if (mysql_query('INSERT INTO cart (session_id, product_id, rec) VALUES ('.$session_id.', '.$prod_id.', '.$rec.')')) {
		$text = '<p><span class="green">Der Artikel wurde Ihrem Warenkorb hinzugefügt.</span></p>';
	}

	// Logging
	mysql_query('INSERT INTO clicks (session_id, time, product_id, action, recommendation) VALUES ('.$session_id.', "'.date('c').'", '.$prod_id.', 1, '.$rec.')');
}
if ($action == 'delete') {
	$prod_id = $_GET['id'];
	if (mysql_query('DELETE FROM cart WHERE product_id='.$prod_id.' AND session_id='.$session_id.' LIMIT 1')) {
		$text = '<p><span class="green">Der Artikel wurde aus Ihrem Warenkorb gelöscht.</span></p>';
	}
}
if ($action == 'buy') {
	$cart_result = mysql_query('SELECT * FROM cart WHERE session_id='.$session_id);
	$price = 0;
		
	while ($row = mysql_fetch_array($cart_result)) {
		$prod_id = $row['product_id'];
		
		$prod_result = mysql_query('SELECT price FROM products WHERE id='.$prod_id);
		$prod_row = mysql_fetch_array($prod_result);
		
		$add = priceToCents($prod_row['price']);
		
		$rec_result = mysql_query('SELECT rec FROM cart WHERE product_id='.$prod_id.' AND session_id='.$session_id);
		$rec_row = mysql_fetch_array($rec_result);
		$prod_rec = $rec_row['rec'];
		
		$price += $add;

		mysql_query('DELETE FROM cart WHERE product_id='.$prod_id.' AND session_id='.$session_id.' LIMIT 1');
		mysql_query('INSERT INTO history (session_id, product_id) VALUES ('.$session_id.', '.$prod_id.')');
		
		// Logging
		mysql_query('INSERT INTO clicks (session_id, time, product_id, action, recommendation) VALUES ('.$session_id.', "'.date('c').'", '.$prod_id.', 2, '.$prod_rec.')');
	}
	
	$credit = $credit - $price;
	mysql_query('UPDATE users SET credit='.$credit.' WHERE id='.$user_id);

	$text = '<p><span class="green">Vielen Dank für Ihren Einkauf. Der Betrag wurde von Ihrem Guthaben abgezogen.</span></p>';
}

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
        <div id="nav">&nbsp;</div>
        <div id="content">
            <h1>Warenkorb</h1><br />
<?php
$result = mysql_query('SELECT * FROM cart WHERE session_id='.$session_id);
$num = mysql_num_rows($result);

echo $text;

if ($num == 0) {
	echo '<p>Es befinden sich keine Artikel in Ihrem Warenkorb.</p><div class="back"><a href="shop.php">&lt; zurück zum Shop</a></div>';
	if ($action == 'buy') {
		echo '<div class="kasse"><a href="start.php?action=history">Artikel zuordnen &gt;</a></div>';
	} else {
        echo '<div class="kasse"><a href="quit.php">Beenden &gt;</a></div>';
	}
} else {
?>
            <p>
            	<table class="cart_table">
					<tr>
                    	<td class="cart_head" colspan="2">Artikel</td>
                        <td class="cart_head">Preis</td>
                        <td class="cart_head">&nbsp;</td>
                    </tr>
<?php
	$price_all = 0;
	while ($row = mysql_fetch_array($result)) {
		$prod_id = $row['product_id'];
		$prod_result = mysql_query('SELECT * FROM products WHERE id='.$prod_id);
		$prod_row = mysql_fetch_array($prod_result);
		$name = $prod_row['name'];
		$add = priceToCents($prod_row['price']);
		$price = priceToShow($add);
		$price_all += $add;
		$image = $prod_row['image'];
		echo '					<tr>
							<td class="cart"><image class="small_img" src="'.$image.'" alt="'.$name.'" /></td>
							<td class="cart" width="300">'.$name.'</td>
							<td class="cart" width="70">'.$price.'</td>
							<td class="cart" width="70"><a href="cart.php?action=delete&id='.$prod_id.'"><img src="images/out_shopping_cart.jpg" alt="Löschen" /></a></td>
						</tr>
	';
	}
	echo '                <tr>
							<td class="cart_head" colspan="2">Gesamt</td>
							<td class="cart" width="70"><strong>'.priceToShow($price_all).'</strong></td>
							<td class="cart" width="70">&nbsp;</td>
						</tr>
					</table>
            </p>
';

	if ($credit >= $price_all) {
		echo '            <div class="back"><a href="shop.php">&lt; zurück zum Shop</a></div>
            <div class="kasse"><a href="cart.php?action=buy">zur Kasse gehen &gt;</a></div>
';
	} else {
		echo '<p><span class="warnung">Ihr Guthaben reicht für diesen Einkauf nicht aus! Bitte löschen Sie einige Artikel.</span></p>
            <div class="back"><a href="shop.php">zurück zum Shop</a></div>
';
	}
}
?>
        </div>
        <div class="clearing">&nbsp;</div>
    </div>
    <div id="footer">Das Copyright für alle Bilder und Produktbeschreibungen liegt bei <a href="http://www.luisaviaroma.com/">LUISAVIAROMA</a>.</div>
</div>
</body>
</html>

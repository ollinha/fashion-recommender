<div id="nav">
	<ul>
        <a href="start.php?action=history"><li>Einkaufshistorie</li></a>
        <li>Anlässe &amp; Situationen</li>
    </ul>
</div>
<div id="content">
    <h1>Anlässe &amp; Situationen</h1>
    <p>Die folgenden Outfits haben Sie für die verschiedenen Anlässe gewählt. Sie können die Zuordnung unter <a href="start.php?action=history">Einkaufshistorie</a> ändern.</p>
<?php
$result = mysql_query('SELECT history.product_id FROM history LEFT JOIN sessions ON sessions.id=history.session_id WHERE history.outfit=1 AND sessions.user_id='.$user_id);
if (mysql_num_rows($result) > 0) {
	echo '<p>Für den Anlass <em>Rendezvous</em> haben Sie das folgende Outfit gewählt:</p>';
	while ($row = mysql_fetch_array($result)) {
		$prod_id = $row['product_id'];
		
		$prod_result = mysql_query('SELECT * FROM products WHERE id='.$prod_id);
		$prod_row = mysql_fetch_array($prod_result);
		
		$image = $prod_row['image'];
		$name = $prod_row['name'];
		echo '<img width="100" class="product" src='.$image.' alt='.$name.' />';
	}
	echo '<div class="clearing">&nbsp;</div>';
}

$result = mysql_query('SELECT history.product_id FROM history LEFT JOIN sessions ON sessions.id=history.session_id WHERE history.outfit=2 AND sessions.user_id='.$user_id);
if (mysql_num_rows($result) > 0) {
	echo '<hr /><p>Für den Anlass <em>Party</em> haben Sie das folgende Outfit gewählt:</p>';
	while ($row = mysql_fetch_array($result)) {
		$prod_id = $row['product_id'];
		
		$prod_result = mysql_query('SELECT * FROM products WHERE id='.$prod_id);
		$prod_row = mysql_fetch_array($prod_result);
		
		$image = $prod_row['image'];
		$name = $prod_row['name'];
		echo '<img width="100" class="product" src='.$image.' alt='.$name.' />';
	}
	echo '<div class="clearing">&nbsp;</div>';
}

$result = mysql_query('SELECT history.product_id FROM history LEFT JOIN sessions ON sessions.id=history.session_id WHERE history.outfit=3 AND sessions.user_id='.$user_id);
if (mysql_num_rows($result) > 0) {
	echo '<hr /><p>Für den Anlass <em>Arbeit</em> haben Sie das folgende Outfit gewählt:</p>';
	while ($row = mysql_fetch_array($result)) {
		$prod_id = $row['product_id'];
		
		$prod_result = mysql_query('SELECT * FROM products WHERE id='.$prod_id);
		$prod_row = mysql_fetch_array($prod_result);
		
		$image = $prod_row['image'];
		$name = $prod_row['name'];
		echo '<img width="100" class="product" src='.$image.' alt='.$name.' />';
	}
	echo '<div class="clearing">&nbsp;</div>';
}

$result = mysql_query('SELECT history.product_id FROM history LEFT JOIN sessions ON sessions.id=history.session_id WHERE history.outfit=0 AND sessions.user_id='.$user_id);
if (mysql_num_rows($result) > 0) {
	echo '<hr /><p>Folgende Artikel haben wurden noch keinem Anlass zugeordnet:</p>';
	while ($row = mysql_fetch_array($result)) {
		$prod_id = $row['product_id'];
		
		$prod_result = mysql_query('SELECT * FROM products WHERE id='.$prod_id);
		$prod_row = mysql_fetch_array($prod_result);
		
		$image = $prod_row['image'];
		$name = $prod_row['name'];
		echo '<img width="100" class="product" src='.$image.' alt='.$name.' />';
	}
	echo '<div class="back"><a href="start.php?action=history">Zuordnung vornehmen &gt;</a>';
} else {
	echo '<div class="back"><a href="start.php?action=history">Zuordnung ändern &gt;</a>';
}
?>
	</div>
</div>

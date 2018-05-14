<div id="nav">
	&nbsp;
</div>
<div id="content">
    <h1>Herzlich Willkommen!</h1>
<?php
$sum_result = mysql_query('SELECT * FROM voting WHERE user_id='.$user_id);
$sum = mysql_num_rows($sum_result);

if ($sum == 7) {
	echo '<p><span class="green">Herzlichen Glückwunsch! Sie haben gewonnen! Der Amazon-Gutschein wird per Email versendet.</span></p>';
}
?>    
    <p>Sie haben insgesamt <strong><?=$sum?></strong> Punkte beim Voting erreicht. Die höchste erreichte Punktzahl beträgt 7 Punkte. Weiter unten sehen Sie eine Zusammenfassung Ihrer Outfits mit den entsprechenden Voting-Stimmen.</p>
    
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
		echo '<img width="100" class="product" src="'.$image.'" alt="'.$name.'" />';
	}
	$voting_result = mysql_query('SELECT * FROM voting WHERE user_id='.$user_id.' AND outfit=1');
	$voting = mysql_num_rows($voting_result);
	
	echo '<div class="clearing">&nbsp;</div>';
	echo '<p>Für dieses Outfit haben Sie <strong>'.$voting.'</strong> Stimmen erhalten.</p>';
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
		echo '<img width="100" class="product" src="'.$image.'" alt="'.$name.'" />';
	}
	$voting_result = mysql_query('SELECT * FROM voting WHERE user_id='.$user_id.' AND outfit=2');
	$voting = mysql_num_rows($voting_result);
	
	echo '<div class="clearing">&nbsp;</div>';
	echo '<p>Für dieses Outfit haben Sie <strong>'.$voting.'</strong> Stimmen erhalten.</p>';
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
		echo '<img width="100" class="product" src="'.$image.'" alt="'.$name.'" />';
	}
	$voting_result = mysql_query('SELECT * FROM voting WHERE user_id='.$user_id.' AND outfit=3');
	$voting = mysql_num_rows($voting_result);
	
	echo '<div class="clearing">&nbsp;</div>';
	echo '<p>Für dieses Outfit haben Sie <strong>'.$voting.'</strong> Stimmen erhalten.</p>';
}
?>
</div>
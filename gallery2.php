<?php
if (isset($_POST['vote'])) {
	$vote = $_POST['voting'];
	mysql_query('INSERT INTO voting (user_id, voting_user_id, outfit) VALUES ('.$vote.', '.$user_id.', 2)');
}
?>
<div id="nav">
	<ul>
        <a href="start.php?action=gallery1"><li>Galerie Rendezvous</li></a>
        <li>Galerie Party</li>
        <a href="start.php?action=gallery3"><li>Galerie Arbeit</li></a>
    </ul>
</div>
<div id="content">
    <h1>Voting für den Anlass Party</h1>
<?php
$voted_result = mysql_query('SELECT * FROM voting WHERE outfit=2 AND voting_user_id='.$user_id);
$voted_num = mysql_num_rows($voted_result);

if ($voted_num == 1) {
	echo '<p><span class="green">Sie haben Ihre Stimme für den Anlass Party bereits vergeben.</span></p>';
	$voted_row = mysql_fetch_array($voted_result);
	$voted_id = $voted_row['user_id'];
}
?>
    <p>Die folgenden Outfits wurden von den verschiedenen Nutzern für den Anlass Arbeit gewählt. Bitte suchen Sie einen
    Favoriten aus und klicken anschließend ganz unten auf "voten". Überlegen Sie gut, Sie können Ihre Wahl im Nachhinein nicht mehr
    ändern.</p>
    <p>HINWEIS:<br />Die Reihenfolge, in der die Outfits aufgeführt sind, entspricht nicht der aktuellen Rangfolge.</p>
    <form action="start.php?action=gallery2" method="POST">
<?php
$result_voted = mysql_query('SELECT * FROM voting WHERE outfit=2 AND voting_user_id='.$user_id);
$num = mysql_num_rows($result_voted);

$result = mysql_query('SELECT DISTINCT users.id FROM history JOIN sessions ON sessions.id=history.session_id JOIN users ON users.id=sessions.user_id');

$counter = 1;

while ($row = mysql_fetch_array($result)) {
	$user = $row['id'];
	
	if ($user != $user_id) {
		
		$outfit_result = mysql_query('SELECT history.product_id FROM history JOIN sessions ON sessions.id=history.session_id JOIN users ON users.id=sessions.user_id WHERE history.outfit=2 AND users.id='.$user);
		
		$outfit_num = mysql_num_rows($outfit_result);
		
		if ($outfit_num > 0) {
		
			echo '<hr /><p>Outfit Nr. '.$counter.' ';
			
			if ($voted_id == $user) {
				echo '<span class="green">-&gt; Sie haben für dieses Outfit gestimmt</span>';
			}
			
			if ($voted_num == 0) {
				echo 'wählen <input type="radio" name="voting" value="'.$user.'" />
				';
			}
			
			echo '</p>';
			$counter++;
			
			while ($outfit_row = mysql_fetch_array($outfit_result)) {
				$prod_id = $outfit_row['product_id'];
				$prod_result = mysql_query('SELECT * FROM products WHERE id='.$prod_id);
				
				$prod_row = mysql_fetch_array($prod_result);
			
				$image = $prod_row['image'];
				$name = $prod_row['name'];
				
				echo '<img width="100" class="product" src="'.$image.'" alt="'.$name.'" />'; 
			}
			echo '<div class="clearing">&nbsp;</div>';
		}
	}
}

if ($voted_num == 0) {
	echo '<input type="submit" name="vote" value="voten"></form><div class="clearing">&nbsp;</div>';
}
?>
</div>

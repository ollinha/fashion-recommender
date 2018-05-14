<?php
include('auth.php');

$action = $_GET['action'];
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Shopping Experiment</title>
<link href="../testshop.css" rel="stylesheet" type="text/css" media="screen" />
</head>

<body>
<div id="page">
	<div id="head">
    	<div id="logo">
        	Experiment<br /><span>Shopping</span>
        </div>
        <div id="credit">&nbsp;</div>
        <div id="nav_main">
        	<a href="start.php">Übersicht</a><br />
            <a href="products.php">Experiment</a><br />
        	<a href="similarities.php">Ähnlichkeiten</a><br />
            <a href="evaluation.php">Evaluierung</a><br />
        	<a href="quit.php">Beenden</a><br />
        </div>
        <div class="clearing">&nbsp;</div>
    </div>
    <div id="main">
    	<div id="cart"></div>
    	<div id="nav">
        	<ul>
                <a href="start.php?action=gallery"><li>Galerie</li></a>
                <a href="start.php?action=similarities"><li>Ähnlichkeiten-Experiment</li></a>
            </ul>
        </div>
        <div id="content">
<?php
function showOutfit($name, $num, $user_id) {
	echo '<h3>'.$name.'</h3>';
	
	$history_result = mysql_query('SELECT history.product_id FROM history LEFT JOIN sessions ON history.session_id=sessions.id WHERE sessions.user_id='.$user_id.' AND outfit='.$num);
	
	while ($history_row = mysql_fetch_array($history_result)) {
		$prod_id = $history_row['product_id'];
		
		$prod_result = mysql_query('SELECT image FROM products WHERE id='.$prod_id);
		$prod_row = mysql_fetch_array($prod_result);
		
		$image = $prod_row['image'];
		$name = $prod_row['name'];
		
		echo '<img width="100" class="product" src="../'.$image.'" alt="'.$name.'" />';
	}
	echo '<div class="clearing">&nbsp;</div>';
}

function makeRandomProducts($ids, $similarities, $except, $num) {
	$rand = mt_rand(0, count($ids) - 1); 
	$value = $ids[$rand];
	
	if (!in_array($value, $except)) {
		$result = mysql_query('SELECT * FROM products WHERE id='.$value);
		$row = mysql_fetch_array($result);
		
		$image = $row['image'];
		$name = $row['name'];
	
		echo '<td><img width="180" src="../'.$image.'" alt="'.$name.'" /><br>
		<input type="hidden" name="id'.$num.'" value="'.$value.'" />
		<input type="hidden" name="sim'.$num.'" value="'.$similarities[$rand].'" />&nbsp;&nbsp;&nbsp;&nbsp;
		<input type="radio" name="rank'.$num.'" value="1" /> 1&nbsp;&nbsp;&nbsp;&nbsp;
		<input type="radio" name="rank'.$num.'" value="2" /> 2&nbsp;&nbsp;&nbsp;&nbsp;
		<input type="radio" name="rank'.$num.'" value="3" /> 3</td>';
		return $value;
	} else {
		makeRandomProducts($ids, $similarities, $except, $num);
	}
}

if ($action == 'gallery') {
	echo '        	<h1>Gallerie</h1>';
	
	$user_result = mysql_query('SELECT * FROM users');
	
	while ($user_row = mysql_fetch_array($user_result)) {
		$s_user_id = $user_row['id'];
		$s_user_name = $user_row['name'];
				
		echo '<h2>'.$s_user_name.'</h2>';
		
		showOutfit('Rendezvous', 1, $s_user_id);
		showOutfit('Party', 2, $s_user_id);
		showOutfit('Arbeit', 3, $s_user_id);
		showOutfit('Nicht zugeordnet', 0, $s_user_id);
	}
} elseif ($action == 'similarities') {
	if (isset($_POST['dem_data']) || isset($_POST['data'])) {
		$age = $_POST['age'];
		$gender = $_POST['gender'];
		
		if (isset($_POST['dem_data'])) {
			$result = mysql_query('SELECT DISTINCT user FROM sim_evaluation ORDER by USER DESC');
			$row = mysql_fetch_array($result);
			
			$last_user = $row['user'];
			$user = $last_user + 1;
		} elseif (isset($_POST['data'])) {
			$user = $_POST['user'];
			
			for ($i=1; $i<4; $i++) {
				mysql_query('INSERT INTO sim_evaluation (user, age, gender, product, rec_product, similarity, user_range) VALUES ('.$user.', '.$age.', '.$gender.', '.$_POST['id'].', '.$_POST['id'.$i].', '.$_POST['sim'.$i].', '.$_POST['rank'.$i].')');
			} 
		}
		
		$ids = array();
		$except = array();
		$result = mysql_query('SELECT id FROM products WHERE cat="Kleidung"');
		
		while ($row = mysql_fetch_array($result)) {
			$ids[] = $row['id'];
		}
		
		$rand = mt_rand(0, count($ids) - 1); 
		$value = $ids[$rand];
		$except[] = $value;
		
		$result = mysql_query('SELECT * FROM products WHERE id='.$value);
		$row = mysql_fetch_array($result);
		
		$image = $row['image'];
		$name = $row['name'];
			
		echo '<h1>Wahrgenommene Ähnlichkeit</h1><table width="100%">
                <form action="start.php?action=similarities" method="POST">
				<input type="hidden" name="age" value="'.$age.'" />
				<input type="hidden" name="gender" value="'.$gender.'" />
				<input type="hidden" name="user" value="'.$user.'" />
				<input type="hidden" name="id" value="'.$value.'" />
                <tr>
                    <td colspan="3"><div id="product_image"><img class="product" src="../'.$image.'" alt="'.$name.'"/></div>
					<div id="description"><br /><br /><br /><br />Falls sie dieses Produkt gerne kaufen möchten, es jedoch leider nicht mehr verfügbar ist. Welche der unteren drei Alternativen würden Sie wählen und in welcher Reihenfolge?</div></td>
                </tr>
                <tr>';
		
		$prod_ids = array();
		$prod_similarities = array();
		$result = mysql_query('SELECT rec_product_id, similarity FROM similarities WHERE product_id='.$value.' AND coverage=1 AND layer=1');
		
		while ($row = mysql_fetch_array($result)) {
			$prod_ids[] = $row['rec_product_id'];
			$prod_similarities[] = $row['similarity'];
		}
		
        $except[] = makeRandomProducts($prod_ids, $prod_similarities, $except, 1);
		$except[] = makeRandomProducts($prod_ids, $prod_similarities, $except, 2);
		$except[] = makeRandomProducts($prod_ids, $prod_similarities, $except, 3);
		
        echo '</tr>
				<tr>
                    <td class="title" colspan="2">&nbsp;</td>
                    <td><br /><input type="submit" name="data" value="weiter" /></td>
                </tr></form></table>';
		
	} else {
		echo '        	<h1>Wahrgenommene Ähnlichkeit</h1><table width="100%">
                <form action="start.php?action=similarities" method="POST">
                <tr>
                    <td class="title">Geschlecht: </td>
                    <td><input type="radio" name="gender" value="1" /> weiblich&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" name="gender" value="0" /> männlich</td>
                </tr>
				<tr>
                    <td class="title">Alter: </td>
                    <td><input maxlength="3" size="2" type="text" name="age" /> Jahre</td>
                </tr>
				<tr>
                    <td>&nbsp;</td>
                    <td><br /><input type="submit" name="dem_data" value="weiter" /></td>
                </tr></form></table>';
	}
	
} else {
	$query = mysql_query('SELECT * FROM products');
	$prod_num = mysql_num_rows($query);
	
	$query = mysql_query('SELECT * FROM users');
	$user_num = mysql_num_rows($query);
	
	$query = mysql_query('SELECT * FROM sessions');
	$sess_num = mysql_num_rows($query);
	
	$query = mysql_query('SELECT * FROM history');
	$history_num = mysql_num_rows($query);
	
	$query = mysql_query('SELECT * FROM clicks');
	$click_num = mysql_num_rows($query);
	
	$query = mysql_query('SELECT * FROM users WHERE credit=250000 AND savings=500000');
	$buy_num = mysql_num_rows($query);

	echo '        	<h1>Übersicht</h1>
            <p>Es befinden sich momentan <span class="green">'.$prod_num.'</span> Produkte in der Datenbank.</p>
            <p>Es befinden sich momentan <span class="green">'.$user_num.'</span> Benutzer in der Datenbank.</p>
            <p>Es befinden sich momentan <span class="green">'.$sess_num.'</span> Sessions in der Datenbank.</p>
            <p>Es wurden bereits <span class="green">'.$history_num.'</span> Produkte gekauft.</p>
            <p>Es befinden sich bereits <span class="green">'.$click_num.'</span> Klicks in der Datenbank.</p>
			<p>Es befinden sich <span class="green">'.$buy_num.'</span> Benutzer in der Datenbank, die noch nichts gekauft haben.</p>';
}
?>
        </div>
        <div class="clearing">&nbsp;</div>
    </div>
</div>
</body>
</html>

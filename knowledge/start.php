<?php
include('auth.php');
include('../functions.php');

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
            	<a href="start.php?action=php"><li>PHP Info</li></a>
                <a href="start.php?action=tableall"><li>Tabelle mit allen Inhalten</li></a>
            	<a href="start.php?action=timestamp"><li>Timestamp</li></a>
                <a href="start.php?action=cents"><li>Preise in Cent</li></a>
                <a href="start.php?action=gallery"><li>Galerie</li></a>
                <a href="start.php?action=similarities"><li>Ähnlichkeiten-Experiment</li></a>
                <a href="start.php?action=sim_data"><li>Ähnlichkeiten-Experiment Daten</li></a>
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

function getCategory($name) {
	$result = 0;
	switch ($name) {
		case 'Kleidung':
			$result = 0;
			break;
		case 'Accessoires':
			$result = 1;
			break;
		case 'Schuhe':
			$result = 2;
			break;	
	}
	return $result;
}

function toTimestamp($string) {
	$split1 = explode('+', $string);
	$split2 = explode('T', $split1[0]);
	
	$date = explode('-', $split2[0]);
	$time = explode(':', $split2[1]);
	
	$hour = $time[0];
	$minute = $time[1];
	$second = $time[2];
	$day = $date[2];
	$month = $date[1];
	$year = $date[0];
	
	$result = mktime($hour, $minute, $second, $month, $day, $year);
	return $result;
}

if ($action == 'gallery') {
	echo '        	<h1>Gallerie</h1>';
	
	$user_result = mysql_query('SELECT * FROM users');
	
	while ($user_row = mysql_fetch_array($user_result)) {
		$s_user_id = $user_row['id'];
		$s_user_name = $user_row['name'];
		
		$voting1_result = mysql_query('SELECT * FROM voting WHERE user_id='.$s_user_id.' AND outfit=1');
		$voting1_num = mysql_num_rows($voting1_result);
		
		$voting2_result = mysql_query('SELECT * FROM voting WHERE user_id='.$s_user_id.' AND outfit=2');
		$voting2_num = mysql_num_rows($voting2_result);
		
		$voting3_result = mysql_query('SELECT * FROM voting WHERE user_id='.$s_user_id.' AND outfit=3');
		$voting3_num = mysql_num_rows($voting3_result);
		
		$sum = $voting1_num + $voting2_num + $voting3_num;
				
		echo '<h2>'.$s_user_name.'</h2>
		Rendezvous: '.$voting1_num.'<br/>
		Party: '.$voting2_num.'<br/>
		Arbeit: '.$voting3_num.'<br/>
		<strong>Gesamt: '.$sum.'</strong>';
		
		
		showOutfit('Rendezvous', 1, $s_user_id);
		showOutfit('Party', 2, $s_user_id);
		showOutfit('Arbeit', 3, $s_user_id);
		showOutfit('Nicht zugeordnet', 0, $s_user_id);
	}
} elseif ($action == 'php') {
	phpinfo();
} elseif ($action == 'tableall') {
	$result = mysql_query('SELECT users.id AS user_id, sessions_new.id AS session_id, clicks_new.id AS click_id, products_new.id AS product_id, users.permutation, users.age, users.gender, users.for_wife, users.experience, users.experience_mode, products_new.cat, products_new.price, clicks_new.recommendation, clicks_new.action, clicks_new.time FROM clicks_new LEFT JOIN sessions_new ON clicks_new.session_id=sessions_new.id JOIN products_new ON clicks_new.product_id=products_new.id JOIN users ON users.id=sessions_new.user_id');

	echo '        	<h1>Datensätze</h1>
	Anzahl: ' .mysql_num_rows($result);
	
	while ($row = mysql_fetch_array($result)) {
		$user_id = $row['user_id'];
		$permutation = $row['permutation'];
		$age = $row['age'];
		$gender = $row['gender'];
		$for_wife = $row['for_wife'];
		$experience = $row['experience'];
		$experience_mode = $row['experience_mode'];
		$session_id = $row['session_id'];
		$click_id = $row['click_id'];
		$product_id = $row['product_id'];
		$cat = $row['cat'];
		$price = $row['price'];
		$recommendation = $row['recommendation'];
		$action = $row['action'];
		$time = $row['time'];
		
		mysql_query('INSERT INTO results (user_id, permutation, age, gender, for_wife, experience, experience_mode, session_id, click_id, product_id, cat, price, recommendation, action, time) VALUES ('.$user_id.', '.$permutation.', '.$age.', '.$gender.', '.$for_wife.', '.$experience.', '.$experience_mode.', '.$session_id.', '.$click_id.', '.$product_id.', '.$cat.', '.$price.', '.$recommendation.', '.$action.', '.$time.')');
	}
} elseif ($action == 'timestamp') {
	echo '        	<h1>Timestamp</h1>';
	
	$session_result = mysql_query('SELECT * FROM sessions');
	
	while ($session_row = mysql_fetch_array($session_result)) {
		$session_id = $session_row['id'];
		$session_user_id = $session_row['user_id'];
		$session_start = $session_row['start'];
		
		$session_timestamp = toTimestamp($session_start);
		
		echo 'Session '.$session_id.'<br />Start: '.$session_start.'<br />Timestamp: '.$session_timestamp.'<br /><br />';
		
		mysql_query('INSERT INTO sessions_new (id, user_id, start) VALUES ('.$session_id.', '.$session_user_id.', '.$session_timestamp.')');
	}
	
	$click_result = mysql_query('SELECT * FROM clicks');
	
	while ($click_row = mysql_fetch_array($click_result)) {
		$click_id = $click_row['id'];
		$click_session_id = $click_row['session_id'];
		$time = $click_row['time'];
		$product_id = $click_row['product_id'];
		$click_action = $click_row['action'];
		$rec = $click_row['recommendation'];
		
		$click_timestamp = toTimestamp($time);
		
		echo 'Klick '.$click_id.'<br />Zeit: '.$time.'<br />Timestamp: '.$click_timestamp.'<br /><br />';
		
		mysql_query('INSERT INTO clicks_new (id, session_id, time, product_id, action, recommendation) VALUES ('.$click_id.', '.$click_session_id.', '.$click_timestamp.', '.$product_id.', '.$click_action.', '.$rec.')');
	}
} elseif ($action == 'cents') {
	echo '        	<h1>Preise</h1>';
	
	$product_result = mysql_query('SELECT * FROM products');
	
	while ($product_row = mysql_fetch_array($product_result)) {
		$product_id = $product_row['id'];
		$product_category = $product_row['cat'];
		$product_price = $product_row['price'];
		
		$category_new = getCategory($product_category);
		$price_new = priceToCents($product_price);
				
		echo 'Produkt '.$product_id.'<br />Alter Preis: '.$product_price.'<br />Neuer Preis: '.$price_new.'<br />Alte Kategorie: '.$product_category.'<br />Neue Kategorie: '.$category_new.'<br /><br />';
		
		mysql_query('INSERT INTO products_new (id, cat, price) VALUES ('.$product_id.', '.$category_new.', '.$price_new.')');
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
	
} elseif ($action == 'sim_data') {
	/*$result = mysql_query('SELECT id FROM sim_evaluation ORDER BY id DESC');	
	$count = 1;
	$test_id = 1;
	
	while ($row = mysql_fetch_array($result)) {
		$res_id = $row['id'];
		mysql_query('UPDATE sim_evaluation SET test_id='.$test_id.' WHERE id='.$res_id);
		$count++;
		if ($count > 3) {
			$test_id++;
			$count = 1;
		}
	}*/
	
	$result = mysql_query('SELECT id, similarity, test_id FROM sim_evaluation');
	
	while ($row = mysql_fetch_array($result)) {
		$res_id = $row['id'];
		$sim = $row['similarity'];
		$test_id = $row['test_id'];
		
		$res_sim1 = mysql_query('SELECT similarity FROM sim_evaluation WHERE test_id='.$test_id.' and range=1');
		$row_sim1 = mysql_fetch_array($res_sim1);
		$sim1 = $row_sim1['similarity'];
		$diff1 = abs($sim - $sim1);
		
		$res_sim2 = mysql_query('SELECT similarity FROM sim_evaluation WHERE test_id='.$test_id.' and range=2');
		$row_sim2 = mysql_fetch_array($res_sim2);
		$sim2 = $row_sim2['similarity'];
		$diff2 = abs($sim - $sim2);

		$res_sim3 = mysql_query('SELECT similarity FROM sim_evaluation WHERE test_id='.$test_id.' and range=3');
		$row_sim3 = mysql_fetch_array($res_sim3);
		$sim3 = $row_sim3['similarity'];
		$diff3 = abs($sim - $sim3);

		mysql_query('UPDATE sim_evaluation SET diff1='.$diff1.', diff2='.$diff2.', diff3='.$diff3.' WHERE id='.$res_id);
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

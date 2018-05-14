<?php
include('auth.php');
include('connect.php');

$site = 'http://master.yanenko.de/';
$user_id = $_COOKIE['user_id'];
$mail = $_GET['mail'];

$result = mysql_query('SELECT * FROM users WHERE id="'.$user_id.'"');

if (mysql_num_rows($result)) {
	$row = mysql_fetch_array($result);
	
	$gender = $row['gender'];
	$age = $row['age'];
	$for_wife = $row['for_wife'];
	$experience = $row['experience'];
	$experience_mode = $row['experience_mode'];
}

if (isset($_POST['data'])) {
	$sql = 'UPDATE users SET ';
	
	$gender = $_POST['gender'];
	$age = $_POST['age'];
	$experience = $_POST['experience'];
	$experience_mode = $_POST['experience_mode'];
	
	if ($gender == 1) {
		$for_wife = 1;
	} else {
		$for_wife = $_POST['for_wife'];
	} 
	
	if (isset($gender)) {
		$sql .= 'gender="'.$gender.'", ';
	}
	if ($age != 0) {
		$sql .= 'age="'.$age.'", ';
	}
	if (isset($for_wife)) {
		$sql .= 'for_wife="'.$for_wife.'", ';
	}
	if (isset($experience)) {
		$sql .= 'experience="'.$experience.'", ';
	}
	if (isset($experience_mode)) {
		$sql .= 'experience_mode="'.$experience_mode.'", ';
	}
	
	if ($sql != 'UPDATE users SET ') {
		$sql = substr($sql, 0, strlen($sql) - 2);
		$sql .=  'WHERE id="'.$user_id.'"';
		
		$result = mysql_query('SELECT * FROM users WHERE id="'.$user_id.'"');
		
		if (mysql_num_rows($result) > 0) {
			mysql_query($sql);
		}	
	}
	
	header('Location: '.$site.'start.php');
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Shopping Experiment</title>
<link href="testshop.css" rel="stylesheet" type="text/css" media="screen" />
<script type="text/javascript">
function makeVisible() {
	document.getElementById('show').setAttribute('style', 'display: table-row;');
}

function makeInvisible() {
	document.getElementById('show').setAttribute('style', 'display: none;');
}
</script>

</head>

<body>
<div id="page">
	<div id="head">
    	<div id="logo">
        	Experiment<br /><span>Daten</span>
        </div>
        <div id="credit"></div>
        <div id="nav_main"></div>
        <div class="clearing">&nbsp;</div>
    </div>
    <div id="main">
    	<div id="cart"></div>
    	<div id="nav">&nbsp;</div>
        <div id="content">
            <table>
                <tr>
                    <td colspan="2">
<?php
if ($mail == 'ok') {
	echo '<p><span class="green">Eine Email mit Ihren Zugangsdaten wurde an die angegebene Email-Adresse verschickt.</span></p>';
}
?>
                        <p>Nun werden noch einige demografische Daten für die Auswertung benötigt. Falls Sie diese gleich ausfüllen, werden Sie nicht weiter danach gefragt. Wenn Sie jedoch keine Angaben machen, wird diese Seite bei jedem Einloggen erneut erscheinen.</p>
                    </td>
                </tr>
                <form action="dem_data.php" method="POST">
<?php
if ($gender == '-1') {
?>
                <tr class="dem_data">
                    <td class="title">Geschlecht: </td>
                    <td><input type="radio" name="gender" value="1" onclick="javascript: makeInvisible()" /> weiblich&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" name="gender" value="0" onclick="javascript: makeVisible()" /> männlich</td>
                </tr>
<?php
}
if ($for_wife == '-1') {
?>
                <tr class="dem_data" id="show"<?php if ($gender == '-1') { echo ' style="display: none;"'; } ?>>
                    <td class="title">Haben Sie schon einmal Damenkleidung eingekauft<br />(für Frau/Freundin)?</td>
                    <td><input type="radio" name="for_wife" value="1" /> ja&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" name="for_wife" value="0" /> nein</td>
                </tr>
<?php
}
if ($age == '-1') {
?>
                <tr class="dem_data">
                    <td class="title">Alter: </td>
                    <td><input maxlength="3" size="2" type="text" name="age" /> Jahre</td>
                </tr>
<?php
}
if ($experience == '-1') {
?>
                <tr class="dem_data">
                    <td class="title">Wie ist Ihre Erfahrung im Bereich Online-Shopping?</td>
                    <td>
                    	<input type="radio" name="experience" value="0" /> Ich habe noch nie im Internet eingekauft.<br />
                        <input type="radio" name="experience" value="1" /> Ich kaufe gelegentlich im Internet ein.<br />
                        <input type="radio" name="experience" value="2" /> Ich kaufe ca. eimal in drei Monaten im Internet ein.<br />
                        <input type="radio" name="experience" value="3" /> Ich kaufe ca. jeden Monat im Internet ein.
                    </td>
                </tr>
<?php
}
if ($experience_mode == '-1') {
?>
                <tr class="dem_data">
                    <td class="title">Wie ist Ihre Erfahrung beim Kauf von Kleidung in Online-Shops?</td>
                    <td>
                    	<input type="radio" name="experience_mode" value="0" /> Ich habe noch nie Kleidung im Internet gekauft.<br />
                        <input type="radio" name="experience_mode" value="1" /> Ich habe schonmal Kleidung im Internet gekauft,<br />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;bevorzuge jedoch das Einkaufen im Ladengeschaft.<br />
                        <input type="radio" name="experience_mode" value="2" /> Ich kaufe meine Kleidung sowohl im Internet als auch<br />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;im Ladengeschäft.<br />
                        <input type="radio" name="experience_mode" value="3" /> Ich kaufe meine Kleidung überwiegend im Internet.
                    </td>
                </tr>
<?php
}
?>
                <tr>
                    <td class="title">&nbsp;</td>
                    <td><input type="submit" name="data" value="speichern" /></td>
                </tr>
                </form>
            </table>
        </div>
        <div class="clearing">&nbsp;</div>
    </div>
</div>
</body>
</html>

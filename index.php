<?php
include('connect.php');

Header('Cache-Control: no-cache');
Header('Pragma: no-cache');

$site = 'http://master.yanenko.de/';

$quit = $_GET['quit'];

$zugang_error = false;
$nickname_new_error = false;
$nickname_old_error = false;
$no_nickname_error = false;
$no_password_error = false;
$password_error = false;
$email_error = false;
$mail_error = false;

if (isset($_POST['old'])) {
	$nickname_old = $_POST['nickname_old'];
	$password_old = $_POST['password_old'];
	
	$result = mysql_query('SELECT * FROM users WHERE name="'.$nickname_old.'"');
	
	if (mysql_num_rows($result) < 1) {
		$nickname_old_error = true;
	} else {
		$row = mysql_fetch_array($result);
		$password = $row['password'];
		
		if ($password_old != $password) {
			$password_error = true;
		}
	}
	
	if (!$nickname_old_error && !$password_error) {
		$user_id = $row[id];
		
		setcookie('user_id', $user_id, time() + 43200);
		
		header('Location: '.$site.'start.php');
	}
}

function nicknameExists($name) {
	$result = mysql_query('SELECT * FROM users WHERE name="'.$name.'"');
	if (mysql_num_rows($result) < 1) {
		return false;
	} else {
		return true;
	}
}
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
        	Experiment<br /><span>Login</span>
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
if ($quit == 'ok') {
	echo '<p><span class="green">Sie haben sich erfolgreich abgemeldet. Bis zum nächsten mal!</span></p>';
}
?>
                        <p>Liebe Teilnehmer,</p>
                        <p>das Experiment geht allmählich dem Ende zu. Sie haben nun noch einmal die Möglichkeit, sich auf dieser
                        Seite einzuloggen und bei der Abstimmung teilzunehmen. Sie können sich alle Outfits in einer Galerie
                        anschauen und für jeden Anlass einen Favoriten wählen. Das eigene Outfit kann nicht gewählt werden.</p>
                        <p>Anfang Mai erhält jeder Teilnehmer eine Email mit dem Rang, den er erreicht hat.</p>
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                    </td>
                </tr>
                <form action="index.php" method="POST">
<?php
if ($nickname_old_error) {
	echo '                    <tr>
                        <td colspan="2" class="warnung">
                            Dieser Nickname ist leider falsch!
                        </td>
                    </tr>';
}
?>
                <tr>
                    <td class="title">Nickname: </td>
                    <td><input type="text" name="nickname_old" value="<?=$nickname_old?>" /></td>
                </tr>
<?php
if ($password_error) {
	echo '                    <tr>
                        <td colspan="2" class="warnung">
                            Das ist das falsche Passwort für den Benutzer '.$nickname_old.'!
                        </td>
                    </tr>';
}
?>
                <tr>
                    <td class="title">Passwort: </td>
                    <td><input type="password" name="password_old" value="<?=$password_old?>" /></td>
                </tr>
                <tr>
                    <td class="title">&nbsp;</td>
                    <td><input type="submit" name="old" value="einloggen" /></td>
                </tr>
                </form>
            </table>
        </div>
        <div class="clearing">&nbsp;</div>
    </div>
</div>
</body>
</html>

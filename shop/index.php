<?php
include('connect.php');

Header('Cache-Control: no-cache');
Header('Pragma: no-cache');

$permutations = 2;

$site = 'http://master.yanenko.de/shop/';

$quit = $_GET['quit'];

$zugang_error = false;
$nickname_new_error = false;
$nickname_old_error = false;
$no_nickname_error = false;
$no_password_error = false;
$password_error = false;
$email_error = false;
$mail_error = false;

if (isset($_POST['new'])) {
	$zugangscode = $_POST['zugangscode'];
	$nickname_new = $_POST['nickname'];
	$password_new = $_POST['password'];
	$email = $_POST['email'];
	
	if ($zugangscode != 'oyMaster25960') {
		$zugang_error = true;
	}
	if (nicknameExists($nickname_new)) {
		$nickname_new_error = true;
	}
	if ($nickname_new == '') {
		$no_nickname_error = true;
	}
	if ($password_new == '') {
		$no_password_error = true;
	}
	if (!emailCorrect($email)) {
		$email_error = true;
	}
	
	if (!$zugang_error && !$nickname_new_error && !$email_error && !$no_nickname_error && !$no_password_error) {
		$mailtext = 'Hallo "'.$nickname_new.'",

herzlich Willkommen beim Shopping-Experiment! Ihre Zugangsdaten für http://master.yanenko.de lauten:

Nickname: '.$nickname_new.'
Passwort: '.$password_new.'

Herzliche Grüße

Olga Yanenko';

		$headers = "From: Olga Yanenko <olga@yanenko.de>\r\n";
		$headers .= "Reply-To: olga@yanenko.de\r\n";
		$headers .= "Content-Type: text/plain; charset=utf-8\r\n";
		
		if (mail($email, "Anmeldung beim Shopping-Experiment", $mailtext, $headers)) {
			$permutation = getPermutation();
			
			mysql_query('INSERT INTO users (name, md5, password, email, credit, savings, permutation) VALUES ("'.$nickname_new.'", "'.md5($nickname_new).'", "'.$password_new.'", "'.$email.'", 250000, 500000, '.$permutation.')');
			$user_id = mysql_insert_id();

			$start = date('c');
			
			mysql_query('INSERT INTO sessions (user_id, start) VALUES ('.$user_id.', "'.$start.'")');
			$session_id = mysql_insert_id();
			
			setcookie('user_id', $user_id, 0);
			setcookie('session_id', $session_id, 0);
			
			header('Location: '.$site.'dem_data.php?mail=ok');
		} else {
			$mail_error = true;
		}	
	}
} elseif (isset($_POST['old'])) {
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
		
		$start = date('c');
		
		mysql_query('INSERT INTO sessions (user_id, start) VALUES ('.$user_id.', "'.$start.'")');
		$session_id = mysql_insert_id();
		
		setcookie('user_id', $user_id, time() + 43200);
		setcookie('session_id', $session_id, time() + 43200);
		
		if (demDataComplete($user_id)) {
			header('Location: '.$site.'start.php');
		} else {
			header('Location: '.$site.'dem_data.php');
		}
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

function emailCorrect($email) {
	if (preg_match("/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/", $email)) {
		return true;
	} else {
		return false;
	}
}

function demDataComplete($id) {
	$result = mysql_query('SELECT * FROM users WHERE id="'.$id.'"');
	$row = mysql_fetch_array($result);
	
	$gender = $row['gender'];
	$age = $row['age'];
	$for_wife = $row['for_wife'];
	$experience = $row['experience'];
	$experience_mode = $row['experience_mode'];
	
	if ($gender != '-1' && $age != '-1' && $for_wife != '-1' && $experience != '-1' && $experience_mode != '-1') {
		return true;
	} else {
		return false;
	}
}

function getPermutation() {
	global $permutations;
	$permutation = 0;
	$result = mysql_query('SELECT * FROM users ORDER BY id DESC');
	if (mysql_num_rows($result) > 0) {
		$row = mysql_fetch_array($result);
		$last_permutation = $row['permutation'];
		
		$permutation = $last_permutation + 1;
		
		if ($permutation > $permutations) {
			$permutation = 0;
		}
	}
	return $permutation;
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
                        <p>vielen Dank, dass Sie sich für die Teilnahme an diesem Experiment entschieden haben.</p>
                        <p>Wenn Sie zum ersten Mal auf dieser Seite sind, brauchen Sie den Zugangscode aus der Einladungs-Email. Desweiteren müssen Sie sich einen Nickname sowie ein Passwort überlegen. Diese werden benötigt, um sich erneut auf dieser Seite einzuloggen. Um Ihnen die Zugangsdaten zuzuschicken, wird zudem eine Email-Adresse benötigt. Diese wird ausschließlich im Rahmen des Experiments verwendet. Nach Abschluss des Experiments werden alle Email-Adressen gelöscht (spätestens Anfang Mai).</p>
                        <p>Wenn Sie sich bereits angemeldet haben, benötigen Sie nur ihre Zugangsdaten, um fortzufahren.</p>
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        Sie sind bereits angemeldet und haben die Zugangsdaten per Email bekommen?
                    </td>
                </tr>
                <form action="index.php" method="POST">
<?php
if ($nickname_old_error) {
	echo '                    <tr>
                        <td colspan="2" class="warnung">
                            Dieser Nickname ist leider falsch! Falls Sie noch keine Zugangsdaten haben, melden Sie sich bitte weiter oben an.
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
                <tr>
                    <td colspan="2">
                        <br /><br />Sie sind neu bei dem Experiment?
                    </td>
                </tr>
                <form action="index.php" method="POST">
<?php
if ($zugang_error) {
	echo '                    <tr>
                        <td colspan="2" class="warnung">
                            Der Zugangscode ist leider nicht korrekt!
                        </td>
                    </tr>';
}
?>
                <tr>
                    <td class="title">Zugangscode: </td>
                    <td><input type="text" name="zugangscode" value="<?=$zugangscode?>" /></td>
                </tr>
<?php
if ($nickname_new_error) {
	echo '                    <tr>
                        <td colspan="2" class="warnung">
                            Dieser Nickname existiert bereits! Falls Sie sich bereits angemeldet haben, loggen Sie sich bitte weiter unten ein. Falls nicht, wählen Sie bitte einen anderen Namen.
                        </td>
                    </tr>';
}
if ($no_nickname_error) {
	echo '                    <tr>
                        <td colspan="2" class="warnung">
                            Bitte geben Sie einen Nickname ein!
                        </td>
                    </tr>';
}
?>
                <tr>
                    <td class="title">Nickname: </td>
                    <td><input type="text" name="nickname" value="<?=$nickname_new?>" /></td>
                </tr>
<?php
if ($no_password_error) {
	echo '                    <tr>
                        <td colspan="2" class="warnung">
                            Bitte geben Sie ein Passwort ein!
                        </td>
                    </tr>';
}
?>
                <tr>
                    <td class="title">Passwort: </td>
                    <td><input type="text" name="password" value="<?=$password_new?>" /></td>
                </tr>
<?php
if ($email_error) {
	echo '                    <tr>
                        <td colspan="2" class="warnung">
                            Bitte geben Sie eine gültige Email-Adresse an!
                        </td>
                    </tr>';
}

if ($mail_error) {
	echo '                    <tr>
                        <td colspan="2" class="warnung">
                            Die Email-Adresse scheint nicht zu stimmen! Die Zugangsdaten konnten nicht versendet werden. Geben Sie bitte eine gültige Adresse ein.
                        </td>
                    </tr>';
}
?>
                <tr>
                    <td class="title">Email: </td>
                    <td><input type="text" name="email" value="<?=$email?>" /></td>
                </tr>
                <input type="hidden" name="from" value="<?=$from?>" />
                <tr>
                    <td class="title">&nbsp;</td>
                    <td><input type="submit" name="new" value="anmelden" /></td>
                </tr>
                </form>
            </table>
        </div>
        <div class="clearing">&nbsp;</div>
    </div>
</div>
</body>
</html>

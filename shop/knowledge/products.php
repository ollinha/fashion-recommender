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
                <a href="products.php?action=crawl"><li>Produkte einlesen</li></a>
                <a href="products.php?action=credit"><li>Gehalt gutschreiben</li></a>
                <a href="products.php?action=reminder"><li>Erinnerung schicken</li></a>
                <a href="products.php?action=voting"><li>Voting beginnen</li></a>
            </ul>
        </div>
        <div id="content">
        
<?php
if ($action == 'crawl') {
	include('includes/functions1.php');
	$folder = 'products';
	
	if (file_exists($folder) && is_dir($folder)) {
		echo '<p>Ordner "'.$folder.'" ist vorhanden.</p>';
		
		$files = opendir($folder);
			
        while ($file = readdir($files)){
			$damen = $folder.'/'.$file.'/damen';
			
            if (file_exists($damen) && is_dir($damen)) {
				echo '<p>Ordner "'.$file.'" enthält einen Ordner "damen".</p>';
				
				$damen_files = opendir($damen);
			
				while ($damen_file = readdir($damen_files)) {
					$product_dir = $damen.'/'.$damen_file;
					
					if (is_dir($product_dir) && $damen_file != 'neue+kollektion' && $damen_file != 'sale' && $damen_file != '.' && $damen_file != '..' && $damen_file != 'extra+fashion'  && $damen_file != 'handschuhe' && $damen_file != 'charms' && $damen_file != 'brieftaschen') {
						echo '<p>Ordner "'.$damen_file.'" wird verarbeitet...<br />';
						
						$product_files = opendir($product_dir);
						
						while ($product_file = readdir($product_files)) {
							$filepath = $product_dir.'/'.$product_file;
							
							if (is_file($filepath)) {
								echo 'Produktinformationen werden aus Datei "'.$product_file.'" gelesen...<br />';
								createDBEntry($filepath, $damen_file);
							}
						}
						
						echo '</p>';
					}
				}
			}
        }
	}
} elseif ($action == 'credit') {
	$result = mysql_query('SELECT * FROM users');
	
	while ($row = mysql_fetch_array($result)) {
		$savings_old = $row['savings'];
		$credit_old = $row['credit'];
		$email = $row['email'];
		$nickname = $row['name'];
		$id = $row['id'];
		
		$savings_new = $savings_old + $credit_old;
		$credit_new = 250000;
		
		$mailtext = 'Hallo "'.$nickname.'",

die letzte Phase des Shopping-Experiments hat begonnen, somit ist dies die letzte Möglichkeit noch etwas einzukaufen, bevor das Experiment am 13. April endet. Ihr restliches Guthaben von '.priceToShow($credit_old).' wurde zu Ihren Ersparnissen hinzugefügt. Ihr Erspartes beträgt nun '.priceToShow($savings_new).'. Ein neues Guthaben von '.priceToShow($credit_new).' wurde Ihrem Konto gutgeschrieben und steht nun für weitere Einkäufe auf http://master.yanenko.de bereit.

Herzliche Grüße

Olga Yanenko';

		$headers = "From: Olga Yanenko <olga@yanenko.de>\r\n";
		$headers .= "Reply-To: olga@yanenko.de\r\n";
		$headers .= "Content-Type: text/plain; charset=utf-8\r\n";
		
		if (mail($email, "Neues Guthaben für das Shopping-Experiment", $mailtext, $headers) && mysql_query('UPDATE users SET credit='.$credit_new.', savings='.$savings_new.' WHERE id='.$id)) {
			echo 'Benutzer "'.$nickname.'":<br />Altes Guthaben: '.priceToShow($credit_old).'<br />Alte Ersparnisse: '.priceToShow($savings_old).'<br />Neues Guthaben: '.priceToShow($credit_new).'<br />Neue Ersparnisse: '.priceToShow($savings_new).'<br /><br />';
		}
	}
} elseif ($action == 'reminder') {
	$result = mysql_query('SELECT * FROM users WHERE credit=250000');
	
	while ($row = mysql_fetch_array($result)) {
		$nickname = $row['name'];
		$email = $row['email'];
		
		$mailtext = 'Hallo "'.$nickname.'",

die nächste Phase des Shopping-Experiments beginnt bald und Sie haben noch nichts ausgegeben. Wenn Sie in den nächsten Tagen nichts einkaufen, wird Ihr Guthaben Ihren Ersparnissen gutgeschrieben und steht nicht mehr für Einkäufe bereit. Wenn Sie doch noch einkaufen wollen, freue ich mich über Ihren Besuch auf http://master.yanenko.de.

HINWEIS: Es handelt sich um ein Experiment bei dem die Käufe von den Teilnehmern simuliert werden. Das bedeutet, dass man die Sachen NICHT wirklich selbst kaufen und bezahlen muss.

Herzliche Grüße

Olga Yanenko';

		$headers = "From: Olga Yanenko <olga@yanenko.de>\r\n";
		$headers .= "Reply-To: olga@yanenko.de\r\n";
		$headers .= "Content-Type: text/plain; charset=utf-8\r\n";
		
		if (mail($email, "Ihr Guthaben verfällt bald", $mailtext, $headers)) {
			echo 'Benutzer "'.$nickname.'" hat eine Erinnerung geschickt bekommen.<br /><br />';
		}		
	}
} elseif ($action == 'voting') {
	$result = mysql_query('SELECT * FROM users');
	
	while ($row = mysql_fetch_array($result)) {
		$nickname = $row['name'];
		$email = $row['email'];
		
		$mailtext = 'Hallo "'.$nickname.'",

die Einkaufphasen sind vorbei. Vielen Dank für die Teilnahme. Nun beginnt das Voting auf http://master.yanenko.de. Das bedeutet, Sie können nun für jeden Anlass einen Favoriten wählen (die eigenen Outfits können nicht gewählt werden). Aus dem Voting und den Ersparnissen wird dann der Gewinner berechnet.

Herzliche Grüße

Olga Yanenko';

		$headers = "From: Olga Yanenko <olga@yanenko.de>\r\n";
		$headers .= "Reply-To: olga@yanenko.de\r\n";
		$headers .= "Content-Type: text/plain; charset=utf-8\r\n";
		
		if (mail($email, "Ihr Guthaben verfällt bald", $mailtext, $headers)) {
			echo 'Benutzer "'.$nickname.'" hat eine Erinnerung geschickt bekommen.<br /><br />';
		}		
	}
}
?>
        </div>
        <div class="clearing">&nbsp;</div>
    </div>
</div>
</body>
</html>

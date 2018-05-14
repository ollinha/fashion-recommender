<?php
$message = '';

if (isset($_POST['update'])) {
	foreach ($_POST as $key => $value) {
		if (substr($key, 0, 4) == 'prod') {
			$history_id = substr($key, 4, strlen($key));
			mysql_query('UPDATE history SET outfit='.$value.' WHERE id='.$history_id);
		}
	}
	$message = '<p><span class="green">Die Zuordnung der Kleidungsstücke zu den Anlässen wurde aktualisiert!</span></p>';
}
?>
<div id="nav">
	<ul>
        <li>Einkaufshistorie</li>
        <a href="start.php?action=gallery"><li>Anlässe &amp; Situationen</li></a>
    </ul>
</div>
<div id="content">
    <h1>Einkaufshistorie</h1>
    <?=$message?>
    <p>Hier sehen Sie eine Auflistung Ihrer bereits getätigten Einkäufe und können einzelne Produkte den verschiedenen Anlässen zuordnen. Diese werden im Menüpunkt <a href="start.php?action=gallery">Anlässe &amp; Situationen</a> auf der linken Seite in einer Galerie-Ansicht geordnet nach Anlässen dargestellt.</p>
<?php
$sess_result = mysql_query('SELECT * FROM sessions WHERE user_id='.$user_id);

function checkOutfit($outfit, $compare) {
	if ($outfit == $compare) {
		return ' checked="checked"';
	} else {
		return '';
	}
}

while ($row = mysql_fetch_array($sess_result)) {
	$sess_id = $row['id'];
	$date = dateToShow($row['start']);	
	
	$history_result = mysql_query('SELECT * FROM history WHERE session_id='.$sess_id);
	$num = mysql_num_rows($history_result);
	
	if ($num > 0) {
		echo '<table style="width: 100%;">
    <form action="start.php?action=history" method="post">
	<td colspan="3"><p>Am '.$date.' haben Sie die folgenden Artikel gekauft:</p></td>';
		
		$count = 1;
		
		while($history_row = mysql_fetch_array($history_result)) {
			$history_id = $history_row['id'];
			$prod_id = $history_row['product_id'];
			$outfit = $history_row['outfit'];
			
			$prod_result = mysql_query('SELECT * FROM products WHERE id='.$prod_id);
			$prod_row = mysql_fetch_array($prod_result);
			
			$image = $prod_row['image'];
			
			echo '<tr>
            <td style="width: 50px;">
            	'.$count.'.
            </td>
            <td>
            	<img  width="120" class="product" src="'.$image.'" alt="" />
            </td>
            <td>';
			if ($outfit != 0) {
				echo '<p><span class="green">Dieser Artikel ist bereits zugeordnet.</span></p>
				<p><em>Zuordnung ändern:</em></p>';
			} else {
				echo '<p><em>Diesen Artikel zuordnen:</em></p>';
			}
            echo '<input type="radio" name="prod'.$history_id.'" value="1"'.checkOutfit($outfit, 1).' /> Rendezvous<br />
                <input type="radio" name="prod'.$history_id.'" value="2"'.checkOutfit($outfit, 2).' /> Party<br />
                <input type="radio" name="prod'.$history_id.'" value="3"'.checkOutfit($outfit, 3).' /> Arbeit
            </td>
        </tr>';
			$count++;
		}
	}
}
?>

        <tr>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>
<?php
$result = mysql_query('SELECT * FROM history LEFT JOIN sessions ON history.session_id=sessions.id WHERE sessions.user_id='.$user_id);
if (mysql_num_rows($result) > 0) {
	echo '<input type="submit" name="update" value="aktualisieren" />';
}
?>
            </td>
        </tr>
    </form>
    </table>
</div>

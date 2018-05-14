<div id="nav">
	<ul>
<?php
$s_kat = urldecode($_GET['kat']);
$s_subkat = urldecode($_GET['subkat']);

$query1 = mysql_query('SELECT DISTINCT cat FROM products');

while ($row1 = mysql_fetch_array($query1)) {
	$kat = $row1['cat'];
	
	if ($s_kat != $kat) {
		echo '<a href="shop.php?action=products&kat='.urlencode($kat).'"><li>'.$kat.'</li></a>';
	} else {
		echo '<li>'.$kat.'</li>';
	}
	
	$query2 = mysql_query('SELECT DISTINCT subcat FROM products WHERE cat="'.$kat.'"');
	
	while ($row2 = mysql_fetch_array($query2)) {
		$subkat = $row2['subcat'];

		if ($s_subkat != $subkat) {
			echo '<a href="shop.php?action=products&subkat='.urlencode($subkat).'"><li class="level1">'.$subkat.'</li></a>';
		} else {
			echo '<li class="level1">'.$subkat.'</li>';
		}		
	}
}
?>
    </ul>
</div>
<div id="content">
<h1>Shop</h1>
<p>Herzlich Willkommen in unserem Online-Shop für exklusive Mode. Viel Spaß bei Ihrem Einkauf!</p>
<?php
$query1 = mysql_query('SELECT DISTINCT cat FROM products');

while ($row1 = mysql_fetch_array($query1)) {
	$kat = $row1['cat'];
	
	echo '<div id="overview"><a href="shop.php?action=products&kat='.urlencode($kat).'"><h2>'.$kat.'</h2></a>';

	$query2 = mysql_query('SELECT DISTINCT subcat FROM products WHERE cat="'.$kat.'"');
	
	while ($row2 = mysql_fetch_array($query2)) {
		$subkat = $row2['subcat'];

		echo '<a href="shop.php?action=products&subkat='.urlencode($subkat).'"><h3>'.$subkat.'</h3></a>';	
	}
	echo '</div>';
}
echo '<div class="clearing">&nbsp;</div>';
?>
</div>
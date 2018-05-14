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
<?php
if ($s_subkat != '') {
	$query = 'SELECT * FROM products WHERE subcat="'.$s_subkat.'"';
	$headline = $s_subkat;
} else {
	$query = 'SELECT * FROM products WHERE cat="'.$s_kat.'"';
	$headline = $s_kat;
}

echo '<h1>'.$headline.'</h1>';

$result = mysql_query($query);
while ($row = mysql_fetch_array($result)) {
	$prod_id = $row['id'];
	$image = $row['image'];
	
	echo '<div class="product"><a href="shop.php?action=details&product='.$prod_id.'"><img src="'.$image.'" alt="'.$name.'"/></div>'; 
}
?>
</div>
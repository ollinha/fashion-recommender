<div id="nav">
	<ul>
<?php
$s_id = $_GET['product'];
$rec = $_GET['rec'];

if ($rec != 1) {
	$rec = 0;
}

// Logging
mysql_query('INSERT INTO clicks (session_id, time, product_id, action, recommendation) VALUES ('.$session_id.', "'.date('c').'", '.$s_id.', 0, '.$rec.')');

$min_coverage = 0.5;

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
		
		$query3 = mysql_query('SELECT DISTINCT gruppe FROM products WHERE subcat="'.$subkat.'"');
	}
}
$query = 'SELECT * FROM products WHERE id='.$s_id;
$result = mysql_query($query);
$row = mysql_fetch_array($result);

$s_kat = $row['cat'];
$image = $row['image'];
$designer = $row['designer'];
$name = $row['name'];
$desc = $row['description'];
$price = priceToShow(priceToCents($row['price']));

$desc_arr = split(' - ', $desc);
?>
    </ul>
</div>
<div id="content">
    <div id="product_image"><img src="<?=$image?>" alt="<?=$name?>" /></div>
    <div id="description">
        <h1><?=$designer?></h1>
        <h3><?=$name?></h3><br />
        <ul>
<?php
foreach ($desc_arr as $value) {
	echo '<li>'.$value.'</li>';
}

$cart_link = 'cart.php?action=add&id='.$s_id;
if ($rec == 1) {
	$cart_link .= '&rec=1';
}
?>
        </ul>
        <div id="buy">
            <div id="price"><?=$price?></div>
            <div id="to_shopping_cart">
                <a href="<?=$cart_link?>"><img src="images/in_shopping_cart.jpg" alt="in den Warenkorb" />
                <br />in den Warenkorb legen</a>
            </div>
        </div>
    </div>
<?php
function makeRandomRecommendation($ids, $except) {
	$rand = mt_rand(0, count($ids) - 1); 
	$value = $ids[$rand];
	
	if (!in_array($value, $except)) {
		$result = mysql_query('SELECT * FROM products WHERE id='.$value);
		$row = mysql_fetch_array($result);
		
		$image = $row['image'];
		$name = $row['name'];
	
		echo '<div class="recommendation"><a href="shop.php?action=details&product='.$value.'&rec=1"><img src="'.$image.'" alt="'.$name.'" /></a></div>';
		return $value;
	} else {
		makeRandomRecommendation($ids, $except);
	}
}

function makeRecommendation($product_id, $num, $min_coverage, $max_coverage, $layer_sim, $cat, $visited_array) {
	$sql = 'SELECT products.* FROM similarities LEFT JOIN products ON similarities.rec_product_id=products.id WHERE similarities.product_id='.$product_id.' AND similarities.layer='.$layer_sim.' AND similarities.coverage>='.$min_coverage.' AND similarities.coverage<='.$max_coverage;
	
	if ($cat != '') {
		$sql .= ' AND products.cat="'.$cat.'"';
	}
	
	$sql .= ' ORDER BY similarities.similarity DESC';	
	$result = mysql_query($sql);
	
	for ($i=0; $i<$num; $i++) {
		$row = mysql_fetch_array($result);
		$value = $row['id'];
		
		while (in_array($value, $visited_array)) {
			$row_1 = mysql_fetch_array($result);
			if ($row_1) {
				$row = $row_1;
				$value = $row['id'];
			}
		}
		
		$image = $row['image'];
		$name = $row['name'];
	
		echo '<div class="recommendation"><a href="shop.php?action=details&product='.$value.'&rec=1"><img src="'.$image.'" alt="'.$name.'" /></a></div>';
		
	}
}

if ($permutation != 0) {
	echo '<div id="recommendations">
	<h3>Unsere Empfehlungen für Sie:</h3>
	<div id="left">';
	
	if ($permutation == 1) {
		$ids = array();
		$except = array();
		
		$result = mysql_query('SELECT id FROM products');
		
		while ($row = mysql_fetch_array($result)) {
			$ids[] = $row['id'];
		}
	
		for ($i=0; $i<3; $i++) {
			$except[] = makeRandomRecommendation($ids, $except);
		}
	
		echo '</div>
	<div id="right">';
	
		for ($i=0; $i<3; $i++) {
			$except[] = makeRandomRecommendation($ids, $except);
		}			
	} elseif ($permutation == 2) {
		$visited_array = array();
		
		$visited_result = mysql_query('SELECT product_id FROM clicks WHERE action=0 AND session_id='.$session_id);
		
		while ($visited_row = mysql_fetch_array($visited_result)) {
			$visited_array[] = $visited_row['product_id'];
		}
	
		if ($s_kat == 'Kleidung') {
			makeRecommendation($s_id, 3, 0.75, 1, 1, '', $visited_array);		
			echo '</div>
		<div id="right">';	
			makeRecommendation($s_id, 1, 0, 1, 0, 'Kleidung', $visited_array);
			makeRecommendation($s_id, 1, 0, 0, 0, 'Schuhe', $visited_array);
			makeRecommendation($s_id, 1, 0, 0, 0, 'Accessoires', $visited_array);	
		} elseif ($s_kat == 'Schuhe') {
			makeRecommendation($s_id, 3, 0.7, 1, 1, 'Schuhe', $visited_array);		
			echo '</div>
		<div id="right">';	
			makeRecommendation($s_id, 2, 0, 0, 0, 'Kleidung', $visited_array);
			makeRecommendation($s_id, 1, 0, 0, 1, 'Accessoires', $visited_array);	
		} elseif ($s_kat == 'Accessoires') {
			makeRecommendation($s_id, 3, 0, 1, 1, 'Accessoires', $visited_array);		
			echo '</div>
		<div id="right">';	
			makeRecommendation($s_id, 2, 0, 0, 0, 'Kleidung', $visited_array);
			makeRecommendation($s_id, 1, 0, 0, 1, 'Schuhe', $visited_array);	
		}
	}
	
	echo '</div>
</div>';
}
?>
</div>
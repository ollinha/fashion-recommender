<?php
include('auth.php');

include('includes/OWLDocument.php');
include('includes/Product.php');
include('includes/Concept.php');
include('includes/Individual.php');
include('includes/Property.php');

$action = $_GET['action'];
$start = $_GET['start'];
$end = $_GET['end'];

if ($action == 'Produkte') {
	$headline = 'OWL';
} elseif ($action == 'similarities') {
	$headline = 'Ähnlichkeiten';
}

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
                <a href="similarities.php?action=individuals"><li>Individuen zeigen</li></a>
                <a href="similarities.php?action=similarities"><li>Ähnlichkeiten berechnen</li></a>
            </ul>
        </div>
        <div id="content">
        	<h1><?=$headline?></h1>
<?
if (file_exists('ontology/fashion_ontology_new.owl')) {
	$owl = new OWLDocument();
	$owl->load('ontology/fashion_ontology_new.owl');
	$owl->init();
	if ($action == 'individuals') {
		$products = $owl->getProducts();
		foreach ($products as $product) {
			$product_id = $product->getID();
			
			$superclasses = $product->getSuperclasses();
			$properties = $product->getProperties();
			
			$result = mysql_query('SELECT * FROM products WHERE id='.$product_id);
			$row = mysql_fetch_array($result);
			
			$image = $row['image'];
			$name = $row['name'];
			
			echo '<h3>'.$name.' (ID: '.$product_id.')</h3><div id="product_image"><img src="../'.$image.'" alt="'.$name.'" /></div>
    <div id="description">
		<h4>Properties</h4>
        <ul>';
			foreach ($properties as $property) {
				echo '<li>'.$property->getName().'('.$property->getRange().')</li>';
			}
		echo '        </ul>
		<h4>Superclasses</h4>
        <ul>';
			foreach ($superclasses as $superclass) {
				echo '<li>'.$superclass.'</li>';
			}
		echo '        </ul></div><div class="clearing">&nbsp;</div>';
		}
	} elseif ($action == 'similarities') {
		$products_1 = $owl->getProducts();
		$products_2 = $owl->getProducts();
		
		foreach ($products_1 as $product_1) {
			$product_1_id = $product_1->getID();
			
			if ($product_1_id >= $start && $product_1_id < $end) {
				$superclasses_1 = $product_1->getSuperclasses();
				$properties_1 = $product_1->getProperties();
				
				$result_1 = mysql_query('SELECT * FROM products WHERE id='.$product_1_id);
				$row_1 = mysql_fetch_array($result_1);
				
				$image_1 = $row_1['image'];
				$name_1 = $row_1['name'];
				
				foreach ($products_2 as $product_2) {
					$product_2_id = $product_2->getID();
					
					if ($product_2_id != $product_1_id) {
						$superclasses_2 = $product_2->getSuperclasses();
						$properties_2 = $product_2->getProperties();
						
						$result_2 = mysql_query('SELECT * FROM products WHERE id='.$product_2_id);
						$row_2 = mysql_fetch_array($result_2);
						
						$image_2 = $row_2['image'];
						$name_2 = $row_2['name'];
						
						echo '<div id="product_image"><img width="100" src="../'.$image_1.'" alt="'.$name_1.'" />
						&nbsp;<img width="100" src="../'.$image_2.'" alt="'.$name_2.'" /></div>
						<div id="description" style="font-size: 10px;">';
						
						$properties_1_length = count($properties_1);
						$properties_2_length = count($properties_2);
						
						$result = 0;
						$count = 0;
						
						$coverage_sim = $owl->computeCoverageDistance($superclasses_1, $superclasses_2);
						
						echo 'Coverage Similarity = <strong>'.$coverage_sim.'</strong><br />';
						
						$layer_1 = $owl->findLayer($superclasses_1);
						$layer_2 = $owl->findLayer($superclasses_2);
						
						$layer_sim = 0;
						
						if ($layer_1 == $layer_2) {
							$layer_sim = 1;
						}
						
						echo 'Layer Similarity = <strong>'.$layer_sim.'</strong><br />';
						
						foreach ($properties_1 as $property_1) {
							$range_1 = $property_1->getRange();
							
							foreach ($properties_2 as $property_2) {
								if ($property_1->getName() == $property_2->getName()) {
									$count++;
									$range_2 = $property_2->getRange();
									
									echo '<br />'.$property_1->getName().'('.$range_1.') vs. '.$property_2->getName().'('.$range_2.') -> ';
									$sim = $owl->computeIndividualDistance($range_1, $range_2);
									$result += $sim;
									
									echo $sim.'<br />';
								}	
							}
						}
						
						$properties_1_diff = $properties_1_length - $count;
						$properties_2_diff = $properties_2_length - $count;
						
						$all_result = $result / ($count + $properties_1_diff + $properties_2_diff);				
						
						echo '<br />'.$result.' / ('.$count.' + '.$properties_1_diff.' + '.$properties_2_diff.') = <strong>'.$all_result.'</strong></div><div class="clearing">&nbsp;</div>';
						
						mysql_query('INSERT INTO similarities (product_id, rec_product_id, similarity, coverage, layer) VALUES ('.$product_1_id.', '.$product_2_id.', '.$all_result.', '.$coverage_sim.', '.$layer_sim.')');
					}
				}
			
			}			
		}
	}
} else {
	echo('Failed to open file.');
}
?>
        </div>
        <div class="clearing">&nbsp;</div>
    </div>
</div>
</body>
</html>

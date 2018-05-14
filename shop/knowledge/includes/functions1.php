<?php

function createDBEntry($file, $category) {
	$cat_array = getCategories($category);
	if (is_array($cat_array)) {
		$handle = fopen($file, 'r');
		$file_content = fread($handle, filesize($file));
		
		preg_match_all('/(<h3[^>]*txDesign[^>]*>)(.*?)(<\/h3>)/s', $file_content, $matches, PREG_SET_ORDER);
		foreach ($matches as $val) {
			$designer = $val[2];
			echo 'Designer: '.$val[2].'<br />';
		}

		preg_match_all('/(<h3[^>]*txDescription[^>]*>)(.*?)(<\/h3>)/s', $file_content, $matches, PREG_SET_ORDER);
		foreach ($matches as $val) {
			$title = $val[2];
			echo 'Titel: '.$val[2].'<br />';
		}
		
		preg_match_all('/(<span[^>]*prz_val[^>]*>)(.*?)(<\/span>)/s', $file_content, $matches, PREG_SET_ORDER);
		foreach ($matches as $val) {
			$price = $val[2];
			echo 'Preis: '.$val[2].'<br />';
		}

		preg_match_all('/(<h3[^>]*txtDescr[^>]*>)(.*?)(<\/h3>)/s', $file_content, $matches, PREG_SET_ORDER);
		foreach ($matches as $val) {
			$desc = $val[2];
			echo 'Beschreibung: '.$val[2].'<br />';
		}
		
		preg_match_all('/<img src="([^"]*)"[^>]*scheda_prod_img[^>]*>/s', $file_content, $matches, PREG_SET_ORDER);
		foreach ($matches as $val) {
			$bild_medium = str_replace('../../../images', 'products', $val[1]);
			$bild_small = str_replace('medium', 'small', $bild_medium);
			echo 'Bilder:<br /><img src="../'.$bild_medium.'" />&nbsp<img src="../'.$bild_small.'" /><br />';
		}
		
		$query = 'INSERT INTO products (cat, subcat, gruppe, designer, name, description, price, image) VALUES ("'.$cat_array[0].'", "'.$cat_array[1].'", "'.$cat_array[2].'", "'.$designer.'", "'.$title.'", "'.$desc.'", "'.$price.'", "'.$bild_medium.'")';
		mysql_query($query);
		
		echo $query.'<br />';
		echo mysql_errno().'<br />';
		echo mysql_error().'<br />';
	}
}

function getCategories($category) {
	switch ($category) {
		case 'hemden':
			$kat = 'Kleidung';
			$subcat = 'Oberteile';
			$group = 'Hemden';
			break;
		/*case 'hosen':
			$kat = 'Kleidung';
			$subcat = 'Hosen';
			$group = '';
			break;
		case 'jacken':
			$kat = 'Kleidung';
			$subcat = 'Jacken und Mäntel';
			$group = 'Jacken';
			break;
		case 'jumpsuit':
			$kat = 'Kleidung';
			$subcat = 'Sonstiges';
			$group = 'Jumpsuit';
			break;
		case 'kleider':
			$kat = 'Kleidung';
			$subcat = 'Kleider';
			$group = '';
			break;
		case 'mÃ¤ntel':
			$kat = 'Kleidung';
			$subcat = 'Jacken und Mäntel';
			$group = 'Mäntel';
			break;*/
		case 'pullover':
			$kat = 'Kleidung';
			$subcat = 'Pullover';
			$group = '';
			break;
		case 'top':
			$kat = 'Kleidung';
			$subcat = 'Oberteile';
			$group = 'Tops';
			break;
		case 't-shirts':
			$kat = 'Kleidung';
			$subcat = 'Oberteile';
			$group = 'T-Shirts';
			break;
		/*case 'jeans':
			$kat = 'Kleidung';
			$subcat = 'Hosen';
			$group = 'Jeans';
			break;
		case 'sportjacken':
			$kat = 'Kleidung';
			$subcat = 'Jacken und Mäntel';
			$group = 'Sportjacken';
			break;
		case 'weste':
			$kat = 'Kleidung';
			$subcat = 'Pullover';
			$group = 'Westen';
			break;*/
		case 'sneaker':
			$kat = 'Schuhe';
			$subcat = 'Sneaker';
			$group = '';
			break;
		case 'stiefel':
			$kat = 'Schuhe';
			$subcat = 'Stiefel';
			$group = '';
			break;
		case 'sandalen':
			$kat = 'Schuhe';
			$subcat = 'Sandalen';
			$group = '';
			break;
		case 'absatzschuhe':
			$kat = 'Schuhe';
			$subcat = 'Absatzschuhe';
			$group = '';
			break;		
		case 'schnÃ¼rschuhe':
			$kat = 'Schuhe';
			$subcat = 'Schnürschuhe';
			$group = '';
			break;
		case 'sweatshirt':
			$kat = 'Kleidung';
			$subcat = 'Pullover';
			$group = 'Sweatshirts';
			break;
		/*case 'lederjacken':
			$kat = 'Kleidung';
			$subcat = 'Jacken und Mäntel';
			$group = 'Lederjacken';
			break;
		case 'schal':
			$kat = 'Accessoires';
			$subcat = 'Schals';
			$group = '';
			break;
		case 'stola':
			$kat = 'Accessoires';
			$subcat = 'Stola';
			$group = '';
			break;*/
		case 'rÃ¶cke':
			$kat = 'Kleidung';
			$subcat = 'Röcke';
			$group = '';
			break;
		/*case 'gÃ¼rtel':
			$kat = 'Accessoires';
			$subcat = 'Gürtel';
			$group = '';
			break;
		case 'shorts':
			$kat = 'Kleidung';
			$subcat = 'Hosen';
			$group = 'Shorts';
			break;*/
		case 'tank+top':
			$kat = 'Kleidung';
			$subcat = 'Oberteile';
			$group = 'Tank Tops';
			break;
		/*case 'armreifen':
			$kat = 'Schmuck';
			$subcat = 'Armreifen';
			$group = '';
			break;
		case 'trench':
			$kat = 'Kleidung';
			$subcat = 'Jacken und Mäntel';
			$group = 'Trenchcoats';
			break;
		case 'ohrringe':
			$kat = 'Schmuck';
			$subcat = 'Ohrringe';
			$group = '';
			break;
		case 'ketten':
			$kat = 'Schmuck';
			$subcat = 'Ketten';
			$group = '';
			break;
		case 'ringe':
			$kat = 'Schmuck';
			$subcat = 'Ringe';
			$group = '';
			break;
		case 'anhÃ¤nger':
			$kat = 'Schmuck';
			$subcat = 'Anhänger';
			$group = '';
			break;
		case 'hÃ¼te':
			$kat = 'Accessoires';
			$subcat = 'Hüte';
			break;
		case 'clutches':
			$kat = 'Accessoires';
			$subcat = 'Taschen';
			$group = 'Clutches';
			break;
		case 'handtaschen':
			$kat = 'Accessoires';
			$subcat = 'Taschen';
			$group = 'Handtaschen';
			break;
		case 'pelzmÃ¤ntel':
			$kat = 'Kleidung';
			$subcat = 'Jacken und Mäntel';
			$group = 'Pelzmäntel';
			break;
		case 'schultertaschen':
			$kat = 'Accessoires';
			$subcat = 'Taschen';
			$group = 'Schultertaschen';
			break;
		case 'totes':
			$kat = 'Accessoires';
			$subcat = 'Taschen';
			$group = 'Totes';
			break;*/
	}
	if ($kat != '') {
		$result = array($kat, $subcat, $group);
	}
	return $result;
}
?>

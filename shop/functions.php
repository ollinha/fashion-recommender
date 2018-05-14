<?php
function priceToCents($price) {
	$split1 = explode('-', $price);
	$price = $split1[0];
	$price = trim(str_replace('EURO', '', $price));
	$split2 = explode('.', $price);
	$result = $split2[0];
	if (strlen($split2[1]) == 0) {
		$result .= '00';
	} elseif (strlen($split2[1]) == 1) {
		$result .= $split2[1];
		$result .= '0';
	} else {
		$result .= $split2[1];
	}
	return $result;
}

function priceToShow($price) {
	if (strlen($price) == 1) {
		$price = '00'.$price;
	}
	if (strlen($price) == 2) {
		$price = '00'.$price;
	}
	$c1 = substr($price, 0, strlen($price) - 2);
	$c2 = substr($price, strlen($price) - 2, strlen($price) - 1);
	$result = $c1.','.$c2.' €';
	return $result;
}

function dateToShow($date) {
	$split1 = explode('T', $date);
	$split2 = explode('-', $split1[0]);
	$result = $split2[2].'.'.$split2[1].'.'.$split2[0];
	return $result;
}

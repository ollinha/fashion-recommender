<?php

class Property {
	private $name;
	private $p_range;
	
	function __construct($n, $r) {
		$this->name = $n;
		$this->p_range = $r;
	}
	
	function getName() {
		return $this->name;
	}
	
	function getRange() {
		return $this->p_range;
	}	
}

?>

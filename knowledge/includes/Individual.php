<?php

class Individual {
	private $name;
	private $superclasses;
	
	function __construct($n, $sc) {
		$this->superclasses = $sc;
		$this->name = $n;
	}
	
	function getName() {
		return $this->name;
	}
	
	function getSuperclasses() {
		return $this->superclasses;
	}	
}

?>

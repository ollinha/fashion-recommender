<?php

class Concept {
	private $superclasses;
	private $name;
	
	function __construct($n, $sc) {
		$this->superclasses = $sc;
		$this->name = $n;
	}
	
	function getSuperclasses() {
		return $this->superclasses;
	}
	
	function getName() {
		return $this->name;
	}
}

?>

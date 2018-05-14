<?php

class Product {
	private $name;
	private $superclasses;
	private $properties;
	private $id;
	
	function __construct($n, $sc, $i, $p) {
		$this->superclasses = $sc;
		$this->name = $n;
		$this->properties = $p;
		$this->id = $i;
	}
	
	function getName() {
		return $this->name;
	}
	
	function getID() {
		return $this->id;
	}
	
	function getSuperclasses() {
		return $this->superclasses;
	}
	
	function getProperties() {
		return $this->properties;
	}
}
?>

<?php

class OWLDocument extends DOMDocument {
	private $individuals;
	private $products;
	private $concepts;
	private $properties;
	private $topconcepts = array('ArmForm', 'Body', 'Color', 'Coverage', 'Designer', 'Detail', 'Fashion',
	'Layer', 'Textile', 'NeckForm','LegForm', 'Orientation', 'Symmetry', 'Width', 'Height', 'Size', 'Form', 'Pattern');
	
	function init() {
		$this->setProperties();
		$this->setClasses();
		$this->setIndividuals();
	}
	
	function setProperties() {
		//echo '<br /><h3>Properties:</h3><br />';
		$children = $this->getElementsByTagNameNS('http://www.w3.org/2002/07/owl#', 'ObjectProperty');
		$this->properties = array();
		foreach ($children as $child) {
			$uri = $child->getAttributeNS('http://www.w3.org/1999/02/22-rdf-syntax-ns#', 'about');
			if ($uri != 'http://www.w3.org/2002/07/owl#topObjectProperty') {
				$name = str_replace('http://master.yanenko.de/fashion_ontology.owl#', '', $uri);
				if ($name != 'covers') {
					$this->properties[] = $name;
					//echo $name.'<br />';
				}
			}
		}
	}
	
	function setClasses() {
		//echo '<br /><h3>Classes:</h3><br />';
		$children = $this->getElementsByTagNameNS('http://www.w3.org/2002/07/owl#', 'Class');
		$this->concepts = array();
		foreach ($children as $child) {
			$uri = $child->getAttributeNS('http://www.w3.org/1999/02/22-rdf-syntax-ns#', 'about');
			if ($uri != '') {
				$name = str_replace('http://master.yanenko.de/fashion_ontology.owl#', '', $uri);
				//echo '<strong>'.$name.'</strong><br />&nbsp;&nbsp;<em>Superclasses:</em><br />';
				$subclassof = $child->getElementsByTagNameNS('http://www.w3.org/2000/01/rdf-schema#', 'subClassOf');
				$superclasses = array();
				foreach ($subclassof as $superclass) {
					$scuri = $superclass->getAttributeNS('http://www.w3.org/1999/02/22-rdf-syntax-ns#', 'resource');
					$scname = str_replace('http://master.yanenko.de/fashion_ontology.owl#', '', $scuri);
					//echo '&nbsp;&nbsp;'.$scname.'<br />';
					$superclasses[] = $scname;
				}
				$this->concepts[] = new Concept($name, $superclasses);
			}
		}
	}
	
	function setIndividuals() {
		$children1 = $this->getElementsByTagNameNS('http://www.w3.org/2002/07/owl#', 'Thing');
		$children2 = $this->getElementsByTagNameNS('http://www.w3.org/2002/07/owl#', 'NamedIndividual');
		$this->products = array();
		$this->individuals = array();
		//echo '<br /><h3>Individuals:</h3><br />';
		foreach ($children1 as $child) {
			$this->setIndividual($child);
		}
		foreach ($children2 as $child) {
			$this->setIndividual($child);
		}
	}
	
	function setIndividual($child) {
		$uri = $child->getAttributeNS('http://www.w3.org/1999/02/22-rdf-syntax-ns#', 'about');
		$name = str_replace('http://master.yanenko.de/fashion_ontology.owl#', '', $uri);
		if (substr($name, 0, 8) == 'Product_') {
			$id = str_replace('Product_', '', $name);
			//echo '<strong>'.$name.'</strong>(ID: '.$id.')<br />&nbsp;&nbsp;<em>Superclasses:</em><br />';
		} else {
			//echo '<strong>'.$name.'</strong><br />&nbsp;&nbsp;<em>Superclasses:</em><br />';
		}
		$superclasses = array();
		$types = $child->getElementsByTagNameNS('http://www.w3.org/1999/02/22-rdf-syntax-ns#', 'type');
		foreach ($types as $type) {
			$scuri = $type->getAttributeNS('http://www.w3.org/1999/02/22-rdf-syntax-ns#', 'resource');
			if ($scuri != 'http://www.w3.org/2002/07/owl#NamedIndividual') {
				$scname = str_replace('http://master.yanenko.de/fashion_ontology.owl#', '', $scuri);
				if (!array_search($scname, $this->topconcepts)) {
					//echo '&nbsp;&nbsp;'.$scname.'<br />';
					$superclasses[] = $scname;
				}
			}
		}
		if (substr($name, 0, 8) == 'Product_') {
			$p = $this->setIndividualProperties($child);
			$this->products[] = new Product($name, $superclasses, $id, $p);
		} else {
			$this->individuals[] = new Individual($name, $superclasses); 
		}
	}
	
	function setIndividualProperties($child) {
		$prop = array();
		//echo '&nbsp;&nbsp;<em>Properties:</em><br />';
		foreach ($this->properties as $value) {
			$props = $child->getElementsByTagName($value); 
			if (count($props) > 0) {
				foreach ($props as $pr) {
					$ruri = $pr->getAttributeNS('http://www.w3.org/1999/02/22-rdf-syntax-ns#', 'resource');
					$r = str_replace('http://master.yanenko.de/fashion_ontology.owl#', '', $ruri);
					//echo '&nbsp;&nbsp;'.$value.'('.$r.')<br />';
					$prop[] = new Property($value, $r);
				}
			}
		}
		return $prop;
	}
	
	function computeIndividualDistance($name_1, $name_2) {
		if ($name_1 == $name_2) {
			return 1;
		} else {
			$individual_1 = $this->findIndividual($name_1);
			$superclasses_1 = $individual_1->getSuperclasses();
			
			$individual_2 = $this->findIndividual($name_2);
			$superclasses_2 = $individual_2->getSuperclasses();
			
			$sim = $this->computeDistance($superclasses_1, $superclasses_2);
			
			return $sim;
		}
	}
	
	function computeDistance($superclasses_1, $superclasses_2) {
		$length_1 = count($superclasses_1);
		$length_2 = count($superclasses_2);
		
		$intersection_array = array_intersect($superclasses_1, $superclasses_2);
		$intersection = count($intersection_array);
		
		$union = $intersection + ($length_1 - $intersection) + ($length_2 - $intersection);
		
		if ($union != 0) {
			$sim = $intersection / $union;
		} else {
			$sim = 0;
		}
		
		return $sim;
	}
	
	function computeCoverageDistance($superclasses_1, $superclasses_2) {
		$coverage_1 = $this->filterSuperclasses($superclasses_1);
		$coverage_2 = $this->filterSuperclasses($superclasses_2);
		
		$result = $this->computeDistance($coverage_1, $coverage_2);
		return $result;
	}
	
	function filterSuperclasses($superclasses) {
		$coverage = array();
		
		foreach ($superclasses as $superclass) {
			if (preg_match('/^[L|U][0-9]+/', $superclass) == 1) {
				$coverage[] = $superclass;
			}
		}
		
		return $coverage;
	}
	
	function findLayer($superclasses) {
		$result = 0;
		foreach ($superclasses as $superclass) {
			if (substr($superclass, 0, 5) == 'Layer') {
				$result = substr($superclass, 5, 1);
				return $result;
			}
		}
		return $result;
	}
	
	function findConcept($name) {
		foreach ($this->concepts as $concept) {
			if ($concept->getName() == $name) {
				return $concept;
			}
		}
	}
	
	function findIndividual($name) {
		foreach ($this->individuals as $individual) {
			if ($individual->getName() == $name) {
				return $individual;
			}
		}
	}
	
	function getProducts() {
		return $this->products;
	}
	
	function getConcepts() {
		return $this->concepts;
	}
	
	function getIndividuals() {
		return $this->individuals;
	}
	
	function getProperties() {
		return $this->properties;
	}
}

?>

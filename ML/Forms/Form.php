<?php

namespace ML\Forms;


class Form extends Element\Form {


	public function __construct($name = '') {
		parent::__construct($name);
	}

	/**
	 *
	 * @param $type
	 * @param $name
	 * @param $label
	 * @param $validators
	 * @return AF_FormElement
	 */
	public function add($type, $name, $label = false, $validators = array()) {
		$class = $type;
		$class[0] = strtoupper($type[0]);
		$class = "Element\{$class}";
		if (class_exists($class)) {
			$object = new $class($label, $validators);
			if ($type == 'multipleselect')
				$name .= '[]'; // multipleselect musi być tablicą
			$this->addElement($name, $object);
			if ($type == "file") {
				$this->enctype = 'multipart/form-data';
			}
			return $object;
		}
		else {
			throw new AF_UnknownClassException("Class $class not found!");
		}
	}



}


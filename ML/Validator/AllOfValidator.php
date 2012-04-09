<?php

namespace ML\Validator;


class AllOf extends Validator {
	
	
	private $validators = array();
	
	
	public function __construct(array $validators, $error = '') {
		parent::__construct($error);
		$error = strlen($error)?$error:'All of validator...';
		$this->setError($error);
		$this->validators = $validators;
	}
	
	
	public function validate(AF_Element $element) {
		foreach ($this->validators as $validator) {
			if (!$validator->validate($element)) {
				return false;
			}
		}
		return true;
	}
	

	
	
}


?>
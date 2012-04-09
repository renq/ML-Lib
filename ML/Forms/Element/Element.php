<?php

namespace ML\Forms\Element;


abstract class Element extends Base {
	
	
	private $validators = array();
	
	private $validateErrors = array();
	
	private $label = false;
	
	private $form = null;
	
	

	/**
	 * The constructor.
	 * 
	 * @param string $label label
	 * @param array $validators array of validators
	 */
	public function __construct($name = '') {
		parent::__construct($name);
	}
	
	
	/**
	 * Validate form.
	 * 
	 * @return boolean validate result
	 */
	public function validate() {
		$result = true;
		$this->validateErrors = array();
		
		foreach ($this->validators as $validator) {
			$tmp = $validator->validate($this);
			if (!$tmp) {
				$result = false;
				$this->addError($validator->getError()); 
			}
		}
		return $result;
	}

	
	/**
	 * Return label.
	 * 
	 * @return string
	 */
	public function getLabel() {
		return $this->label;
	}
	
	
	/**
	 * Set label.
	 * 
	 * @param string $label
	 */
	public function setLabel($label) {
		$this->label = $label;
		return $this;
	}
	
	
	public function setValue($value) {
	    $this->setAttribute('value', $value);
	    return $this;
    }
    
    
    public function getValue() {
        return $this->getAttribute('value');
    }

	
	/**
	 * Return HTML code.
	 * 
	 * @return string
	 */
	public abstract function show();
	
	
	/**
	 * Add validator.
	 * 
	 * @param AF_Validator $validator
	 */
	public function addValidator(AF_Validator $validator) {
		$this->validators[] = $validator;
	}
	
	
	/**
	 * Gets an array of validators.
	 * @return array
	 */
	public function getValidators() {
		return $this->validators;
	}
	
	
	/**
	 * Convert all applicable characters to HTML entities.
	 * 
	 * @param string $text
	 * @return string
	 */
	protected function valueEntities($text) {
		return htmlentities($text, ENT_COMPAT, 'UTF-8');
	}
	
	
	/**
	 * Return attribute string.
	 * 
	 * @return string
	 */
	protected function getAttributesString() {
		$result = '';
		foreach ($this->getAttributes() as $k => $v) {
			if (!is_array($v)) {
				$v = $this->valueEntities($v);
				$result .= "$k=\"$v\" ";
			}
		}
		return trim($result);
	}
	
	
	
	/**
	 * Set a form.
	 * 
	 * @param FormElement $form
	 */
	public function setForm(Form $form) {
		$this->form = $form;
	}
	
	
	/**
	 * Return form.
	 * 
	 * @return FormElement
	 */
	public function getForm() {
		return $this->form;
	}
	
	
	/**
	 * Add validation error.
	 * 
	 * @param string $error
	 */
	public function addError($error) {
		$this->validateErrors[] = $error;
	}

	
	/**
	 * Return all validation errors
	 * 
	 * @return array array of string
	 */
	public function getErrors() {
		return $this->validateErrors;
	}
	
	
	public function getHtmlName() {
		return $this->getName();
	}
	
	
	/**
	 * 
	 * @return boolean
	 */
	public abstract function isSent();
	
	
	/**
	 * Set values from request.
	 * 
	 */
	public abstract function setFromRequest();
	
	
	/**
	 * Gets value from request.
	 * @return string|NULL
	 */
	protected function getValueFromRequest() {
		$data = $this->getForm()->getRequest();
		$name = $this->getName();
		$matches = array();
		preg_match_all('/\[[^\]]+\]/', $name, $matches);
		$parts = $matches[0];
		$first = $name;
		if ($pos = strpos($name, '[')) {
			$first = substr($name, 0, $pos);
		}
		array_unshift($parts, $first);
		$tmp = $data;
		foreach ($parts as $v) {
			$v = trim($v, '[]');
			if (array_key_exists($v, $tmp)) {
				if (is_array($tmp[$v])) {
					$tmp = $tmp[$v];
				}
				else {
					return $tmp[$v];
				}
			}
		}
		return null;
	}
	
	
	/**
	 * Add element to form.
	 * 
	 * @param FormElement $form
	 * @param string $name unique element name
	 */
	public function addTo(AF_FormElement $form, $name) {
		$form->addElement($name, $this);
	}
	
	
	public function __toString() {
		return $this->show();
	}

	
}


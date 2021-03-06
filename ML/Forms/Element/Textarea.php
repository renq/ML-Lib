<?php

namespace ML\Forms\Element;


class Textarea extends Element {

	
	public function __construct($label = false, $validators = array()) {
		parent::__construct($label, $validators);
	}
	
	
	public function isSent() {
		if ($data = $this->getForm()->getRequest()) {
			if (array_key_exists($this->getName(), $data)) {
				return true;
			}
		}
		return false;
	}
	
	
	public function setFromRequest() {
		if ($value = $this->getValueFromRequest()) {
			$this->value = $value;
		}
	}
	
	
	protected function getAttributesString() {
		$result = '';
		foreach ($this->getAttributes() as $k => $v) {
			if (!is_array($v) && $k != 'value') {
				$v = $this->valueEntities($v);
				$result .= "$k=\"$v\" ";
			}
		}
		return trim($result);
	}
	
	
	public function show() {
		return <<<EOD
<textarea {$this->getAttributesString()}>{$this->valueEntities($this->value)}</textarea>
EOD;
		 
	}
	

}
 


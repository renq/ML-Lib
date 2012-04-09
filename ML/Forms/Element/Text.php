<?php

namespace ML\Forms\Element;


class Text extends Element {

	
	public function __construct($name) {
		parent::__construct($name);
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
		$value = $this->getValueFromRequest();
		if ($value != '') {
			$this->value = $value;
		}
	}
	
	
	public function show() {
		return <<<EOD
<input type="text" {$this->getAttributesString()} />
EOD;
		 
	}
	

}
 


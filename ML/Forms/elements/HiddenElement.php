<?php


class AF_HiddenElement extends AF_Element {

	
	public function __construct() {
		parent::__construct();
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
	
	
	public function show() {
		return <<<EOD
<input type="hidden" {$this->getAttributesString()} />
EOD;
		 
	}
	

}
 

?>
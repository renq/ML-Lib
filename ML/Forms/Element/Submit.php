<?php

namespace ML\Forms\Element;


class Submit extends Element {

	
	public function __construct($label = false, $validators = array()) {
		parent::__construct($label, $validators);
	}
	
	
	public function isSent() {
		return true;
	}
	
	
	public function setFromRequest() {
				
	}
	
	
	public function show() {
		return <<<EOD
<input class="submit" type="submit" {$this->getAttributesString()} />
EOD;
		 
	}
	

}
 

?>
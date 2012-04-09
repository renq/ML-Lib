<?php

namespace ML\Forms\Element;


class Password extends Text {

	
	public function __construct($label = false, $validators = array()) {
		parent::__construct($label, $validators);
	}
	
	
	public function show() {
		return <<<EOD
<input type="password" {$this->getAttributesString()} />
EOD;
		 
	}
	

}
 

?>
<?php

namespace ML\Forms\Element;


class RadioSet extends Element {
	
	
	private $options;

	
	public function __construct($label = false, $validators = array()) {
		parent::__construct($label, $validators);
	}
	
	
	public function isSent() {
		return true;
	}
	
	
	public function setFromRequest() {
		if ($value = $this->getValueFromRequest()) {
			$this->value = $value;
		}
	}
	
	
	public function setOptions($data) {
		$this->options = $data;
	}
	
	
	public function getOptions() {
		return $this->options;
	}
	
	
	public function show() {
		$html = <<<EOD
<ul {$this->getAttributesString()}>

EOD;
		foreach ($this->getOptions() as $k => $v) {
			$selected = ($k==$this->value && strlen($k) == strlen($this->value))?'checked="checked" ':'';
			$html .= <<<EOD

	<li><input type="radio" name="{$this->getHtmlName()}" value="{$k}" {$selected}/> {$v}</li> 
EOD;
		}
		$html .= <<<EOD

</ul>
EOD;
		return $html;
		 
	}
	

}
 

?>
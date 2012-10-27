<?php

if (!class_exists('Template')) {
	die("Class Template is required.");
}

class Template extends Layout {
	
	
	protected $tpl = null; 
	
	
	public function __construct($tplFile) {
		parent::__construct();
		$this->tpl = new Template($tplFile);
	}
	

	
	protected final function showElements() {
		$this->tpl->elements = $this->getForm()->getElements();
		return $this->tpl;
	}
	
	
	public function getTemplate() {
		return $this->tpl;
	}
	
	
}


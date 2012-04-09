<?php

namespace ML\Forms\Element;


/**
 * TODO scalić z select, zrobić ewentualnie opcję strict... Taki pomysł :)
 * @author renq
 *
 */
class Dynamicselect extends Select {


	public function setFromRequest() {
		$value = $this->getValueFromRequest();
		if (strlen($value)) {
			$this->value = $value;
		}
	}

//	public function setFromRequest() {
//		try {
//			parent::setFromRequest();
//		}
//		catch (AF_OutOfBoundsException $e) {
//
//		}
//	}


}



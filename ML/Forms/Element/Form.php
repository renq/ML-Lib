<?php

namespace ML\Forms\Element;


use ML\Forms\Layout\Layout;

abstract class Form extends Base {


	private $elements;

	private $formErrors = array();

	private $isSent = null;

	private $isStarted = false;

	private $request = null;

	private $layout = null;

	public static $defaultLayout = '\ML\Forms\Layout\Paragraph';


	/**
	 * The constructor.
	 *
	 */
	public function __construct($name = '') {
	    if (!$name) $name = $this->generateName();
		parent::__construct($name);

		$this->method = 'post';

		$hidden = new Hidden($this->getName());
		$hidden->value = 'ok';
		$this->addElement($hidden);
	}
	
	
	private function generateName() {
	     return preg_replace('/[^0-9A-Z\-\_]/i', '-', get_class($this));
	}


	/**
	 * Return true if form is submitted and false otherwise.
	 *
	 * @return boolean
	 */
	public function isSent() {
		if (gettype($this->isSent) != "boolean") {
			$name = $this->getName();
			try {
				$element = $this->getElement($name);
				return $element->isSent();
			}
			catch (AF_OutOfBoundsException $e) {
				$this->isSent = false;
			}
			/*
			$result = true;
			foreach ($this->getElements() as $k => $v) {
				if (!$v->isSent()) {
					$result = false;
					break;
				}
			}
			*/
		}
		return $this->isSent;
	}


	/**
	 * Start form.
	 *
	 */
	public function start() {
		if (!$this->isStarted) {
			if ($this->isSent()) {
				$name = $this->getName();
				foreach ($this->getElements() as $k => $v) {
					$v->setFromRequest();
				}
			}
		}
	}


	/**
	 * Return data from POST/GET.
	 *
	 * @return array
	 */
	public function getRequest() {
		$name = $this->getName();
		if (array_key_exists($name, $_REQUEST)) {
			if (empty($this->request)) {
				$request = array();
				/*foreach ($_REQUEST as $k => $v) {
					if ($this->isElement($k)) {
						$request[$k] = $v;
					}
				}
				$this->request = $request;*/
				$this->request = $_REQUEST;
			}
			return $this->request;
		}
		return false;
	}


	/**
	 * Validate form.
	 *
	 * @return boolean
	 */
	public function validate() {
		$this->start();
		if ($this->isSent()) {
			$elements = $this->getElements();
			$result = true;
			foreach ($elements as $k => $v) {
				$tmp = $v->validate();
				if (!$tmp) {
					$result = false;
				}
			}
			$resultCustom = $this->customValidator();
			$formErrors = $this->getFormErrors();
			return $result && $resultCustom && empty($formErrors);
		}
		return null;
	}


	/**
	 * Return array of all form elements.
	 *
	 * @return array of ELement
	 */
	public function getElements() {
		return $this->elements;
	}


	/**
	 * Add element to the form.
	 *
	 * @param string $elementName
	 * @param Element $element
	 */
	public function addElement(Element $element) {
		$element->setForm($this);
		$this->elements[$element->getName()] = $element;
	}


	/**
	 * Get element.
	 *
	 * @param string $elementName
	 * @return Element
	 */
	public function getElement($elementName) {
		if (array_key_exists($elementName, $this->elements)) {
			return $this->elements[$elementName];
		}
		throw new AF_OutOfBoundsException("Element '$elementName' not exists!");
	}


	/**
	 * Returns true if element exists.
	 * @param string $elementName
	 * @return boolean
	 */
	public function isElement($elementName) {
		return array_key_exists($elementName, $this->elements);
	}


	/**
	 * Render form
	 *
	 * @param Layout $layout
	 * @return string
	 */
	public function show(Layout $layout = null) {
		if (!$layout instanceof Layout) {
			if ($this->layout instanceof Layout) {
				$layout = $this->layout;
			}
			else {
				$layout = new self::$defaultLayout;
			}
		}
		return $layout->show($this);
	}


	/**
	 * Return array of error.
	 *
	 * @return array of string
	 */
	public function getErrors() {
		$result = array();
		foreach ($this->getElements() as $element) {
			$result = array_merge($result, $element->getErrors());
		}
		return $result;
	}


	/**
	 * Return submited request data - like $_REQUEST.
	 *
	 * @return array
	 */
	public function getData() {
		$result = array();
		foreach ($this->getElements() as $name => $element) {
			$value = urlencode($element->value);
			parse_str("{$name}={$value}", $tmp);
			$result = $this->arrayMergeRecursive($result, $tmp);
			//$result = array_merge_recursive();
		}
		return $result;
	}


	/**
	 * Return array similar to $_FILES array.
	 *
	 * @return array
	 */
	public function getFileData() {
		return empty($_FILES)?array():$_FILES;
	}


	private function isNumberIndexedArray($array) {
		$count = count($array);
		if (empty($array) || (isset($array[0]) && isset($array[$count-1]))) {
			return true;
		}
		return false;
	}


	/**
	 * Flat post array
	 * @param array $data
	 * @param string $keyString
	 * @param array $result
	 */
	private function flatArray($data, $keyString = '', &$result = array()) {
		foreach ($data as $k => $v) {
			$newKeyString = strlen($keyString)?"{$keyString}[$k]":$k;
			if (is_array($v) && !$this->isNumberIndexedArray($v)) {
				$result = $result + $this->flatArray($v, $newKeyString);
			}
			else {
				$result[$newKeyString] = $v;
			}
		}
		return $result;
	}


	/**
	 * Seting a values for element. $data is an associative array, where key is a element name and value is element's value.
	 * Returns true if success.
	 * @param array $data
	 * $param boolean $strict
	 *
	 * @return boolean
	 */
	public function setData($data, $strict = false) {
		if (is_array($data)) {
			$data = $this->flatArray($data);
			foreach ($data as $name => $value) {
				if (is_array($value) /* && $this->isNumberIndexedArray($value)*/ ) {
					$name = $name . '[]';
				}

				if (!$this->isElement($name) && !$strict) {
					continue;
				}
				$element = $this->getElement($name);
				$element->value = $value;
			}
			return true;
		}
		return false;
	}


	/**
	 * Custom validator, good for overloading.
	 * @return boolean
	 */
	protected function customValidator() {
		return true;
	}


	/**
	 * Add form validation error.
	 *
	 * @param string $error
	 */
	public function addFormError($error) {
		$this->formErrors[] = $error;
	}


	/**
	 * Return array of validation errors.
	 *
	 * @return array of string
	 */
	public function getFormErrors() {
		return $this->formErrors;
	}


	public function setLayout(Layout $layout) {
		$this->layout = $layout;
	}


	public function getLayout() {
		return $this->layout;
	}


	private function __arrayMergeRecursive(&$result, $array, $keys = array()) {
		foreach ($array as $k => $v) {
			$newKeys = array_merge($keys, array($k));
			if (is_array($v)) {
				$this->__arrayMergeRecursive($result, $v, $newKeys);
			}
			else {
				$current = &$result;
				foreach ($keys as $key) {
					if (!isset($current[$key])) {
						$current[$key] = array();
					}
					$current = &$current[$key];
				}
				$current[$k] = $v;
			}
		}
	}


	/**
	 * Przyjmuje w parametrze N tablic i łączy je podobnie, do array_merge_recursive, ale z tym,
	 * że w przypadku tablic indeksowanych numerami, które nie są po kolei, zachowuje tą numerację, a nie pieprzy tablicę
	 * jak funkcja array_merge_recursive.
	 */
	protected function arrayMergeRecursive() {
		$numArgs = func_num_args();
		if ($numArgs == 0) {
			return false;
		}
		elseif ($numArgs == 1) {
			return func_get_arg(0);
		}
		else {
			$result = array();
			foreach (func_get_args() as $array) {
				$this->__arrayMergeRecursive($result, $array);
			}
			return $result;
		}
	}
	
	
	public function setFromRequest() {

	}


}


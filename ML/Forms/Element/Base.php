<?php

namespace ML\Forms\Element;


abstract class Base {


	private $attributes = array();

	private $name;



	/**
	 * The constructor.
	 *
	 */
	public function __construct($name = '') {
		$this->setName($name);
	}


	/**
	 * Set value for attribute
	 *
	 * @param string $attribute
	 * @param string $value
	 */
	public function setAttribute($attribute, $value) {
	    // TODO sprawdzać nazwę atrybutu
		$this->attributes[$attribute] = $value;
		return $this;
	}


	/**
	 *
	 * @see forms/elements/BaseElement#set()
	 *
	 */
	public function __set($attribute, $value) {
		return $this->setAttribute($attribute, $value);
	}


	/**
	 * Returns value for attribute
	 *
	 * @param string $attribute
	 * @return string
	 */
	public function getAttribute($attribute) {
		if (array_key_exists($attribute, $this->attributes)) {
			return $this->attributes[$attribute];
		}
		return '';
	}


	/**
	 *
	 * @see forms/elements/BaseElement#get()
	 *
	 */
	public function __get($attribute) {
		return $this->getAttribute($attribute);
	}


	/**
	 * Return values for all attributes.
	 *
	 * @return unknown_type
	 */
	public function getAttributes() {
		return $this->attributes;
	}


	/**
	 * Return element name.
	 *
	 * @return string
	 */
	public function getName() {
		return $this->getAttribute('name');
	}


	/**
	 * Set element name.
	 *
	 * @param string $name
	 */
	public function setName($name) {
		$this->setAttribute('name', $name);
	}


	/**
	 * @see forms/elements/BaseElement#show()
	 */
	public function __toString() {
		return $this->show();
	}



}



<?php namespace Bruha\Utils\Object;
/**
 * Store table column type information
 * @author Radek Brůha
 * @version 1.0
 */
class Type {
	public $name;
	public $length;
	public $extra;

	function __construct($name = NULL, $length = NULL, $extra = NULL) {
		$this->name = $name;
		$this->length = $length;
		$this->extra = $extra;
	}
}
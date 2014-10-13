<?php namespace Bruha\Utils\Object\Key;
/**
 * Store table column foreign key information
 * @author Radek Brůha
 * @version 1.0
 */
class ForeignKey {
	public $table;
	public $key;
	public $value;

	function __construct($table = NULL, $key = NULL, $value = NULL) {
		$this->table = $table;
		$this->key = $key;
		$this->value = $value;
	}
}
<?php namespace Bruha\Utils\Object\Key;
/**
 * Store table column foreign key information
 * @author Radek Brůha
 * @version 1.1
 */
class Foreign {
	public $table;
	public $key;
	public $value;

	function __construct($table, $key, $value) {
		$this->table = $table;
		$this->key = $key;
		$this->value = $value;
	}
}
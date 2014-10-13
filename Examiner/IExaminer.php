<?php namespace Bruha\Examiner;
/**
 * Database examiner interface
 * @author Radek Brůha
 * @version 1.0
 */
interface IExaminer {
	/**
	 * @param \Bruha\Utils\DatabaseConnection $database
	 * @param \stdClass $settings
	 */
	public function __construct(\Bruha\Utils\DatabaseConnection $database, \stdClass $settings);

	/**
	 * Gets list of database tables
	 * @retrun array
	 */
	public function getTables();
	
	/**
	 * Gets list of table columns
	 * @param string $tableName
	 * @return array
	 */
	public function getColumns($tableName);
	
	/**
	 * Gets list of column keys
	 * @param string $tableName
	 * @param string $columnName
	 * @return array
	 */
	public function getColumnKeys($tableName, $columnName);
	
	/**
	 * Gets list of column foreign keys
	 * @param type $tableName
	 * @param type $columnName
	 * @return array
	 */
	public function getColumnForeignKeys($tableName, $columnName);
}
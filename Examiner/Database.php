<?php namespace Bruha\Examiner;
/**
 * Database examination
 * @author Radek Brůha
 * @version 1.0
 */
class Database {
	/** @var \Utils\DatabaseConnection */
	private $database;

	/** @param \Utils\DatabaseConnection $database */
	public function __construct(\Bruha\Utils\DatabaseConnection $database) {
		$this->database = $database;
	}

	/** @return array of \Utils\Object\Table */
	public function getTables() {
		$query = $this->database->query('SHOW TABLE STATUS');
		$tables = [];
		while ($row = $query->fetch_object()) {
			$name = $row->Name;
			$comment = $row->Comment ? $row->Comment : FALSE;
			$columns = (new \Bruha\Examiner\Table($this->database, $name))->getColumns();
			$tables[] = new \Bruha\Utils\Object\Table($name, $comment, $columns);
		}
		return $tables;
	}
}
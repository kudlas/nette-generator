<?php namespace Bruha\Examiner;
/**
 * Database examination
 * @author Radek Brůha
 * @version 1.0
 */
class Database {
	/** @var \Utils\Database */
	private $database;

	/** @param \Utils\Database $database */
	public function __construct(\Bruha\Utils\Database $database) {
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
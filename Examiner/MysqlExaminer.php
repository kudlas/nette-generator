<?php namespace Bruha\Examiner;
/**
 * MySQL database examiner
 * @author Radek Brůha
 * @version 1.0
 */
class MysqlExaminer implements \Bruha\Examiner\IExaminer {
	/** @var Bruha\Utils\DatabaseConnection */
	private $database;
	/** @var array */
	private $tables = [];
	/** @var \stdClass */
	private $settings;

	/**
	 * @param \Bruha\Utils\DatabaseConnection $database
	 * @param \stdClass $settings
	 */
	public function __construct(\Bruha\Utils\DatabaseConnection $database, \stdClass $settings) {
		$this->database = $database;
		$this->settings = $settings;
	}

	/**
	 * Gets list of database tables
	 * @retrun array
	 */
	public function getTables() {
		foreach ($this->database->query('SHOW TABLE STATUS;')->fetchAll(\PDO::FETCH_NUM) as $t) $this->tables[] = new \Bruha\Utils\Object\Table($t[0], $t[17] ?: NULL, []);
		foreach ($this->tables as $t) $t->columns = $this->getColumns($t->name);
		foreach ($this->tables as $t) {
			$hasPK = FALSE;
			foreach ($t->columns as $c) foreach ($c->keys as $k) {
				if ($k instanceof \Bruha\Utils\Object\Key\PrimaryKey) $hasPK = TRUE;
				if ($k instanceof \Bruha\Utils\Object\Key\ForeignKey) $k->value = $this->getColumnForeignKeyValue($k);
			}
			$t->state = $hasPK ? \Utils\Constants::TABLE_STATE_OK : \Utils\Constants::TABLE_STATE_ERROR_NO_PRIMARY_OR_UNIQUE_KEY;
		}
		return $this->tables;
	}

	/**
	 * Gets list of table columns
	 * @param string $tableName
	 * @return array
	 */
	public function getColumns($tableName) {
		$columns = [];
		foreach ($this->database->query("SHOW FULL COLUMNS FROM $tableName;")->fetchAll(\PDO::FETCH_NUM) as $c) {
			$columns[] = new \Bruha\Utils\Object\Column($c[0], $this->getColumnType($c[1]), $c[3] !== 'NO', $this->getColumnKeys($tableName, $c[0]), $c[5], $c[6], $c[8]);
		}
		return $columns;
	}

	/**
	 * Gets list of column keys
	 * @param string $tableName
	 * @param string $columnName
	 * @return array
	 */
	public function getColumnKeys($tableName, $columnName) {
		$keys = [];
		foreach ($this->database->query("SHOW INDEX FROM $tableName WHERE Column_name = '$columnName';")->fetchAll(\PDO::FETCH_NUM) as $k) {
			if ($k[2] === 'PRIMARY') $keys[] = new \Bruha\Utils\Object\Key\PrimaryKey;
			if ((int)$k[1] === 0) $keys[] = new \Bruha\Utils\Object\Key\UniqueKey;
			if ((int)$k[1] === 1) $keys[] = new \Bruha\Utils\Object\Key\IndexKey;
			foreach ($this->getColumnForeignKeys($tableName, $columnName) as $f) $keys[] = $f;
		}
		return $keys;
	}

	/**
	 * Gets list of column foreign keys
	 * @param type $tableName
	 * @param type $columnName
	 * @return array
	 */
	public function getColumnForeignKeys($tableName, $columnName) {
		$keys = [];
		if ($this->settings->source === \Utils\Constants::SOURCE_MYSQL_DISCOVERED) {
			foreach ($this->database->query("SELECT REFERENCED_TABLE_NAME, REFERENCED_COLUMN_NAME FROM information_schema.KEY_COLUMN_USAGE WHERE TABLE_SCHEMA = DATABASE() AND REFERENCED_TABLE_NAME IS NOT NULL AND TABLE_NAME = '$tableName' AND COLUMN_NAME = '$columnName';")->fetchAll(\PDO::FETCH_BOTH) as $k) {
				foreach ($this->tables as $t) {
					if ($t->name === $k[0]) {
						$keys[] = new \Bruha\Utils\Object\Key\ForeignKey($t, $k[1]);
						break;
					}
				}
			}
		} else if ($this->settings->source === \Utils\Constants::SOURCE_MYSQL_CONVENTIONAL) {
			if (($position = mb_strrpos($columnName, '_')) !== FALSE && (int)$this->database->query("SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = '" . mb_substr($columnName, 0, $position) . "' AND COLUMN_NAME = '" . mb_substr($columnName, $position + 1) . "';")->fetch(\PDO::FETCH_NUM)[0] === 1) {
				foreach ($this->tables as $t) {
					if ($t->name === mb_substr($columnName, 0, $position)) {
						$keys[] = new \Bruha\Utils\Object\Key\ForeignKey($t, mb_substr($columnName, $position + 1));
						break;
					}
				}
			}
		}
		return $keys;
	}

	/**
	 * Gets column type
	 * @param string $columnType
	 * @return \Bruha\Utils\Object\Type
	 */
	private function getColumnType($columnType) {
		$type = new \Bruha\Utils\Object\Type;
		$type->length = NULL;
		$type->extra = NULL;
		if (($position = strpos($columnType, '(')) !== FALSE) {
			$type->name = substr($columnType, 0, $position);
			$type->length = (int)\Nette\Utils\Strings::match($columnType, '~\d+~')[0];
			if (strpos($columnType, 'unsigned') !== FALSE) $type->extra[] = 'unsigned';
			if (strpos($columnType, 'zerofill') !== FALSE) $type->extra[] = 'zerofill';
		} else $type->name = $columnType;
		if ($type->name === 'tinyint' && $type->length === 1) {
			$type->name = 'boolean';
			$type->length = 1;
		}
		if ($type->name === 'enum' || $type->name === 'set') {
			$type->extra = explode(',', str_replace(['enum(', 'set(', "'", ')'], '', $columnType));
			$type->length = count($type->extra);
		}
		return $type;
	}

	/**
	 * Gets column name which is shown instead of foreign key
	 * @param \Bruha\Utils\Object\Key\ForeignKey $key
	 * @return string
	 */
	private function getColumnForeignKeyValue(\Bruha\Utils\Object\Key\ForeignKey $key) {
		foreach ($key->table->columns as $c) {
			if (in_array($c->name, ['name', 'title'], TRUE)) return $c->name;
			if (in_array($c->type->name, ['varchar', 'char'])) return $c->name;
		}
		return count($key->table->columns) >= 2 ? $key->table->columns[1]->name : $key->table->columns[0]->name;
	}
}
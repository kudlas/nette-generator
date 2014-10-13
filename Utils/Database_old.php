<?php namespace Bruha\Utils;
/**
 * Database wrapper for MySQLi connection with basic exceptions handling
 * @author Radek Brůha
 * @version 1.0
 */
class Database_old extends \MySQLi {
	public $database;
	/**
	 * Database connect
	 * @param string $hostname MySQL host
	 * @param string $username MySQL username
	 * @param string $password MySQL password
	 * @param string $database MySQL database
	 * @throws \DatabaseException
	 */
	public function __construct($hostname, $username, $password, $database) {
		parent::__construct($hostname, $username, $password, $database);
		$this->database = $database;
		if ($this->connect_error) throw new \DatabaseException("[$this->connect_errno] $this->connect_error");
	}

	/**
	 * Execute SQL query with exception handling
	 * @param string $query SQL query
	 * @return MySQLi_result
	 * @throws \DatabaseException
	 */
	public function query($query) {
		$result = parent::query($query);
		if ($this->error) throw new \DatabaseException("[$this->errno] $this->error");
		return $result;
	}

	/**
	 * Build MySQL database tables from Doctrine2 entities
	 * @param \stdClass $settings
	 * @throws \DatabaseException
	 */
	public function buildFromEntities(\stdClass $settings) {
		$entitiesDir = __DIR__ . '/../../../../app/' . ($settings->moduleName ? "{$settings->moduleName}Module" : NULL) . '/models/Entities';
		foreach (\Bruha\Utils\File::getDirectoryFiles($entitiesDir) as $class) {
			if (mb_strpos($class, '~') === FALSE) $metadata[] = $settings->entityManager->getClassMetadata('\Kdyby\Doctrine\\' . str_replace('.php', '', $class));
		}
		try {
			(new \Doctrine\ORM\Tools\SchemaTool($settings->entityManager))->createSchema($metadata);
		} catch (\Doctrine\ORM\Tools\ToolsException $e) {
			$e = $e->getPrevious()->getPrevious()->getPrevious();
			throw new \DatabaseException($e->getMessage(), $e->errorInfo[1]);
		}
	}
}
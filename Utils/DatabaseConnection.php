<?php namespace Bruha\Utils;
/**
 * Database wrapper for MySQLi connection with basic exceptions handling
 * @author Radek Brůha
 * @version 1.0
 */
class DatabaseConnection extends \PDO {
	public $database;
	/**
	 * Database connect
	 * @param string $hostname Hostname
	 * @param string $username Username
	 * @param string $password Password
	 * @param string $database Database
	 * @throws \DatabaseException
	 */
	public function __construct($hostname, $username, $password, $database) {
		parent::__construct("mysql:dbname=$database;host=$hostname", $username, $password, NULL);
	//	$this->database = $database;
		
	//	if ($this->connect_error) throw new \DatabaseException("[$this->connect_errno] $this->connect_error");
	}

	public function query($query) {
		return parent::query($query);
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
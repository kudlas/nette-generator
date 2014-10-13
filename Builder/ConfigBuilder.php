<?php namespace Bruha\Builder;
/**
 * Nette Framework 2.2.X config building class
 * @author Radek Brůha
 * @version 1.0
 */
class ConfigBuilder extends BaseBuilder {
	/**
	 * Build and save Nette Framework 2.2.X config
	 * @param array of \Utils\Object\Table $tables
	 * @param \stdClass $settings
	 */
	public function build($tables, \stdClass $settings) {
		$config = \Nette\Neon\Neon::decode(\Bruha\Utils\File::read(__DIR__ . "/$this->projectPath/config/config.neon"));
		$services = $config['services'];
		foreach ($tables as $k => $v) {
			if ($v === "App\Models\{$v->sanitizedName}Repository") unset($services[$k]);
			$services[lcfirst($v->sanitizedName) . 'Repository'] = new \Nette\Neon\Entity('App' . ($settings->moduleName ? "\\{$settings->moduleName}Module" : NULL) . '\Models\\' . $v->sanitizedName . 'Repository', [$v->name]);
		}
		$config['services'] = $services;
		$config['nette']['database']['default']['reflection'] = $settings->source === \Utils\Constants::SOURCE_MYSQL_CONVENTIONAL ? 'conventional' : 'discovered';
		\Bruha\Utils\File::write(__DIR__ . "/$this->projectPath/config/config.neon", $config, TRUE, TRUE);
		\Bruha\Utils\File::write(__DIR__ . "/$this->projectPath/config/config.local.neon", '# This file is not used :)');	
	}
	
	/**
	 * Check Nette database configuration
	 * @param \SystemContainer $netteContainer Nette 2.2.X container
	 * @return \stdClass|boolean Database connection parameters
	 */
	public function checkDatabaseConfiguration($settings) {
		
		try {
			echo PHP_EOL . ' => Verifying Nette configuration:' . PHP_EOL . '    => Verifying database configuration:';
			new \PDO("mysql:host=$settings->hostname;dbname=$settings->database", $settings->username, $settings->password);
			echo ' SUCCESS' . PHP_EOL;
			return $settings;
		} catch (\Exception $e) {
			echo ' FAILURE' . PHP_EOL;
			switch ($e->getCode()) {
				case 2002:
					preg_match('~=(.*);~', $e->getTrace()[0]['args'][0], $hostname);
					echo "       => Database server '{$hostname[1]}' not found."; break;
				case 1044:
					preg_match("~'(.*)'~", $e->getMessage(), $info);
					$info = explode("'@'", $info[1]);
					echo "       => Access denied for user '{$info[0]}' to database '{$info[1]}'.";
					break;
				case 1045:
					preg_match("~'(.*)'~", $e->getMessage(), $info);
					$info = explode("'@'", $info[1]);
					echo "       => Access denied for user '{$info[0]}'@'{$info[1]}'.";
					break;
				case 1049:
					preg_match("~'(.*)'~", $e->getMessage(), $info);
					echo "       => Unknown database '{$info[1]}'.";
					break;
				default: echo '    => ' . $e->getMessage(); break;
			}
			do {
				echo PHP_EOL . '       => Insert right database configuration now:' . PHP_EOL . '          => Database hostname: ';
				$hostname = trim(fgets(STDIN));
				echo '          => Database username: ';
				$username = trim(fgets(STDIN));
				echo '          => Database password: ';
				$password = trim(fgets(STDIN));
				echo '          => Database name: ';
				$database = trim(fgets(STDIN));
			} while($this->verifyDatabaseConfiguration($hostname, $username, $password, $database));	
			$config = \Nette\Neon\Neon::decode(\Bruha\Utils\File::read(__DIR__ . '/../../../../app/config/config.neon'));
			$config['nette']['database']['default']['dsn'] = "mysql:host=$hostname;dbname=$database";
			$config['nette']['database']['default']['user'] = $username;
			$config['nette']['database']['default']['password'] = $password;
			$config['nette']['database']['default']['reflection'] = $this->settings->source === \Utils\Constants::SOURCE_MYSQL_CONVENTIONAL ? 'conventional' : 'discovered';
			\Bruha\Utils\File::write(__DIR__ . '/../../../../app/config/config.neon', $config, TRUE, TRUE);	
			return (object)['hostname' => $hostname, 'username' => $username, 'password' => $password, 'database' => $database];
		}
	}
	
	/**
	 * Verify new database connection
	 * @param string $hostname
	 * @param string $username
	 * @param string $password
	 * @param string $database
	 * @return boolean
	 */
	private function verifyDatabaseConfiguration($hostname, $username, $password, $database) {
		try {
			echo '    => Verifying database configuration:';
			new \PDO("mysql:host=$hostname;dbname=$database", $username, $password);
			echo ' SUCCESS' . PHP_EOL;
			return FALSE;
		} catch (\PDOException $e) {
			echo ' FAILURE' . PHP_EOL;
			switch ($e->getCode()) {
				case 2002:
					preg_match('~=(.*);~', $e->getTrace()[0]['args'][0], $hostname);
					echo "       => Database server '{$hostname[1]}' not found."; break;
				case 1044:
					preg_match("~'(.*)'~", $e->getMessage(), $info);
					$info = explode("'@'", $info[1]);
					echo "       => Access denied for user '{$info[0]}' to database '{$info[1]}'.";
					break;
				case 1045:
					preg_match("~'(.*)'~", $e->getMessage(), $info);
					$info = explode("'@'", $info[1]);
					echo "       => Access denied for user '{$info[0]}'@'{$info[1]}'.";
					break;
				case 1049:
					preg_match("~'(.*)'~", $e->getMessage(), $info);
					echo "       => Unknown database '{$info[1]}'.";
					break;
				default: echo '    => ' . $e->getMessage(); break;
			}
			return TRUE;
		}
	}
	
	/**
	 * Check Nette extensions configuration
	 * @param \stdClass $settings
	 */
	public function checkExtensionsConfiguration(\stdClass $settings) {
		echo '    => Verifying extensions configuration:' . PHP_EOL;
		$config = (new \Nette\Neon\Decoder())->decode(\Bruha\Utils\File::read(__DIR__ . "/$this->projectPath/config/config.neon"));
		echo '       => Kdyby\Translation: ';
		if (!isset($config['extensions']['translation'])) {
			$config['extensions']['translation'] = 'Kdyby\Translation\DI\TranslationExtension';
			echo 'MISSING' . PHP_EOL . '          => Installing: OK ' . PHP_EOL;
		} else echo 'SUCCESS' . PHP_EOL;
		echo '       => Kdyby\Annotations: ';
		if (!isset($config['extensions']['annotations'])) {
			$config['extensions']['annotations'] = 'Kdyby\Annotations\DI\AnnotationsExtension';
			echo 'MISSING' . PHP_EOL . '          => Installing: OK' . PHP_EOL;
		} else echo 'SUCCESS' . PHP_EOL;
		echo '       => Kdyby\Replicator: ';
		if (!isset($config['extensions']['replicator'])) {
			$config['extensions']['replicator'] = 'Kdyby\Replicator\DI\ReplicatorExtension';
			echo 'MISSING' . PHP_EOL . '          => Installing: OK' . PHP_EOL;
		} else echo 'SUCCESS' . PHP_EOL;
		echo '       => Kdyby\Console: ';
		if (!isset($config['extensions']['console'])) {
			$config['extensions']['console'] = 'Kdyby\Console\DI\ConsoleExtension';
			echo 'MISSING' . PHP_EOL . '          => Installing: OK' . PHP_EOL;
		} else echo 'SUCCESS' . PHP_EOL;
		echo '       => Kdyby\Events: ';
		if (!isset($config['extensions']['events'])) {
			$config['extensions']['events'] = 'Kdyby\Events\DI\EventsExtension';
			echo 'MISSING' . PHP_EOL . '          => Installing: OK' . PHP_EOL;
		} else echo 'SUCCESS' . PHP_EOL;
		if ($settings->target === \Utils\Constants::TARGET_DOCTRINE2) {
			echo '       => Kdyby\Doctrine: ';
			if (!isset($config['extensions']['doctrine'])) $config['extensions']['doctrine'] = 'Kdyby\Doctrine\DI\OrmExtension';
			if (!isset($config['doctrine'])) {
				$config['doctrine'] = ['host' => $settings->database->hostname,'user' => $settings->database->username, 'password' => $settings->database->password, 'dbname' => $settings->database->database, 'metadata' => ['App' => '%appDir%'], 'dql' => ['string' => ['CONCAT_WS' => 'DoctrineExtensions\Query\Mysql\ConcatWs']]];			
				echo 'MISSING' . PHP_EOL . '          => Installing: OK' . PHP_EOL;
			} else echo 'SUCCESS' . PHP_EOL;
		}
		\Bruha\Utils\File::write(__DIR__ . "/$this->projectPath/config/config.neon", $config, TRUE, TRUE);
	}
}
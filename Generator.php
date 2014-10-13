<?php namespace Bruha\Generator;
/**
 * Main application setup class
 * @author Radek Brůha
 * @version 1.0
 */
class Generator {
	private static $netteDirectory;
	private static $startTime;
	private static $settings;

	public function run() {
		//define('STDIN', fopen('php://stdin', 'r')); // For testing in browser
		static::$netteDirectory = __DIR__ . '/../../../app';
		$netteContainer = $this->getNetteContainer();
		//echo '<pre>'; // For testing in browser
		echo 'Welcome to the Nette Framework CRUD generator 1.0@beta3.';
		$configBuilder = new \Bruha\Builder\ConfigBuilder;
		static::$settings = (object)[
			'source' => \Utils\Constants::SOURCE_DOCTRINE2,
			'target' => \Utils\Constants::TARGET_DOCTRINE2,
			'foreignKeysChooser' => \Utils\Constants::FOREIGN_KEYS_TABLE,
			'tables' => [],
			'moduleName' => '',
			'templateName' => 'default',
			'entityManager' => NULL,
		];
		static::$settings->database = $this->getDatabaseConnectionParameters($netteContainer);
		static::$settings->database = $configBuilder->checkDatabaseConfiguration(static::$settings->database);
		var_dump(static::$settings->database);
		$configBuilder->checkExtensionsConfiguration(static::$settings);
		
		
		$this->showSourceDialog();
		$tables = $this->processSourceDialog($netteContainer);
		$this->showTablesDialog($tables);
		$this->showTargetDialog();
		$this->showForeignKeysDialog();
		$this->showModuleDialog();
		$this->chooseTemplatesDialog();
		static::$startTime = microtime(TRUE);
		$this->generate();
	}
	
	
	/** Show source choosing dialog */
	private function showSourceDialog() {
		echo PHP_EOL . ' => Choose source for CRUD building:' . PHP_EOL;
		echo '    => Press 1 for MySQL InnoDB tables (with foreign keys support).' . PHP_EOL;
		echo '    => Press 2 for MySQL MyISAM tables (without foreign keys support).' . PHP_EOL;
		echo '    => Press 3 for Doctrine2 entities.' . PHP_EOL;
		while ($source = (int)trim(fgets(STDIN))) if (in_array($source, [1, 2, 3], TRUE)) break;
		if (!empty($source)) static::$settings->source = $source;
	}

	/** Process source choosing dialog */
	private function processSourceDialog($netteContainer) {
		echo '    => Connecting to database: ';
		try {
		$database = new \Bruha\Utils\DatabaseConnection(static::$settings->database->hostname, static::$settings->database->username, static::$settings->database->password, static::$settings->database->database);
		} catch (\Exception $e) { echo $e->getMessage(); }
		if (static::$settings->source === \Utils\Constants::SOURCE_DOCTRINE2) {
			echo 'SUCCESS' . PHP_EOL . ' => Creating MySQL InnoDB engine tables: ';
			static::$settings->entityManager = $netteContainer->getByType('\Kdyby\Doctrine\EntityManager');
			$database->buildFromEntities(static::$settings);
		}
		echo 'SUCCESS' . PHP_EOL . '       => List of tables:' . PHP_EOL;
		return (new \Bruha\Examiner\MysqlExaminer($database, static::$settings))->getTables();
	}

	/** Show tables choosing dialog */
	private function showTablesDialog($tables) {
		foreach ($tables as $table) echo '          => ' . $table->name . PHP_EOL;
		echo PHP_EOL . ' => Choose tables for CRUD building:' . PHP_EOL;
		echo '    => Press Enter for all tables.' . PHP_EOL;
		echo '    => Write only few table names separated by comma.' . PHP_EOL;
		$choosenTables = trim(fgets(STDIN));
		$choosenTables = !empty($choosenTables) ? array_map('trim', explode(',', $choosenTables)) : $tables;
		echo PHP_EOL . '    => Verifing tables:' . PHP_EOL;
		foreach ($choosenTables as $k => $v) {
			$isInDatabase = FALSE;
			$isStateOK = 1;
			foreach ($tables as $t) {
				if (is_object($v) && $v->name === $t->name || is_string($v) && $v === $t->name) {
					$isInDatabase = TRUE;
					if ($t->state === \Utils\Constants::TABLE_STATE_OK) $isStateOK = $t->state;
				}
			}
			$tableName = is_object($v) ? $v->name : $v;
			if ($isInDatabase) {
				if ($isStateOK) {
					echo "       => $tableName: ERROR" . PHP_EOL . '          => Table doesn\'t have any primary key!' . PHP_EOL;
					unset($choosenTables[$k]);
				} else {
					echo "       => $tableName: DONE" . PHP_EOL;
					foreach ($tables as $t) if(is_object($v) && $v->name === $t->name || is_string($v) && $v === $t->name) static::$settings->tables[] = $t;
				}
			} else {
				echo "       => $tableName: ERROR" . PHP_EOL . '          => Table doesn\'t exists!' . PHP_EOL;
				unset($choosenTables[$k]);
			}
		}
	}

	/** Show target choosing dialog */
	private function showTargetDialog() {
		echo PHP_EOL . ' => Choose target for CRUD building' . PHP_EOL;
		echo '    => Press 1 for Nette\Database models.' . PHP_EOL;
		echo '    => Press 2 for Doctrine2 models.' . PHP_EOL;
		while ($target = (int)trim(fgets(STDIN))) if (in_array($target, [1, 2], TRUE)) break;
		if (!empty($target)) static::$settings->target = $target;
	}
	
	/** Show foreign keys choosing dialog */
	private function showForeignKeysDialog() {
		echo PHP_EOL . ' => Choose foreign keys chooser for CRUD building' . PHP_EOL;
		echo '    => Press 1 for open full table in new window' . PHP_EOL;
		echo '    => Press 2 for selectbox with search' . PHP_EOL;
		while ($chooser = (int)trim(fgets(STDIN))) if (in_array($chooser, [1, 2], TRUE)) break;
		if (!empty($chooser)) static::$settings->foreignKeysChooser = $chooser;
	}

	private function generate() {
		try {
			echo PHP_EOL . ' => Processing tables: ';
			if (static::$settings->target === \Utils\Constants::TARGET_DOCTRINE2 && static::$settings->source !== \Utils\Constants::SOURCE_DOCTRINE2) {
				echo PHP_EOL . '    => Building Doctrine2 entites from database: ';
				(new \Bruha\Builder\EntityBuilder)->build(static::$settings);
				echo 'DONE' . PHP_EOL;
			}
			
			$changeFormTableBuilder = new \Bruha\Builder\ChangeTableFormBuilder;
			$changeFormSelectBuilder = new \Bruha\Builder\ChangeSelectFormBuilder;
			$modelBuilder = new \Bruha\Builder\ModelBuilder;
			$presenterBuilder = new \Bruha\Builder\Presenter;
			$templateBuilder = new \Bruha\Builder\Template;
			$changeFormTemplateBuilder = new \Bruha\Builder\ChangeTemplate;
			$basePresenterBuilder = new \Bruha\Builder\BasePresenter;
			$baseModelBuilder = new \Bruha\Builder\BaseModel;
			$layoutBuilder = new \Bruha\Builder\Layout;
			$routerBuilder = new \Bruha\Builder\Router;
			$configBuilder = new \Bruha\Builder\ConfigBuilder;
			$translateBuilder = new \Bruha\Builder\Translate;
	
			foreach (static::$settings->tables as $k => $table) {
				echo PHP_EOL . "    => Table $table->name: ";
				$presenterBuilder->build($table, static::$settings, $changeFormTableBuilder->build($table, static::$settings), $changeFormSelectBuilder->build($table, static::$settings)); 
				(new \Bruha\Builder\ControlForm)->build($table->columns, static::$settings);
				$modelBuilder->build($table, static::$settings);
				$templateBuilder->build($table, static::$settings);
				$changeFormTemplateBuilder->build($table, static::$settings);
				echo 'DONE';
			}
			echo PHP_EOL . PHP_EOL . 'Procesing final configuration: ' . PHP_EOL . ' => Building common presenter: ';
			$basePresenterBuilder->build(static::$settings);
			echo 'DONE' . PHP_EOL . ' => Building common model: ';
			$baseModelBuilder->build(static::$settings);
			echo 'DONE' . PHP_EOL . ' => Building common template: ';
			$layoutBuilder->build(static::$settings->tables, static::$settings);
			echo 'DONE' . PHP_EOL . ' => Building router: ';
			$routerBuilder->build(static::$settings->tables, static::$settings);
			echo 'DONE' . PHP_EOL . ' => Building config: ';
			$configBuilder->build(static::$settings->tables, static::$settings);
			$translateBuilder->build(static::$settings->tables);
			echo 'DONE' . PHP_EOL . ' => Building images: ';
			\Bruha\Utils\File::directoryCopy(__DIR__ . '/Templates/' . static::$settings->templateName . '/images', static::$netteDirectory . '/../www/images');
			echo PHP_EOL . ' => Building Cascading Style Sheet: ';
			\Bruha\Utils\File::directoryCopy(__DIR__ . '/Templates/' . static::$settings->templateName . '/css', static::$netteDirectory . '/../www/css');
			echo PHP_EOL . ' => Building Javascript: ';
			\Bruha\Utils\File::directoryCopy(__DIR__ . '/Templates/' . static::$settings->templateName . '/js', static::$netteDirectory . '/../www/js');
			echo PHP_EOL . ' => Cleaning Nette Cache: ';
			\Bruha\Utils\File::removeDirectory(static::$netteDirectory . '/../temp/cache');
			echo 'DONE' . PHP_EOL;
			echo PHP_EOL . 'Application successfully built in ' . number_format(microtime(TRUE) - static::$startTime, 2, '.', ' ') . ' seconds.' . PHP_EOL;
		} catch (\Doctrine\ORM\Mapping\MappingException $e) {
			echo 'ERRROR' . PHP_EOL . '      => ' . $e->getMessage();	
		} catch (\Exception $e) {
			echo 'ERROR' . PHP_EOL . $e->getMessage();
		}
	}
	
	/**
	 * Load Nette 2.2.X, Doctrine2, project classes and return its container
	 * @return \SystemContainer Nette 2.2.X container
	 */
	private function getNetteContainer() {
		$netteContainer = require static::$netteDirectory . '/bootstrap.php';
		$loader = new \Nette\Loaders\RobotLoader;
		$loader->setCacheStorage(new \Nette\Caching\Storages\DevNullStorage());
		$loader->addDirectory(__DIR__);
		$loader->addDirectory(__DIR__ . '/../../doctrine/annotations/lib');
		$loader->addDirectory(__DIR__ . '/../../doctrine/cache/lib');
		$loader->addDirectory(__DIR__ . '/../../doctrine/common/lib');
		$loader->addDirectory(__DIR__ . '/../../doctrine/dbal/lib');
		$loader->addDirectory(__DIR__ . '/../../doctrine/inflector/lib');
		$loader->addDirectory(__DIR__ . '/../../doctrine/lexer/lib');
		$loader->addDirectory(__DIR__ . '/../../doctrine/orm/lib');
		$loader->register();
		return $netteContainer;
	}

	/**
	 * Return Nette 2.2.X database connection prameters
	 * @param \SystemContainer $netteContainer
	 * @return \stdClass Database connection parameters
	 */
	private function getDatabaseConnectionParameters($netteContainer) {
		$databaseConnection = $netteContainer->getByType('\Nette\Database\Connection');
		$databaseReflectionProperty = (new \ReflectionClass('\Nette\Database\Connection'))->getProperty('params');
		$databaseReflectionProperty->setAccessible(TRUE);
		$databaseConncetionParameters = $databaseReflectionProperty->getValue($databaseConnection);
		preg_match('~=(.*);~', $databaseConncetionParameters[0], $hostname);
		return (object)['hostname' => $hostname[1], 'username' => $databaseConncetionParameters[1], 'password' => $databaseConncetionParameters[2], 'database' => mb_substr($databaseConncetionParameters[0], mb_strpos($databaseConncetionParameters[0], ';dbname=') + 8)];
	}

	/** Show module choosing dialog */
	private function showModuleDialog() {
		echo PHP_EOL . ' => Do you want to build into module?' . PHP_EOL;
		echo '    => Yes? Write his name.' . PHP_EOL;
		echo '    => No? Press enter.' . PHP_EOL;
		$moduleName = trim(fgets(STDIN));
		if (!empty($moduleName)) static::$settings->moduleName = $moduleName;
	}

	/** Show template choosing dialog */
	private function chooseTemplatesDialog() {
		echo PHP_EOL . ' => What templates do you want to use?' . PHP_EOL . '    => Write their name or press enter for default ones.' . PHP_EOL;
		while ($template = trim(fgets(STDIN))) {
			if (is_dir(__DIR__ . '/Templates/' . $template)) break;
			echo " => ERROR: There is no directory with '$template' templates." . PHP_EOL . PHP_EOL;
			echo ' => What templates do you want to use?' . PHP_EOL . '    => Write their name or press enter for default ones.' . PHP_EOL;
		}
		if (!empty($template)) static::$settings->templateName = $template;
	}
}
<?php namespace Bruha\Generator;
/**
 * Main application setup class
 * @author Radeb Brůha
 * @version 1.0
 */
class Application {
	private static $netteDirectory;
	private static $startTime;
	private static $settings = [
		'from' => 1,
		'tables' => FALSE,
		'moduleName' => 'Test',
		'what' => 2,
		'templateName' => 'default'
	];

	public function run() {
		#echo '<pre>'; // For testing in browser
		#define('STDIN', fopen('php://stdin', 'r')); // For testing in browser
		static::$netteDirectory = __DIR__ . '/../../../app';
		static::$settings = (object)static::$settings;
		$netteContainer = $this->getNetteFrameworkContainer();
		static::$settings->database = $this->getDatabaseConnectionParameters($netteContainer);
		static::$settings->entityManager = $netteContainer->getByType('Kdyby\Doctrine\Entitymanager');
		$this->showSourceDialog();
		$this->showTablesDialog();
		$this->showDestinationDialog();
		$this->showModuleDialog();
		$this->chooseTemplatesDialog();
		static::$startTime = microtime(TRUE);
		$this->generate();
	}

	private function generate() {
		try {
			echo PHP_EOL . 'Processing initial configuration: ' . PHP_EOL . ' => Connecting to database: ';
			$database = new \Bruha\Utils\Database(static::$settings->database->hostname, static::$settings->database->username, static::$settings->database->password, static::$settings->database->database);

			if (static::$settings->from === 2) {
				echo 'DONE' . PHP_EOL . ' => Creating MySQL InnoDB engine tables: ';
				$database->buildFromEntities(static::$settings);
			}

			if (static::$settings->what === 2) {
				(new \Bruha\Builder\Entity)->build(static::$settings);
			} else echo 'DONE' . PHP_EOL . ' => Analysing database tables: ';
			$tables = (new \Bruha\Examiner\Database($database))->getTables();
			echo 'DONE' . PHP_EOL . PHP_EOL . 'Processing tables: ';

			$changeFormBuilder = new \Bruha\Builder\ChangeForm;
			$presenterBuilder = new \Bruha\Builder\Presenter;
			$templateBuilder = new \Bruha\Builder\Template;
			$changeFormTemplateBuilder = new \Bruha\Builder\ChangeTemplate;
			$basePresenterBuilder = new \Bruha\Builder\BasePresenter;
			$baseModelBuilder = new \Bruha\Builder\BaseModel;
			$layoutBuilder = new \Bruha\Builder\Layout;
			$routerBuilder = new \Bruha\Builder\Router;
			$configBuilder = new \Bruha\Builder\Config;
			$translateBuilder = new \Bruha\Builder\Translate;

			foreach ($tables as $table) {
				if (static::$settings->tables) if (!in_array($table->name, static::$settings->tables, TRUE)) continue;
				$generatedTables[] = $table;
				echo PHP_EOL . " => Table $table->name: ";
				$presenterBuilder->build($table, static::$settings, $changeFormBuilder->build($table, static::$settings), (new \Bruha\Builder\ControlForm())->build($table->columns, static::$settings));
				$templateBuilder->build($table, static::$settings);
				$changeFormTemplateBuilder->build($table, static::$settings);
				echo 'DONE';
			}
			if (!$generatedTables) exit('ERROR' . PHP_EOL . ' => There are no tables with given names in database!');

			echo PHP_EOL . PHP_EOL . 'Procesing final configuration: ' . PHP_EOL . ' => Building common presenter: ';
			$basePresenterBuilder->build(static::$settings);
			echo 'DONE' . PHP_EOL . ' => Building common model: ';
			$baseModelBuilder->build(static::$settings);
			echo 'DONE' . PHP_EOL . ' => Building common template: ';
			$layoutBuilder->build($generatedTables, static::$settings);
			echo 'DONE' . PHP_EOL . ' => Building router: ';
			$routerBuilder->build($generatedTables, static::$settings);
			echo 'DONE' . PHP_EOL . ' => Building config: ';
			$configBuilder->build(static::$settings);
			$translateBuilder->build($generatedTables);
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
		} catch (\Exception $e) {
			echo 'ERROR' . PHP_EOL . $e->getMessage();
		}
	}

	/**
	 * Load Nette Framework 2.2.X, Doctrine2, project classes and return its container
	 * @return \SystemContainer Nette Framework 2.2.X container
	 */
	private function getNetteFrameworkContainer() {
		$netteContainer = require_once static::$netteDirectory . '/bootstrap.php';
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
	 * Return Nette Framework 2.2.X database connection prameters
	 * @param \SystemContainer $netteContainer
	 * @return \stdClass Database connection parameters
	 */
	private function getDatabaseConnectionParameters($netteContainer) {
		$databaseConnection = $netteContainer->getByType('\Nette\Database\Connection');
		$databaseReflectionProperty = (new \ReflectionClass($databaseConnection))->getProperty('params');
		$databaseReflectionProperty->setAccessible(TRUE);
		$databaseConncetionParameters = $databaseReflectionProperty->getValue($databaseConnection);
		preg_match('~=(.*);~', $databaseConncetionParameters[0], $hostname);
		return (object)[
				'hostname' => $hostname[1],
				'username' => $databaseConncetionParameters[1],
				'password' => $databaseConncetionParameters[2],
				'database' => substr($databaseConncetionParameters[0], mb_strpos($databaseConncetionParameters[0], ';dbname=') + 8)];
	}

	/** Show source choosing dialog */
	private function showSourceDialog() {
		echo 'Welcome to Nette Framework 2.2.X CRUD generator.' . PHP_EOL . 'What do you want to use for CRUD building?' . PHP_EOL;
		echo ' => Press 1 for MySQL tables.' . PHP_EOL;
		echo ' => Press 2 for Doctrine2 entities.' . PHP_EOL;
		while ($from = (int)trim(fgets(STDIN))) if (in_array($from, [1, 2], TRUE)) break;
		if (!empty($from)) static::$settings->from = $from;
	}

	/** Show tables choosing dialog */
	private function showTablesDialog() {
		echo 'Do you want to build only few tables?' . PHP_EOL;
		echo ' => Yes? Write their names and use , as separator.' . PHP_EOL;
		echo ' => No? Press enter.' . PHP_EOL;
		$tables = trim(fgets(STDIN));
		if (!empty($tables)) static::$settings->tables = array_map('trim', explode(',', $tables));
	}

	/** Show destination choosing dialog */
	private function showDestinationDialog() {
		echo 'What models do you want to build?' . PHP_EOL;
		echo ' => Press 1 for \Nette\Database models.' . PHP_EOL;
		echo ' => Press 2 for Doctrine2 models.' . PHP_EOL;
		while ($what = (int)trim(fgets(STDIN))) if (in_array($what, [1, 2], TRUE)) break;
		if (!empty($what)) static::$settings->what = $what;
	}

	/** Show module choosing dialog */
	private function showModuleDialog() {
		echo 'Do you want to build into module?' . PHP_EOL;
		echo ' => Yes? Write his name.' . PHP_EOL;
		echo ' => No? Press enter.' . PHP_EOL;
		$moduleName = trim(fgets(STDIN));
		if (!empty($moduleName)) static::$settings->moduleName = $moduleName;
	}

	/** Show template choosing dialog */
	private function chooseTemplatesDialog() {
		echo 'What templates do you want to use?' . PHP_EOL . ' => Write their name or press enter for default ones.' . PHP_EOL;
		while ($template = trim(fgets(STDIN))) {
			if (is_dir(__DIR__ . '/Templates/' . $template)) break;
			echo " => ERROR: There is no directory with '$template' templates." . PHP_EOL . PHP_EOL;
			echo 'What templates do you want to use?' . PHP_EOL . ' => Write their name or press enter for default ones.' . PHP_EOL;
		}
		if (!empty($template)) static::$settings->templateName = $template;
	}
}
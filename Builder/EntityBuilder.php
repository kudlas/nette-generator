<?php namespace Bruha\Builder;
/**
 * Doctrine2 entity building class
 * @author Radek Brůha
 * @version 1.0
 */
class EntityBuilder extends BaseBuilder {
	/**
	 * Build and save Doctrine2 entities from database
	 * @param \stdClass $settings
	 */
	public function build(\stdClass $settings) {
		$database = ['dbname' => $settings->database->database, 'user' => $settings->database->username, 'password' => $settings->database->password, 'host' => $settings->database->hostname, 'driver' => 'pdo_mysql'];	
		$directory = $settings->moduleName ? __DIR__ . "/$this->projectPath/{$settings->moduleName}Module/models/Entities" : __DIR__ . "/$this->projectPath/models/Entities";
		$metadata = [];

		$config = new \Doctrine\ORM\Configuration;
		$config->setMetadataDriverImpl($config->newDefaultAnnotationDriver(__DIR__ . '/Utils/Entity'));
				
		$config->setMetadataCacheImpl(new \Doctrine\Common\Cache\ArrayCache);
		$config->setProxyDir(__DIR__ . '/Utils/Proxy');
		$config->setProxyNamespace('Proxy');

		$entityGenerator = new \Doctrine\ORM\Tools\EntityGenerator();
		$entityGenerator->setClassToExtend('\Nette\Object');
		$entityGenerator->setAnnotationPrefix('ORM\\');
		$entityGenerator->setUpdateEntityIfExists(TRUE);
		$entityGenerator->setRegenerateEntityIfExists(TRUE);
		$entityGenerator->setGenerateStubMethods(TRUE);
		$entityGenerator->setGenerateAnnotations(TRUE);
		
		$entityManager = \Doctrine\ORM\EntityManager::create($database, $config);
		$entityManager->getConnection()->getDatabasePlatform()->registerDoctrineTypeMapping('set', 'string');
		$entityManager->getConnection()->getDatabasePlatform()->registerDoctrineTypeMapping('enum', 'string');
		$driver = new \Doctrine\ORM\Mapping\Driver\DatabaseDriver($entityManager->getConnection()->getSchemaManager());
		$entityManager->getConfiguration()->setMetadataDriverImpl($driver);
		$metadataFactory = new \Doctrine\ORM\Tools\DisconnectedClassMetadataFactory();
		$metadataFactory->setEntityManager($entityManager);
		foreach ($driver->getAllClassNames() as $class) $metadata[] = $metadataFactory->getMetadataFor($class);
		$entityGenerator->generate($metadata, $directory);
		
		foreach (\Bruha\Utils\File::getDirectoryFiles($directory) as $file) {
			$content = str_replace('<?php', '<?php namespace Kdyby\Doctrine;', \Bruha\Utils\File::read("$directory/$file"));
			preg_match_all('~private \$(.*);~', $content, $matches);
			foreach ($matches[1] as $key => $value) if(($posisiton = mb_strpos($value, ' ')) !== FALSE) $matches[1][$key] = mb_substr($value, 0, $posisiton);
			$oldVariables = $newVariables = $matches[1];
			foreach ($oldVariables as $key => $value) {
				$newVariables[$key] = $newValue = implode('_', array_map('lcfirst', preg_split('~(?=[A-Z])~', $value)));
				$oldGetters[$key] = 'get' . ucfirst($value);
				$newGetters[$key] = 'get' . ucfirst($newValue);
				$oldSetters[$key] = 'set' . ucfirst($value);
				$newSetters[$key] = 'set' . ucfirst($newValue);
				$content = str_replace($oldSetters[$key] . '(\\', $oldSetters[$key] . '(\Kdyby\Doctrine\\', $content);
			}
			\Bruha\Utils\File::write("$directory/$file", preg_replace('~(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+~', "\n", str_replace($oldSetters, $newSetters, str_replace($oldGetters, $newGetters, str_replace($oldVariables, $newVariables, $content)))));
		}
	}
}
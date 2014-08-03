<?php namespace Bruha\Builder;
/**
 * Router building class
 * @author Radek Brůha
 * @version 1.0
 */
class Router extends Base {
	/**
	 * Build and save router
	 * @param array of \Utils\Object\Table $tables
	 * @param \stdClass $settings
	 */
	public function build(array $tables, \stdClass $settings) {
		$this->sourcePath = "/../Templates/$settings->templateName/router/router.latte";
		$this->destinationPath = __DIR__ . "/$this->projectPath/router/RouterFactory.php";
		$this->params['routerName'] = $tables[0]->sanitizedName;
		$this->params['moduleName'] = $settings->moduleName;
		$this->saveTemplate();
	}
}
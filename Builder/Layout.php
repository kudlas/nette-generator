<?php namespace Bruha\Builder;
/**
 * Layout building class
 * @author Radek Brůha
 * @version 1.0
 */
class Layout extends Base {
	/**
	 * Build and save layout template
	 * @param array of \Utils\Object\Table $tables
	 * @param \stdClass $settings
	 */
	public function build(array $tables, \stdClass $settings) {
		$this->sourcePath = "/../Templates/$settings->templateName/template/layout.latte";
		$this->destinationPath = __DIR__ . "/$this->projectPath/templates/@layout.latte";
		$this->params['tables'] = $tables;
		$this->saveTemplate();
	}
}
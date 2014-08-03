<?php namespace Bruha\Builder;
/**
 * Form template building class
 * @author Radek Brůha
 * @version 1.0
 */
class ChangeTemplate extends Base {
	/**
	 * Build and save form template
	 * @param \Utils\Object\Table $table
	 * @param \stdClass $settings
	 */
	public function build(\Bruha\Utils\Object\Table $table, \stdClass $settings) {
		$this->sourcePath = "/../Templates/$settings->templateName/template/change.latte";
		$this->destinationPath = $settings->moduleName ? __DIR__ . "/$this->projectPath/{$settings->moduleName}Module/templates/{$table->sanitizedName}/change.latte" : __DIR__ . "/$this->projectPath/templates/{$table->sanitizedName}/change.latte";
		$this->saveTemplate();
	}
}
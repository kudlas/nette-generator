<?php namespace Bruha\Builder;
/**
 * Template building class
 * @author Radek Brůha
 * @version 1.0
 */
class Template extends BaseBuilder {
	/**
	 * Build and save template
	 * @param \Utils\Object\Table $table
	 * @param \stdClass $settings
	 */
	public function build(\Bruha\Utils\Object\Table $table, \stdClass $settings) {
		$this->sourcePath = "/../Templates/$settings->templateName/template/list.latte";
		$this->destinationPath = $settings->moduleName ? __DIR__ . "/$this->projectPath/{$settings->moduleName}Module/templates/{$table->sanitizedName}/list.latte" : __DIR__ . "/$this->projectPath/templates/{$table->sanitizedName}/list.latte";
		foreach ($table->columns as $column) foreach ($column->keys as $key) if ($key instanceof \Bruha\Utils\Object\Key\PrimaryKey) $this->params['primaryKey'] = $column->name;		
		$this->params['table'] = $table;
		$this->saveTemplate();
	}
}
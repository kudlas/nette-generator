<?php namespace Bruha\Builder;
/**
 * Model building class
 * @author Radek Brůha
 * @version 1.0
 */
class ModelBuilder extends BaseBuilder {
	/**
	 * Build and save presenter
	 * @param \Utils\Object\Table $table
	 * @param \stdClass $settings
	 * @param string $form
	 */
	public function build(\Bruha\Utils\Object\Table $table, \stdClass $settings) {
		$this->sourcePath = $settings->target === \Utils\Constants::TARGET_NETTE_DATABASE ? "/../Templates/$settings->templateName/model/nette-database/model.latte" : "/../Templates/$settings->templateName/model/doctrine2/model.latte";
		$this->destinationPath = $settings->moduleName ? __DIR__ . "$this->projectPath/{$settings->moduleName}Module/models/{$table->sanitizedName}Repository.php" : __DIR__ . "$this->projectPath/models/{$table->sanitizedName}Repository.php";
		$this->params['moduleName'] = $settings->moduleName ? "\\{$settings->moduleName}Module" : NULL;
		$this->params['modelName'] = $table->sanitizedName;
		$this->saveTemplate();
	}
}
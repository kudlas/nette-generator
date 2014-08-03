<?php namespace Bruha\Builder;
/**
 * Presenter building class
 * @author Radek Brůha
 * @version 1.0
 */
class Presenter extends Base {
	/**
	 * Build and save presenter
	 * @param \Utils\Object\Table $table
	 * @param \stdClass $settings
	 * @param string $form
	 */
	public function build(\Bruha\Utils\Object\Table $table, \stdClass $settings, $editForm, $searchForm) {
		$this->sourcePath = "/../Templates/$settings->templateName/presenter/presenter.latte";
		$this->destinationPath = $settings->moduleName ? __DIR__ . "/$this->projectPath/{$settings->moduleName}Module/presenters/{$table->sanitizedName}Presenter.php" : __DIR__ . "/$this->projectPath/presenters/{$table->sanitizedName}Presenter.php";
		$this->params['presenterName'] = $table->sanitizedName;
		$this->params['moduleName'] = $settings->moduleName ? "\\{$settings->moduleName}Module" : NULL;
		$this->params['changeForm'] = $editForm;
		$this->params['controlForm'] = $searchForm;
		$this->saveTemplate();
	}
}
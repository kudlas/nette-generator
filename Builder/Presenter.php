<?php namespace Bruha\Builder;
/**
 * Presenter building class
 * @author Radek Brůha
 * @version 1.0
 */
class Presenter extends BaseBuilder {
	/**
	 * Build and save presenter
	 * @param \Utils\Object\Table $table
	 * @param \stdClass $settings
	 * @param string $changeForm
	 */
	public function build(\Bruha\Utils\Object\Table $table, \stdClass $settings, $changeTableForm, $changeSelectForm) {
		$fTables = [];
		$this->sourcePath = "/../Templates/$settings->templateName/presenter/presenter.latte";
		$this->destinationPath = $settings->moduleName ? __DIR__ . "/$this->projectPath/{$settings->moduleName}Module/presenters/{$table->sanitizedName}Presenter.php" : __DIR__ . "/$this->projectPath/presenters/{$table->sanitizedName}Presenter.php";
		$this->params['presenterName'] = $table->sanitizedName;
		$this->params['moduleName'] = $settings->moduleName ? "\\{$settings->moduleName}Module" : NULL;
		$this->params['changeTableForm'] = $changeTableForm;
		$this->params['changeSelectForm'] = $changeSelectForm;
		$this->params['columns'] = '[';
		foreach ($table->columns as $column) {
			 $this->params['columns'] .= "'$table->name.$column->name' => '" . ($column->comment ?: $column->name) . "', ";
			 foreach ($column->keys as $k) if ($k instanceof \Bruha\Utils\Object\Key\ForeignKey) $fTables[$k->table->name] = $k->table;
		}
		$this->params['tables'] = $fTables;
		$this->params['columns'] = str_replace(', ]', ']', $this->params['columns'] .= ']');
		$this->saveTemplate();
	}
}
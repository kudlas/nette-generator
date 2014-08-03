<?php namespace Bruha\Builder;
/**
 * Search form building class
 * @author Radek Brůha
 * @version 1.0
 */
class ControlForm extends Base {
	/**
	 * Build and save search form
	 * @param array of \Utils\Object\Column $columns
	 * @param \stdClass $settings
	 * @return string Search form
	 */
	public function build(array $columns, \stdClass $settings) {
		$this->sourcePath = "/../Templates/$settings->templateName/form/control.latte";
		$this->params['columns'] = '[';
		foreach ($columns as $column) {
			if ($column->key instanceof \Bruha\Utils\Object\Key\Foreign) {
				$this->params['columns'] .= "'{$column->key->table}.{$column->key->value}' => '" . ($column->comment ?: $column->name) . "', ";
			} else $this->params['columns'] .= "'$column->name' => '" . ($column->comment ?: $column->name) . "', ";
		}
		$this->params['columns'] = str_replace(', ]', ']', $this->params['columns'] .= ']');
		return $this->buildTemplate();
	}
}
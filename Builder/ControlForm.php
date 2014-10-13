<?php namespace Bruha\Builder;
/**
 * Search form building class
 * @author Radek Brůha
 * @version 1.0
 */
class ControlForm extends BaseBuilder {
	/**
	 * Build and save search form
	 * @param array of \Utils\Object\Column $columns
	 * @param \stdClass $settings
	 * @return string Search form
	 */
	public function build(array $columns, \stdClass $settings) {
		$this->sourcePath = "/../Templates/$settings->templateName/form/control.latte";
		$this->params['columns'] = '[';
		foreach ($columns as $c) {
			foreach ($c->keys as $k) {
				if ($k instanceof \Bruha\Utils\Object\Key\ForeignKey) {
					$this->params['columns'] .= "'{$k->table->name}.{$k->value}' => '" . ($c->comment ?: $c->name) . "', ";
				} else $this->params['columns'] .= "'$c->name' => '" . ($c->comment ?: $c->name) . "', ";
			}
		}
		$this->params['columns'] = str_replace(', ]', ']', $this->params['columns'] .= ']');
		return $this->buildTemplate();
	}
}
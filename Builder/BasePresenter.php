<?php namespace Bruha\Builder;
/**
 * Base presenter building class
 * @author Radek Brůha
 * @version 1.0
 */
class BasePresenter extends Base {
	/**
	 * Build and save base presenter
	 * @param \stdClass $settings
	 */
	public function build(\stdClass $settings) {
		$this->sourcePath = "/../Templates/$settings->templateName/presenter/base.latte";
		$this->destinationPath = $settings->moduleName ? __DIR__ . "/$this->projectPath/{$settings->moduleName}Module/presenters/BasePresenter.php" : __DIR__ . "/$this->projectPath/presenters/BasePresenter.php";
		$this->params['moduleName'] = $settings->moduleName ? "\\{$settings->moduleName}Module" : NULL;
		$this->saveTemplate();
	}
}
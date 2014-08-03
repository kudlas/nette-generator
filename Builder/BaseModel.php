<?php namespace Bruha\Builder;
/**
 * Base model building class
 * @author Radek Brůha
 * @version 1.0
 */
class BaseModel extends Base {
	/**
	 * Build and save base model
	 * @param \stdClass $settings
	 */
	public function build(\stdClass $settings) {
		$this->sourcePath = $settings->what === 1 ? "/../Templates/$settings->templateName/model/nette-database/base.latte" : "/../Templates/$settings->templateName/model/doctrine2/base.latte";
		$this->destinationPath = $settings->moduleName ? __DIR__ . "$this->projectPath/{$settings->moduleName}Module/models/BaseRepository.php" : __DIR__ . "$this->projectPath/models/BaseRepository.php";
		$this->params['moduleName'] = $settings->moduleName ? "\\{$settings->moduleName}Module" : NULL;
		$this->saveTemplate();
		
		$this->sourcePath = $settings->what === 1 ? "/../Templates/$settings->templateName/Model/nette-database/custom.latte" : "/../Templates/$settings->templateName/model/doctrine2/custom.latte";
		$this->destinationPath = $settings->moduleName ? __DIR__ . "$this->projectPath/{$settings->moduleName}Module/models/CustomRepository.php" : __DIR__ . "$this->projectPath/models/CustomRepository.php";
		$this->params['moduleName'] = $settings->moduleName ? "\\{$settings->moduleName}Module" : NULL;
		$this->saveTemplate(FALSE);
	}
}
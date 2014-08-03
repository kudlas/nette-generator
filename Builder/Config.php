<?php namespace Bruha\Builder;
/**
 * Nette Framework 2.2.X config building class
 * @author Radek Brůha
 * @version 1.0
 */
class Config extends Base {
	/**
	 * Build and save Nette Framework 2.2.X config
	 * @param array of \Utils\Object\Table $tables
	 * @param \stdClass $settings
	 */
	public function build(\stdClass $settings) {
		$config = (new \Nette\Neon\Decoder())->decode(\Bruha\Utils\File::read(__DIR__ . "/$this->projectPath/config/config.neon"));
		$services = $config['services'];
		foreach ($services as $key => $value) if ($value === 'App\Models\CustomRepository') unset($services[$key]);
		$services['CustomRepository'] = new \Nette\Neon\Entity('App' . ($settings->moduleName ? "\\{$settings->moduleName}Module" : NULL) . '\Models\CustomRepository');
		$config['services'] = $services;
		\Bruha\Utils\File::write(__DIR__ . "/$this->projectPath/config/config.neon", str_replace('()', '', (new \Nette\Neon\Encoder)->encode($config, \Nette\Neon\Encoder::BLOCK)));
	}
}
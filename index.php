<?php require __DIR__ . '\Generator.php';
if (!function_exists('dump')) {
	/**
	 * Tracy\Debugger::dump() shortcut.
	 * @tracySkipLocation
	 */
	function dump($var)
	{
		foreach (func_get_args() as $arg) {
			Tracy\Debugger::dump($arg);
		}
		return $var;
	}
}

if (!function_exists('barDump')) {
	/**
	 * Tracy\Debugger::barDump() shortcut.
	 * @tracySkipLocation
	 */
	function barDump($var, $title = NULL, array $options = NULL) {
		Tracy\Debugger::barDump($var, $title, $options);
	}
}

if (!function_exists('ajaxDump')) {
	/**
	 * \Tracy\FireLogger::log shortcut.
	 * @tracySkipLocation
	 */
	function ajaxDump($var) {
		\Tracy\FireLogger::log($var);
	}
}

(new \Bruha\Generator\Generator)->run();
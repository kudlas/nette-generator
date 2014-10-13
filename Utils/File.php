<?php namespace Bruha\Utils;
/**
 * File operation class
 * @author Radek Brůha
 * @version 1.0
 */
class File {

	/**
	 * Read content of file
	 * @param string $source File source
	 * @return string File content
	 * @throws \FileException
	 * @static
	 */
	public static function read($source) {
		if (is_file($source)) {
			if (($content = file_get_contents($source)) === FALSE) throw new \FileException("Cannot read file $source.");
			return $content;
		} else throw new \FileException("Cannot read file $source.");
	}

	/**
	 * Write content into file
	 * @param string $destination
	 * @param string $content
	 * @throws \FileException
	 * @static
	 */
	public static function write($destination, $content, $rewrite = TRUE, $neon = FALSE) {
		if (!$rewrite) return;
		if (!is_dir(dirname($destination))) if (!mkdir(dirname($destination), 0777, TRUE)) throw new \FileException("Cannot create path $destination.");
		if (!file_put_contents($destination, $neon ? str_replace('()', '', \Nette\Neon\Neon::encode($content, \Nette\Neon\Encoder::BLOCK)) : $content)) throw new \FileException("Cannot write file $destination.");
	}
	
	/**
	 * Copy all files and directories within directory to another directory
	 * @param string $source Source directory path
	 * @param string $destination Destinaion directory path
	 * @throws \FileException
	 * @static
	 */
	public static function directoryCopy($source, $destination) {
		if (!is_dir($destination)) if (!mkdir($destination, 0777, TRUE)) throw new \FileException("    => Cannot create directory $destination.");
		foreach ($iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($source, \RecursiveDirectoryIterator::SKIP_DOTS), \RecursiveIteratorIterator::SELF_FIRST) as $item) {
			if ($item->isDir()) {
				$path = $destination . DIRECTORY_SEPARATOR . $iterator->getSubPathName();
				if (!mkdir($path)) throw new \FileException("    => Cannot create directory $path.");
				echo PHP_EOL . '    => ' . realpath($path);
			} else {
				$path = $destination . DIRECTORY_SEPARATOR . $iterator->getSubPathName();
				if (!copy($item, $path)) throw new \FileException("    => Cannot create file $path.");
				echo PHP_EOL . '    => ' . realpath($path);
			}
		}
	}

	/**
	 * Get all files within directory
	 * @param string $source Source directory path
	 * @return array
	 * @static
	 */
	public static function getDirectoryFiles($source) {
		$files = [];
		foreach (new \DirectoryIterator($source) as $file) if ($file->isFile()) $files[] = $file->getFilename();
		return $files;
	}

	/**
	 * Clean Nette Framework 2.2.X cache directories and files
	 * @param string $path Cache location
	 * @throws \FileException
	 * @static
	 */
	public static function removeDirectory($path) {
		$path = realpath($path) !== FALSE ? realpath($path) : $path;
		if (is_dir($path)) {
			foreach (new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($path, \RecursiveDirectoryIterator::SKIP_DOTS), \RecursiveIteratorIterator::CHILD_FIRST) as $file) {
				if ($file->isDir()) {
					if (!rmdir($file->getRealPath())) throw new \FileException('    => Cannot remove directory ' . $file->getRealPath() . '.');
				} else if (!unlink($file->getRealPath())) throw new \FileException('    => Cannot remove file ' . $file->getRealPath() . '.');
			}
			if (!rmdir($path)) throw new \FileException("    => Cannot remove directory $path.");
		}
	}
}
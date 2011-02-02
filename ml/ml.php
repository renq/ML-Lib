<?php

namespace ml;



function autoload($className) {
	$parts = \explode('\\', $className);
	if (count($parts) > 1 && $parts[0] == 'ml') {
		array_shift($parts);
		$className = array_pop($parts);
		$dir = implode(DIRECTORY_SEPARATOR, $parts);
		$fileName = __DIR__ . DIRECTORY_SEPARATOR . str_replace('_', DIRECTORY_SEPARATOR, $dir . DIRECTORY_SEPARATOR . $className) . '.php';
		require $fileName;
	}
}
	
 
spl_autoload_register('ml\autoload');

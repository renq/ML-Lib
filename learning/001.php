<pre>
<?php


function handler($errno, $errstr, $errfile, $errline, $context) {
	echo "zajebisty hendler: $errstr\n";
	return true;
}


set_error_handler('handler');
set_error_handler('handler');
trigger_error('mesedz 1');
restore_error_handler();
trigger_error('mesedz 2');
restore_error_handler();
trigger_error('mesedz 3');
restore_error_handler();
set_error_handler('handler');
1/0;
trigger_error('mesedz 4');

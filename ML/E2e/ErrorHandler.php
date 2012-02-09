<?php

namespace ML\E2e;


class ErrorHandler {
	
	
	private static $instance;
	
	private $enabled = false;
	
	
	private function __construct() {
		
	}
	
	
	public static function getInstance() {
		if (!self::$instance instanceof ErrorHandler) {
			self::$instance = new ErrorHandler();
		}
		return self::$instance;
	}
	
	
	public final function __clone() {
		throw new \BadMethodCallException("Clone is not allowed");
	}
	
	
	public static function run() {
		$instance = self::getInstance();
		if (!$instance->enabled) {
			$instance->enabled = true;
			set_error_handler(array('ml\e2e\ErrorHandler', 'handler'), -1);
		}
		return $instance;
	}
	
	
	public static function stop() {
		$instance = self::getInstance();
		if ($instance->enabled) {
			$instance->enabled = false;
			restore_error_handler();
		}
		return $instance;
	}
	
	
	public function isEnabled() {
		return $this->enabled;
	}
	
	
	public static function handler($errno, $errstr, $errfile, $errline, $context) {
		if (error_reporting() & $errno) {
			switch ($errno) {
				case E_NOTICE:
					throw new NoticeException("E_NOTICE: $errstr; File: $errfile; Line: $errline", $errno);
			
				case E_USER_NOTICE:
					throw new UserNoticeException("E_USER_NOTICE: $errstr; File: $errfile; Line: $errline", $errno);
			
				case E_WARNING:
					throw new WarningException("E_WARNING: $errstr; File: $errfile; Line: $errline", $errno);
			        
				case E_USER_WARNING:
			        throw new UserWarningException("E_USER_WARNING: $errstr; File: $errfile; Line: $errline", $errno);
			        
				case E_STRICT:
					throw new StrictException("E_STRICT: $errstr; File: $errfile; Line: $errline", $errno);
				
				case E_DEPRECATED:
					throw new DeprecatedException("E_DEPRECATED: $errstr; File: $errfile; Line: $errline", $errno);
				
				case E_RECOVERABLE_ERROR:
					throw new RecoverableErrorException("E_RECOVERABLE_ERROR: $errstr; File: $errfile; Line: $errline", $errno);
			
			    default:
			        throw new UnknownException("UNKNOWN: $errstr; File: $errfile; Line: $errline", $errno);
		    }
		}
		return true;
	}
	
	
	
}
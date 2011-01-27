<?php

namespace ml\e2e;


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
		throw new BadMethodCallException("Clone is not allowed");
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
					throw new NoticeException("E_NOTICE: $errstr", $errno);
			
				case E_USER_NOTICE:
					throw new UserNoticeException("E_USER_NOTICE: $errstr", $errno);
			
				case E_WARNING:
					throw new WarningException("E_WARNING: $errstr", $errno);
			        
				case E_USER_WARNING:
			        throw new UserWarningException("E_USER_WARNING: $errstr", $errno);
			        
				// These errors are triggered in compile time. It's no way to catch them.
				/*
				case E_STRICT:
					throw new StrictException("E_STRICT: $errstr", $errno);
				
				case E_DEPRECATED:
					throw new DeprecatedException("E_DEPRECATED: $errstr", $errno);
				*/
			
			    default:
			        throw new UnknownException("UNKNOWN: $errstr", $errno);
		    }
		}
		return true;
	}
	
	
	
}
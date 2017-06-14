<?php
/**
 *  
 *  Copyright (C) 2017
 *
 *
 *  @who	   	PAJ
 *  @info   	paj@gaiterjones.com
 *  @license    blog.gaiterjones.com
 * 	 
 *
 *
 */
namespace PAJ\Application\Docker\Scale\DemoApp;

/* Main application Loader */
class Loader
{
	
	protected $__;
	protected $__config;
	protected $__security;
	
	public function __construct() {
		
		try
		{
			$this->set('errorMessage','');
			$this->loadConfig();
			$this->loadSecurity();			
			$this->loadExternalVariables();			
			$this->renderPage();
		
		}
		catch (\Exception $e)
	    {
	    	$this->set('errorMessage', 'ERROR : '. $e->getMessage(). "\n". ' <!-- <br><pre>'. "\n". $this->getExceptionTraceAsString($e). '</pre> -->');
			
			// log to logging module
			if ($this->__config->get('loggingEnabled')) {
				\PAJ\Library\Log\Helper::logThis('EXCEPTION : '. $e->getMessage(),$this->__config->get('applicationName'),true,$this->__config->get('logFilePath').'log',false);
			}			
						
	    	if (php_sapi_name() != 'cli') {
				$this->set('requestedpage','frontend_home-notloggedin');
				$this->renderPage();
			} else {
			
				echo $e->getMessage(). "\n";
				
			}
			
			exit;
	    }
	}

	private function loadConfig()
	{
		$this->__config= new config();
		
		$_version='BETA v0.0.0';
		$_versionNumber=explode('-',$_version);
		$_versionNumber=$_versionNumber[0];
		
		$this->set('version',$_version);
		$this->set('versionNumber',$_versionNumber);
	}
	
	private function loadSecurity()
	{
		if (php_sapi_name() != 'cli') {
			$this->set('loggedin',false);
		}
	}
	
	private function loadExternalVariables()
	{
		// default gui / sub page
		$_defaultSubPage='home';
		$_defaultGUI='frontend';

		// -- 403, 404 error handlers
		if(isset($_GET['403'])) { throw new \Exception ('#403 you do not have permission to access this page, sorry about that.');}
		if(isset($_GET['404'])) { throw new \Exception ('#404 the page you requested ('. $_SERVER[REQUEST_URI]. ') does not exist.');}
		
		// -- initialise variables from GET	
		//
		if(isset($_GET['ajax'])){ $_ajaxRequest = true;} else { $_ajaxRequest = false;}	
		if(isset($_GET['class'])){ $_ajaxClass = $_GET['class'];} else { $_ajaxClass = false;}
		
		if(isset($_GET['class'])){ $_ajaxClass = $_GET['class'];} else { $_ajaxClass = false;}
		if(isset($_GET['page'])){ $_defaultSubPage = $_GET['page'];}
		
		$_defaultSubPage=$_defaultSubPage.'-notloggedin';
	
		$this->set('requestedpage',$_defaultGUI. '_'. $_defaultSubPage);
	}
	

		public function renderPage()
		{
			// ouput methods
			// 1. HTML
			

			// get Page class
			$_pageClass=explode('_',$this->get('requestedpage'));
			$_requestedPage=$_pageClass[0];
			$_requestedSubPage=null;
			
			if (isset($_pageClass[1])) { $_requestedSubPage=$_pageClass[1]; }
			
			$_pageClass=__NAMESPACE__. '\\Page\\'.ucfirst($_requestedPage);
			
			if (!class_exists($_pageClass)) { throw new \Exception('Requested page class '. $_pageClass. ' is not valid.'); }
			
			$_page = new $_pageClass(array(
			  "requestedpage"		 	=> 		$_requestedPage,
			  "requestedsubpage"	 	=> 		$_requestedSubPage,
			  "version"	 				=> 		$this->get('version'),
			  "versionnumber"			=> 		$this->get('versionNumber'),			  
			  "loggedin"			 	=> 		$this->get('loggedin'),
			  "applicationname"		 	=> 		$this->__config->get('applicationName'),
			  "errorMessage" 		 	=>		$this->__['errorMessage']
			));
		
			echo $_page. ($_cache ? "\n<!-- CACHED -->" : "\n<!-- NOT CACHED -->");
			
			unset($_page);
		}

		public function getExceptionTraceAsString($exception) {
			$rtn = "";
			$count = 0;
			foreach ($exception->getTrace() as $frame) {
				$args = "";
				if (isset($frame['args'])) {
					$args = array();
					foreach ($frame['args'] as $arg) {
						if (is_string($arg)) {
							$args[] = "'" . $arg . "'";
						} elseif (is_array($arg)) {
							$args[] = "Array";
						} elseif (is_null($arg)) {
							$args[] = 'NULL';
						} elseif (is_bool($arg)) {
							$args[] = ($arg) ? "true" : "false";
						} elseif (is_object($arg)) {
							$args[] = get_class($arg);
						} elseif (is_resource($arg)) {
							$args[] = get_resource_type($arg);
						} else {
							$args[] = $arg;
						}   
					}   
					$args = join(", ", $args);
				}
				$rtn .= sprintf( "#%s %s(%s): %s(%s)\n",
										 $count,
										 $frame['file'],
										 $frame['line'],
										 $frame['function'],
										 $args );
				$count++;
			}
			return $rtn;
		}			
		
		public function set($key,$value)
		{
			$this->__[$key] = $value;
		}
			
	  	public function get($variable)
		{
			return $this->__[$variable];
		}
		
	}

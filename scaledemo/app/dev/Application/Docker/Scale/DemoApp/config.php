<?php
/*

	Edit configuration settings here

*/

// 
//
//

namespace PAJ\Application\Docker\Scale\DemoApp;

class config
{
	// configure memcache
	const useMemcache=false;
	const memcacheServer='memcached';
	const memcacheServerPort='11211';
	const memcacheTTL='604800';
	const cacheKey='DOCKER-SCALE-DEMOAPP';
	const cacheHTML=false;	
	
	// mysql database credentials
	const DBPATH = 'localhost';
	const DBUSER = 'webappssql';
	const DBPASS = 'Monday14111112';
	const DBNAME = 'appsecurity';
	
	// configure session variables
	//
	const sessionEnabled = true;
	const sessionLifetime = 86400;
	
	// configure logging module
	//
	const loggingEnabled = false; // logs to logging module
	// path to folder for file logging
	const logFilePath = '/var/www/logs/';
	// ttl for cached log data
	const logCacheTTL = '86400'; // 24 hours
	// show ip address in logs - see log.php
	const logShowIP = true;
	// show geo info in logs - see log.php
	const logGeoInfo = true;		
	
	// my constants here
	const applicationName = 'NEW APP TEMPLATE1';
	const applicationURL = 'http://blog.gaiterjones.com/dropdev/PAJ/www/NewAppTemplate/';
	const applicationDomain = 'blog.gaiterjones.com';
	const siteTitle='This is an application template.';
	
	// security
	const blockFailedAttempts = false;
	const SSLLogin = false;
	const securityEnabled = true;
	
	// timezone
	const timezone='Europe/Berlin';	
	
	
	public $_serverURL;
	public $_serverPath;
	
	public function __construct()
	{
		if (php_sapi_name() != 'cli') {
			$this->_serverURL=$this->serverURL();
			$this->_serverPath=$this->serverPath();
		}
	}
	
	
    public function get($constant) {
	
	    $constant = 'self::'. $constant;
	
	    if(defined($constant)) {
	        return constant($constant);
	    }
	    else {
	        return false;
	    }

	}

	/**
	 * serverURL function.
	 * 
	 * @access public
	 * @return string
	 */
	public function serverURL() {
	 $_serverURL = 'http';
	 if (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on") {$_serverURL .= "s";}
	 $_serverURL .= "://";
	 if ($_SERVER["SERVER_PORT"] != "80") {
	  $_serverURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"];
	 } else {
	  $_serverURL .= $_SERVER["SERVER_NAME"];
	 }
	 return $_serverURL;
	}
	
	private function serverPath() {
	 $_serverPath=$_SERVER["REQUEST_URI"];
	 //$_serverPath=explode('?',$_serverPath);
	 //$_serverPath=$_serverPath[0];
	 
	 return $_serverPath;
	}	
	
}




?>
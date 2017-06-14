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
 */
namespace PAJ\Application;
include '../../../autoload.php';
define ('ANS','Docker\Scale\DemoApp'); // Application Name Space

// App
$_PAJApp = new Docker\Scale\DemoApp\Loader();
	unset($_PAJApp);
		exit;


?>
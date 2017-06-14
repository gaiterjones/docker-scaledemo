<?php
/**
 *  
 *  Copyright (C) 2017 paj@gaiterjones.com
 *
 * 	http://www.medazzaland.co.uk/dropbox/dev/new-app-template/?template=Overflow
 *
 */

namespace PAJ\Application\Docker\Scale\DemoApp\Page;

/**
 * Page FRONTEND class.
 * 
 * @extends Page
 */
class Frontend extends \PAJ\Application\Docker\Scale\DemoApp\Page {


	public function __construct($_variables) {
	
		// load parent
		parent::__construct($_variables);
		
		// define valid subpages and login status for pages
		//
		$_validSubPages=array(
			'home-notloggedin' 		=> array('secure' => false)
		);		

		$_subPageLinks=false;
		$_subPage=$this->get('requestedsubpage'); // get subpage

		$_loggedIn=$this->get('loggedin'); // get logged in status
		
		// check requested sub page is valid
		//
		if (!array_key_exists($_subPage, $_validSubPages) && $_subPage != null) {
			throw new \Exception ($this->__t->__('The requested page was not found').  ' - ('. $_subPage. ')');
		}

		// check login status for sub page is valid if security enabled
		//
		if ($this->__config->get('securityEnabled') && $_validSubPages[$_subPage]['secure']=== true && !$_loggedIn && $_subPage != null) {
			
			// redirect to login page
			$_SESSION["originatingpage"] = $this->__config->_serverURL.$_SERVER['REQUEST_URI'];
			
			//setcookie("originatingpage", $_SERVER[HTTP_HOST].$_SERVER[REQUEST_URI], time() + (86400 * 30), "/"); // 86400 = 1 day
			header('Location: '. $this->__config->get('applicationURL'). 'login/');
			exit;
		}			
		
	
		// load html for page - PAJ
		$_template=new \PAJ\Application\Docker\Scale\DemoApp\Page\Frontend\HTML\Template\PAJ($this->__);
			$this->set('html',$_template->get('html'));
				unset($_template);			
			
		
		// render page
		$this->createPage($_validSubPages);
		
	}


		/**
		 * __toString function.
		 * 
		 * @access public
		 * @return void
		 */
		public function __toString()
		{
			$_html=$this->get('pageHtml');
			
			// log page views to logging module
			if ($this->__config->get('loggingEnabled')) {
				\PAJ\Library\Log\Helper::logThis('PAGE : '. $this->get('requestedsubpage'). ' - '. $_SERVER[REQUEST_URI],$this->__config->get('applicationName'),false);
			}

			// return and minify DEV true/false, ULTRA true/false
			return ($this->minify($_html,true,false));

		}

}
?>
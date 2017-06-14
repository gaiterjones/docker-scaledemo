<?php
/**
 *  
 *  Copyright (C) 2017 paj@gaiterjones.com
 *
 * 	
 *
 */
 
namespace PAJ\Application\Docker\Scale\DemoApp\Page\Frontend\HTML\Template;

/**
 * HTML TEMPLATE class.
 * http://scaledemo.dev.com/
 * @extends Page
 */
class PAJ extends \PAJ\Application\Docker\Scale\DemoApp\Page {

public function __construct($_variables) {
	
	parent::__construct($_variables);

	$this->html();

}

function html()
{
$_HTML[] = array
	(
    'page' => array
    	(
	    	'*',
	    ),
    'html' => '
<!DOCTYPE HTML>
<html>
	<head>
		<title>DOCKER SCALE DEMO APP</title>
		<meta http-equiv="content-type" content="text/html; charset=utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes"/>
		<META NAME="ROBOTS" CONTENT="NOINDEX, NOFOLLOW">
		<link href="//maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
        <link rel="stylesheet" type="text/css" href="/css/reset.css" media="all">
        <link rel="stylesheet" type="text/css" href="/css/style.css" media="all">
        <!-- Google Fonts embed code -->
        <script type="text/javascript">
            (function() {
                var link_element = document.createElement("link"),
                    s = document.getElementsByTagName("script")[0];
                if (window.location.protocol !== "http:" && window.location.protocol !== "https:") {
                    link_element.href = "http:";
                }
                link_element.href += "//fonts.googleapis.com/css?family=Overpass:100italic,100,200italic,200,300italic,300,400italic,400,600italic,600,700italic,700,800italic,800,900italic,900";
                link_element.rel = "stylesheet";
                link_element.type = "text/css";
                s.parentNode.insertBefore(link_element, s);
            })();

		</script>
		
	</head>
	
	<body>
	  <div class="wrapper">
		<div>
		  <article>	
			<h1><i class="fa fa-check-circle" aria-hidden="true"></i>&nbsp;DOCKER SCALE DEMO APP</h1>
			'.	\PAJ\Application\Docker\Scale\DemoApp\Page\Frontend\Data::getCounter(). 
				\PAJ\Application\Docker\Scale\DemoApp\Page\Frontend\Data::getHostInfo(). 
				\PAJ\Application\Docker\Scale\DemoApp\Page\Frontend\Data::getManagerData(). '
			</article>
		</div>
'
);

// error meesage html
$_HTML[] = array
	(
    'page' => array
    	(
	    	'error',
	    ),
	'html' => '
		<!-- Error Page -->
				<div class="error">
				
						<header>
							<h1>Houston, we have a problem...</h1>
						</header>
						<p>
						How very <strong>embarrassing</strong>, something has gone wrong.
						<p>
						'. $this->get('errorMessage'). '
						</p>
				</div>					
	
	'
	);	
	

// footer
$_HTML[] = array
	(
    'page' => array
    	(
	    	'*',
	    ),
	'html' => '

		</div>
		<div class="footer">
			<p><span class="copyleft">&copy;</span>2017 <a target="_blank" href="http://blog.gaiterjones.com">blog.gaiterjones.com</a></p>
		</div>
	</body>
</html>
'
);





$this->set('html',$_HTML);
	
}


}
?>
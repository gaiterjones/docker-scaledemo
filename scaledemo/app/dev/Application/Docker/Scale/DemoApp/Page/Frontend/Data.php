<?php
/**
 *  
 *  Copyright (C) 2017
 *
 *
 *  @who	   	PAJ
 *  @info   	paj@gaiterjones.com
 *  @license    -
 * 	
 *
 */

namespace PAJ\Application\Docker\Scale\DemoApp\Page\Frontend;

class Data {

	function getCounter()
	{
		// get cached counter
		//
		$_key='docker-scaledemoapp-counter';
		$_counter=(int)\PAJ\Library\Cache\Helper::getCachedString($_key,false);
		if ($_counter)
		{
			// increment beans
			$_counter++;
		} else {
			$_counter=1;
		}
		
		\PAJ\Library\Cache\Helper::setCachedString($_counter,$_key,0);
		
		return '<div class="beancounter">
					<h1>
						Beans Counted : '. $_counter. '&nbsp;&nbsp;&nbsp;<i title="refresh" style="cursor: pointer;" class="fa fa-refresh" aria-hidden="true" onClick="window.location.reload()"></i>
					</h1>
				</div>
				';
	}
	
	
	function getManagerData()
	{
		$_html='<h1>Manager Data</h1><p>Waiting for manager to start (should take no more than 60 seconds) ...</p>';
		
		// get cached manager output
		//
		$_key='docker-scalemanager-output';
		$_managerData=\PAJ\Library\Cache\Helper::getCachedString($_key);
		
		// render html
		//
		if ($_managerData)
		{
			foreach ($_managerData['manageroutput']['upstreamcontainers'] as $_key => $_container)
			{
				$_ipA=$_container['networksettings']['Networks']['scaledemo_proxy']['IPAddress'];
				$_ipB=$_container['networksettings']['Networks']['scaledemo_wwwserver']['IPAddress'];
				$_background='white';
				if ($_ipA == getHostByName(getHostName()) || $_ipB == getHostByName(getHostName()))
				{
					$_background='yellow';
				}
				$_containerData[]='<span style="background-color: '. $_background. ';" class="ip'. str_replace('.','',$_ip) .'"><i class="fa fa-server" aria-hidden="true"></i>&nbsp;'. $_container['name']. ' <strong>'. $_container['state']. '</strong> - '. $_container['up']. '</span>';
			}
			
			$_hearbearTimeStamp=$_managerData['manageroutput']['heartbeat']['timestamp'];
			$_heartbeatElapsedTime=self::humanTiming((new \DateTime())->getTimestamp(),$_hearbearTimeStamp);
			
			$_html='
				<h1><i class="fa fa-server" aria-hidden="true"></i>&nbsp;X&nbsp;'. $_managerData['manageroutput']['scalecontainercount']. '&nbsp;&nbsp;&nbsp;Manager Data&nbsp</h1>
			
					<h2 title="manager cron job heartbeat"><i class="fa fa-heart" aria-hidden="true"></i>&nbsp;heartbeat : '. $_heartbeatElapsedTime. '</h2>
					<ul>
						<li>Project Name : '. $_managerData['manageroutput']['projectname']. '</li>
						<li>Scale service name : '. $_managerData['manageroutput']['servicename']. '</li>
						<li>Service container count : '. $_managerData['manageroutput']['scalecontainercount']. '</li>
					</ul>
					<ul>
						<li>'. implode('</li><li>',$_containerData). '</li>
					</ul>						
					<h2><i class="fa fa-cog" aria-hidden="true"></i>&nbsp;'. ($_managerData['manageroutput']['changedetected']==='true' ? $_managerData['manageroutput']['updatestream']:'No scale changes detected'). '</h2>
						<span style="display:block; padding-left: 25px">
							Docker scale command : 
						</span>
						<span style="display:block; padding-left: 35px">
							docker-compose scale '. $_managerData['manageroutput']['servicename']. '='. $_managerData['manageroutput']['scalecontainercount']. '
						</span>
						<span style="display:block; padding-left: 25px">
							Nginx dynups config : 
						</span>
						<span style="display:block; padding-left: 35px">
							'. implode(';',$_managerData['manageroutput']['nginxupstreamserverconfig']['output']). ';
						</span>
						<!--<pre>'. print_r($_managerData, true). '</pre>-->
			
			';
			
			
			
		}

		return $_html;
	}
	
	function getHostInfo()
	{
		$_html='
			<h2><i class="fa fa-retweet" aria-hidden="true"></i>&nbsp;running in : <strong>'. gethostname(). '</strong> : '. getHostByName(getHostName()). '</h2>
		';
		


		return $_html;
	}

	protected function humanTiming ($time1,$time2)
	{
	
		$time = $time1 - $time2; // to get the time since that moment
	
		$tokens = array (
			31536000 => 'year',
			2592000 => 'month',
			604800 => 'week',
			86400 => 'day',
			3600 => 'hour',
			60 => 'minute',
			1 => 'second'
		);
	
		foreach ($tokens as $unit => $text) {
			if ($time < $unit) continue;
			$numberOfUnits = floor($time / $unit);
			return $numberOfUnits.' '. $text.(($numberOfUnits>1)?'s':''). ' ago.';
		}
	
	}	
}
?>
<?php
/**
 *  Application CRON CLI for SCALE MANAGER APPLICATION
 *
 *  Copyright (C) 2017
 *
 *
 *  @who	   	PAJ
 *  @info   	paj@gaiterjones.com
 *  @license    blog.gaiterjones.com
 * 	
 */
 
namespace PAJ\Application\Docker\Scale\Manager\Cron;
 
class Cli extends CronController
{

	public function __construct() {
	
		parent::__construct();
		
		if (php_sapi_name() === 'cli') {
			$this->doCronCli();
		}

	}
	
	// -- command line / cron cli tasks
	//		
	private function doCronCli()
	{
	
		// commandline - cron
		//
		
			$_silent=false;
			$_forceUpdate=false;
		
			// cli cron jobs
			foreach($_SERVER['argv'] as $_value)
			{
				if ($_value==="silent") {$_silent=true;}
				if ($_value==="update") {$_forceUpdate=true;}
				
				if ($_silent) { error_reporting(0); }
				
				$_cmd=explode('=',$_value);
				  
				if (isset($_cmd[1])) // check for command=value
				{
					$_value=$_cmd[0];
					
				} 
				

				// scale manager CRON
				// monitor containers and update nginx dynamic upstream hosts configuration
				//
				// usage:
				//      php cron.php silent scalemanager - silent, use in cron
				//      php cron.php scalemanager - run with debug info
				//      php cron.php update scalemanager -  force an update
				//
				//
				if ($_value==="scalemanager") {
					
					$_upstreamScaleContainers=array();
					$_managerOutput=array();
					
					$_projectName=getenv('SCALE_MANAGER_PROJECTNAME');
					if (!$_projectName){ $_projectName='scaledemo';}

					$_scaleContainerServiceName=getenv('SCALE_CONTAINER_NAME');
					if (!$_scaleContainerServiceName){ $_scaleContainerServiceName='php-apache';}

					$_proxyNetworkName=getenv('PROXY_NETWORK_NAME');					
					if (!$_proxyNetworkName){ $_proxyNetworkName='proxy';}
					
					$_scaleContainerName=$_projectName.'_'.$_scaleContainerServiceName;
					
				
					try
					{					
						
						$_success=false;
						
						// get docker container data
						//
						$_obj=new \PAJ\Library\Docker\Scale\Manager\GetContainers();
						
							$_output=$_obj->get('output');
							$_success=$_obj->get('success');
							$_managerOutput['getcontainers']=$_output;
							unset($_obj);
						if($_success) // got container data
						{

							foreach ($_output['docker']['containers'] as $_container)
							{

								// parse containers for scaled upstream container names
								//
								if (strpos($_container['name'],$_scaleContainerName)!== false)
								{
									// add to array
									//
									$_upstreamScaleContainers[]=$_container;
								}
							}
							
								if (!$_silent) {echo count($_upstreamScaleContainers). ' upstream docker containers found.'. "\n";}
								$_managerOutput['upstreamcontainers']=$_upstreamScaleContainers;
							
							// extract upstream configuration data from containers
							//
							foreach($_upstreamScaleContainers as $_server)
							{
								$_managerOutput['containerdata']=$_server;
								$_containerUpstreamServerConfig[]='server '. $_server['networksettings']['Networks'][$_server['project'].'_proxy']['IPAddress'].':80';
								if (!$_silent) {echo $_server['name']. ' : '. $_server['networksettings']['Networks'][$_server['project'].'_proxy']['IPAddress']. ' - '. $_server['up']. "\n";}
							}
								
							$_managerOutput['containerupstreamconfig']=$_containerUpstreamServerConfig;
						
							if (count($_containerUpstreamServerConfig) > 0)
							{
								
								// get current nginx upstream config
								//
								$_dynamicUpstream=new \PAJ\Library\Docker\Scale\Nginx\DynamicUpstream();
									$_nginxUpstreamServerConfig=$_dynamicUpstream->getUpstream($_projectName,'nginx',true);
									$_managerOutput['nginxupstreamserverconfig']=$_nginxUpstreamServerConfig;								
									if (!$_silent) {echo count($_nginxUpstreamServerConfig['output']).' upstream servers in nginx config.'. "\n". implode(';',$_nginxUpstreamServerConfig['output'])."\n";}	
										
								$_managerOutput['updatestream']='nothing to do';
							
								// compare nginx config with docker scale containers available
								//								
								
								if ($_nginxUpstreamServerConfig['output'] !== $_containerUpstreamServerConfig || $_forceUpdate)
								{
									
									// change
									//
									$_managerOutput['changedetected']['timestamp']=(new \DateTime())->getTimestamp();
							
									if (!$_silent) {echo 'change detected!'. "\n";}
									$_managerOutput['changedetected']='true';
									
									// update nxinx upstream host config
									//
									$_updateUpstream=$_dynamicUpstream->updateUpstream($_projectName,'nginx',implode(';',$_containerUpstreamServerConfig));
									
									if (isset($_updateUpstream['output'][0]) && $_updateUpstream['output'][0] === 'add server failed')
									{
										if (!$_silent) {echo 'ERROR nginx upstream host configuration not updated : '. $_updateUpstream['output'][0]. "\n";}
										$_managerOutput['updatestream']='ERROR nginx upstream host configuration not updated : '. $_updateUpstream['output'][0];
										
									} else {
										
										if (!$_silent) {echo 'nginx upstream host configuration was updated '.  (new \DateTime())->format('d-m-Y H:i:s'). "\n";}
										$_managerOutput['updatestream']='nginx upstream host configuration was updated '. (new \DateTime())->format('d-m-Y H:i:s');
									}
									
									// increment namespace for cache
									//
									$_cache=new \PAJ\Library\Cache\Memcache();
										$_cache->incVersion($_projectName);
											unset($_cache);
									
								} else {
									
									// no change
									//
									if (!$_silent) {echo 'no upstream host configuration changes detected.'. "\n";}
									$_managerOutput['changedetected']='false';
									

											
								}
								
								unset($_dynamicUpstream);
								
							} else {
								
								$_managerOutput['updatestream']='ERROR - No upstream containers available!';
							}
						}
							
					}
					catch (\Exception $e)
					{
						// catch bad guys
						//throw new \Exception($e);
					}				
					
					$_managerOutput['projectname']=$_projectName;
					$_managerOutput['servicename']=$_scaleContainerServiceName;
					$_managerOutput['scalecontainername']=$_scaleContainerName;
					$_managerOutput['scalecontainercount']=count($_containerUpstreamServerConfig);
					$_managerOutput['heartbeat']['timestamp']= (new \DateTime())->getTimestamp();
					
					// cache manager output
					//
					$_key='docker-scalemanager-output';
					\PAJ\Library\Cache\Helper::setCachedString(array('manageroutput' => $_managerOutput),$_key);
					
					if (!$_silent) {
						
						print_r(array('manageroutput' => $_managerOutput));
						if (!$_silent) {echo 'done.'. "\n";}
					}
					
						
					exit;				
				
				}

			}
				
		
			exit; // cron finished
		
	}

}
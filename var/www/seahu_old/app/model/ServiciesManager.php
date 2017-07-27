<?php

namespace App\Model;

use Nette;
use App\Model;
use Tracy\Debugger;


class ServiciesManager 
{
	var $services;
	
	public function __construct()
	{
		$ip=$_SERVER['SERVER_ADDR'];
		if ($ip=="::1") $ip="127.0.0.1";
		
		$rex=array(
			'id'=>0,
			'title'=>"REX control system",
			'logo'=>"logo_rex.png",
			'preview'=>"preview_rex.png",
			'link'=>"http://$ip:8008",
			'homePage'=>"https://www.rexcontrols.com",
			'help'=>"pi/help/pi",
			'script'=>"rex.sh",
			'description'=>"Comercial excellent automation system not only for home automation.",
			'status'=>"?");
		$domoticz=array(
			'id'=>1,'title'=>"Domoticz",
			'logo'=>"logo_domoticz.png",
			'preview'=>"preview_domoticz.jpeg",
			'link'=>"http://$ip:8080",
			'homePage'=>"http://domoticz.com/",
			'help'=>"pi/help/domoticz",
			'script'=>"domoticz.sh",
			'description'=>"Open source home automation system, easy to use and with support lot of sesnsors.",
			'status'=>"?");
		$ser2net=array(
			'id'=>2,
			'title'=>"Serial port to net",
			'logo'=>"serial.png",
			'preview'=>"preview_ser2net.jpg",
			'link'=>"http://$ip/ser2net",
			'homePage'=>"http://$ip/help/serial_port_to_net/cz.Seahu_SH017_seriova_komunikace.pdf",
			'help'=>"pi/help/ser2net",
			'script'=>"ser2net.sh",
			'description'=>"Service for bridge serial ports over network. If you need control serial devices from remotely PC than you can use this module with this service.",
			'status'=>"?");
		$rfx433MHz=array(
			'id'=>3,
			'title'=>"RFX 433MHz",
			'logo'=>"rfx433MHz_logo2.svg",
			'preview'=>"rfx433Mhz_service2.svg",
			'link'=>"http://$ip/rfx433MHz",
			'homePage'=>"http://$ip/help/RFX433MHz/cz.Seahu_SH017_rfx433MHz.pdf",
			'help'=>"pi/help/rfx433MHz",
			'script'=>"rfx433MHz.sh",
			'description'=>"If you have on this module receiver and transceiver for free broadcast band 433MHz than you can use this service as interface between Domoticz automation system and wireless 433MHz devices.",
			'status'=>"?");
		//$Services = array($domoticz,$rex);
		$this->services = array($domoticz,$rex,$ser2net,$rfx433MHz);
	}
	
	public function refereshStatus()
	{
		for($i=0; $i<count($this->services);$i++){
			$this->services[$i]['status']=$this->statusService($this->services[$i]['id']);
		}
	}

	public function enableServicie($id)
	{
		$this->enableService($id);
	}

	public function renderDisableServicie($id)
	{
		$this->disableService($id);
	}

	//enable service
	public function enableService($id)
	{
		$service=$this->getServiceById($id);
		Debugger::barDump($service, 'service');
		$script=$service['script'];
		$exe="/usr/bin/sudo /opt/seahu/services/service_$script enable";
		Debugger::barDump($exe, 'status exe');
		$output=shell_exec ($exe);
	}

	//disable service
	public function disableService($id)
	{
		$service=$this->getServiceById($id);
		Debugger::barDump($service, 'service');
		$script=$service['script'];
		$exe="/usr/bin/sudo /opt/seahu/services/service_$script disable";
		Debugger::barDump($exe, 'status exe');
		$output=shell_exec ($exe);
	}

	//status service
	protected function statusService($id)
	{
		$service=$this->getServiceById($id);
		Debugger::barDump($service, 'service');
		$script=$service['script'];
		$cmd="/usr/bin/sudo /opt/seahu/services/service_$script status";
		$output=shell_exec ("/usr/bin/sudo /opt/seahu/services/service_$script status");
		$lines=explode("\n", $output );
		if (count($lines)<2) return false;
		$last_line=$lines[count($lines)-2];
		Debugger::barDump($cmd, 'status cmd');
		Debugger::barDump($output, 'status output');
		Debugger::barDump($lines, 'status lines');
		Debugger::barDump($last_line, 'status last line');
		if ($last_line=="OK") return true;
		else return false; 
	}

	// get service by id
	protected function getServiceById($id)
	{
		foreach($this->services as $service) {
			if ($service['id']==$id) return $service;
		}
		return false;
	}

}


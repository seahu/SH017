<?php

namespace App\Presenters;

use Nette;
use App\Model;
use  Nette\Application\UI;
use Nette\Application\UI\Form;
use Tracy\Debugger;

class ModemPresenter extends BasePresenter
{

	protected function initArrs(){
		// find all serial devices
		$all_devices=scandir("/dev");
		Debugger::barDump($all_devices, 'all_devices');
		$devices=array();
		foreach($all_devices as $device){
			if(strpos($device,"ttyAMA")!== false OR 
				strpos($device, "ttyS")!== false OR
				strpos($device,"ttyUSB")!== false ) $devices[]=$device;
		}
		$this->template->devices = $devices;

		$this->template->bauds = array(
			300,
			1200,
			2400,
			4800,
			9600,
			19200,
			38400,
			57600,
			115200
		);
	}

	public function renderDefault()
	{
		$this->initArrs();
		$this->template->anyVariable = 'any value';
		//$this->netGetData();
		$this->modemGetActualSeting();
	}

	public function renderWait()
	{
		//jen definice pro router, aby nasel cestu a priradil si sablonu showNewCard.late v ni je pak vlozena komponenta newCardForm ktera vytvori a vozi formular definovany vyse
	}
		
	// --------------- configure port from -----------------------------------------
	protected function createComponentConfigureModem($cardId)
	{
		if (!$this->getUser()->isLoggedIn()) {
			$this->error('For edit you must be login.');
			//$this->redirect('Wifi:'); //pri vyrvareni component neni dostupne presmerovani
		}
		$this->initArrs();
		$this->modemGetActualSeting();
		$form = new UI\Form;
		$form->addCheckbox('enable', 'Enable modem');
		$form->addSelect('device', 'Device:', $this->template->devices);
		$form->addSelect('baud', 'Baud rate:', $this->template->bauds);
		$form->addText('dial', 'Dial :');
		$form->addText('apn', 'APN :');
		$form->addSubmit('insert', 'Set');
		$form->onSuccess[] = array($this, 'setConfigureModem');
		Debugger::barDump($this->template, 'template');

		// set defaults
		$defualtArr=array();
		$defualtArr['enable'] = ( $this->template->enable=="yes") ? 1 : 0 ;
		$val=$this->getArrKeyByVal($this->template->devices,$this->template->device);
		Debugger::barDump($val, 'val id device');
		if ( $val!==FALSE ) $defualtArr["device"]=$val;
		$val=$this->getArrKeyByVal($this->template->bauds,$this->template->baud);
		if ( $val!==FALSE ) $defualtArr["baud"]=$val;
		$defualtArr["dial"]=$this->template->dial;
		$defualtArr["apn"]=$this->template->apn;
		//Debugger::barDump($defualtArr, 'defualtArr');
		$form->setDefaults($defualtArr);		
		return $form;
	}
	
	//vlola se po uspesne odeslani formulare
	public function setConfigureModem(UI\Form $form, $values)
	{	
		if (!$this->getUser()->isLoggedIn()) {
			$this->error('For edit you must be login.');
		}
		$this->initArrs();
		// write new config to /etc/ppp/peers/Provider
		$context="";
		$context.= "/dev/".$this->template->devices[$values->device]."\n";
		$context.= $this->template->bauds[$values->baud]."\n";
		$context.= "defaultroute\n";
		$context.= "replacedefaultroute\n";
		$context.= "usepeerdns\n";
		$context.= "nodetach\n";
		$context.= "crtscts\n";
		$context.= "lock\n";
		$context.= "noauth\n";
		$context.= "local\n";
		$context.= "noipdefault\n";
		$context.= "debug\n";
		$context.= "nodeflate\n";
		$context.=  "connect \"/usr/sbin/chat -v -f /etc/chatscripts/Provider\"\n";
		file_put_contents('/etc/ppp/peers/Provider', $context);
		
		// write new config to /etc/chatscripts/Provider
		$context="";
		$context.= "TIMEOUT 10\n";
		$context.= "ABORT 'BUSY'\n";
		$context.= "ABORT 'NO ANSWER'\n";
		$context.= "ABORT 'ERROR'\n";
		$context.= "\n";
		$context.= "\"\" 'ATZ'\n";
		$context.= "\n";
		$context.= "SAY 'Setting APN FQDN\\n'\n";
		$context.= "OK 'AT+CGDCONT=1,\"IP\",\"".$values->apn."\"'\n";
		$context.= "\n";
		$context.= "ABORT 'NO CARRIER'\n";
		$context.= "SAY 'Dialling!\\n'\n";
		$context.= "OK 'ATD".$values->dial."'\n";
		$context.= "CONNECT ''\n";
		file_put_contents('/etc/chatscripts/Provider', $context);
		
		if ($values->enable=="on") {
			shell_exec ("/usr/bin/sudo /opt/seahu/services/service_modem.sh enable >/dev/null 2>/dev/null &");
			$this->flashMessage('Starting modem.');
			$this->redirect('Modem:wait');
			sleep(15);
		}
		else {
			shell_exec ("/usr/bin/sudo /opt/seahu/services/service_modem.sh disable");
			$this->flashMessage('Stop modem.');
		}

		$this->flashMessage('Sucessfuly change modem setting.');
		//$this->redirect('Homepage:');
		$this->redirect('Modem:');
	}	
	
	public function renderConfigureModem($noID)
	{
		//jen definice pro router, aby nasel cestu a priradil si sablonu showNewCard.late v ni je pak vlozena komponenta newCardForm ktera vytvori a vozi formular definovany vyse
	}

	// pomocna funkce urcena pro nastavovani defaultnich hodnot formulare	
	protected function getArrKeyByVal($arr,$val){ //vrati klic
		foreach ($arr as $key => $value){
			if ($value==$val) return $key;
		}
		return false;
	}

	protected function modemGetActualSeting()
	{
		// need edit /etc/sudoers to enable apache (www-data user) run script as superuser whithou press password
		// add line:
		//www-data  ALL=NOPASSWD: /etc/network/pi/getNetSeting.sh
		//run scan bash program generate output with seting report
		$output=shell_exec ("/usr/bin/sudo /opt/seahu/getModemSeting.sh");
		$lines=explode("\n", $output );
		$this->template->enable = $lines[0];
		$this->template->status = $lines[1];
		$this->template->device = $lines[2];
		$this->template->baud = $lines[3];
		$this->template->dial = $lines[4];
		$this->template->apn = $lines[5];
		$this->template->ip = $lines[6];
		$this->template->netmask = $lines[7];
		$this->template->gateway = $lines[8];
		$this->template->dns = $lines[9];
		$this->template->signal = $lines[10];
	}

}

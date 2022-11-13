<?php

namespace App\Presenters;

use Nette;
use App\Model;
use  Nette\Application\UI;
use Nette\Application\UI\Form;
use Tracy\Debugger;

class HomepagePresenter extends BasePresenter
{

	protected function initArrs(){
		// find all serial devices
		$all_devices=scandir("/dev");
		//Debugger::barDump($all_devices, 'all_devices');
		$devices=array();
		foreach($all_devices as $device){
			if(strpos($device,"ttyAMA")!== false OR 
				strpos($device, "ttyACM")!== false OR
				strpos($device, "ttyS")!== false OR
				strpos($device,"ttyUSB")!== false ) $devices["/dev/".$device]="/dev/".$device;
		}
		$this->template->devices = $devices;
		
		$this->template->rates = array(
			"300"=>300,
			"600"=>600,
			"1200"=>1200,
			"2400"=>2400,
			"4800"=>4800,
			"9600"=>9600,
			"19200"=>19200,
			"38400"=>38400,
			"57600"=>57600,
			"115200"=>115200,
			"230400"=>230400,
		);
		$this->template->parities= array (
			"n"=>"NONE",
			"e"=>"EVEN",
			"o"=>"ODD", 
		);
		$this->template->stopBits = array(
			"1"=>"1STOPBIT",
			"2"=>"2STOPBITS",
		);
		$this->template->dataBits = array(
			"7"=>"7DATABITS",
			"8"=>"8DATABITS",
		);
		$this->template->flows = array(
			"none"=>"",
			"xonxoff"=>"XONXOFF",
			"rtscts"=>"RTSCTS",
		);
		$this->template->states = array(
			"on"=>"Enable",
			"off"=>"Disable",
		);
		$this->template->protocols = array(
			"udp"=>"udp",
			"tcp"=>"tcp",
			"telnet"=>"telnet",
			"telnet(rfc2217)"=>"telnet(rfc2217)"
		);
		$this->template->remoteContols = array(
			"remctl",
		);
	}

	public function renderDefault()
	{
		$this->initArrs();
		$this->template->anyVariable = 'any value';
		//$this->netGetData();
		$this->Ser2netReadSeting();
	}

	// --------------- configure port from -----------------------------------------
	protected function createComponentConfigurePortForm($cardId)
	{
		$this->initArrs();
		$this->Ser2netReadSeting();
		// add other values to some array
		//array_unshift($this->template->flows,"NONE");
		$form = new UI\Form;
		$form->addText('control_port', 'Configure port number');
		$form->addSubmit('insert', 'Set');
		$form->onSuccess[] = array($this, 'setConfigurePort');

		if (isset($this->template->control_port)) {
			$form->setDefaults( array('control_port' => $this->template->control_port) );
		}
		return $form;
	}
	
	//vlola se po uspesne odeslani formulare
	public function setConfigurePort(UI\Form $form, $values)
	{	
		if (!$this->getUser()->isLoggedIn()) {
			$this->error('For edit you must be login.');
		}
		$this->Ser2netReadSeting();
		$control_port=$values->control_port;
		if ($control_port=="") unset($this->template->control_port);
		else $this->template->control_port=$control_port;
		//Debugger::barDump($conf, 'conf');
		$this->netWriteInterfaceSeting();

		$this->flashMessage('Sucessfuly change control port.');
		$this->redirect('Homepage:');
		//$this->redirect('Ser2net:');
	}	
	
	public function renderConfigurePortForm($noID)
	{
		//jen definice pro router, aby nasel cestu a priradil si sablonu showNewCard.late v ni je pak vlozena komponenta newCardForm ktera vytvori a vozi formular definovany vyse
	}


	
	// --------------- new configure entry -----------------------------------------
	protected function createComponentAddSer2netForm($cardId)
	{
		$this->initArrs();
		$this->Ser2netReadSeting();
		// add other values to some array
		//array_unshift($this->template->flows,"NONE");
		$form = new UI\Form;
		$form->addSelect('enable', 'Enable:', $this->template->states);
		$form->addSelect('device', 'Device:', $this->template->devices);
		$form->addSelect('protocol', 'Protocol', $this->template->protocols);
		$form->addText('port', 'Port number');
		$form->addSelect('rate', 'Baud rate:', $this->template->rates);
		$form->addText('timeout', 'Connection time out:');
		$form->addSelect('parity', 'Parity:', $this->template->parities);
		$form->addSelect('stopBit', 'Stop bits:', $this->template->stopBits);
		$form->addSelect('dataBit', 'Data bits:', $this->template->dataBits);
		$form->addSelect('flow', 'Flow control:', $this->template->flows);

		$form->addSubmit('insert', 'add');
		$form->onSuccess[] = array($this, 'addSer2net');
		
		
		// get next defaut port
		$port=1000;
		foreach($this->template->ser2net_config as $conf) if ($conf["port"]>$port) $port=$conf["port"];
		$port=$port+1;

		// set defaults
		$form->setDefaults(array(
			//'device' => '/dev/ttyAMA0',
			'protocol' => 'tcp',
			'port' => $port,
			'rate' => '9600',
			'timeout' => "600",
			'parity' => 'n',
			'stopBit' => 1,
			'dataBit' => 8,
			'flow' => 'none',
		));
		return $form;
	}

	//vlola se po uspesne odeslani formulare
	public function addSer2net(UI\Form $form, $values)
	{	
		if (!$this->getUser()->isLoggedIn()) {
			$this->error('For edit you must be login.');
		}
		$this->Ser2netReadSeting();
		$conf=array();
		$conf["enable"]=$values->enable;
		$conf["device"]=$values->device;
		$conf["protocol"]=$values->protocol;
		$conf["port"]=$values->port;
		$conf["timeout"]=$values->timeout;
		$conf["rate"]=$values->rate;
		$conf["parity"]=$values->parity;
		$conf["stopBit"]=$values->stopBit;
		$conf["dataBit"]=$values->dataBit;
		$conf["flow"]=$values->flow;
		if ($conf["flow"]=="none") $conf["flow"]="";
		//Debugger::barDump($conf, 'conf');
		$this->template->ser2net_config[]=$conf;
		$this->netWriteInterfaceSeting();

		$this->flashMessage('Sucessfuly add new serial to network port.');
		$this->redirect('Homepage:');
	}

	public function renderAddSer2netForm($noID)
	{
		//jen definice pro router, aby nasel cestu a priradil si sablonu showNewCard.late v ni je pak vlozena komponenta newCardForm ktera vytvori a vozi formular definovany vyse
	}

	// --------------- edit configure entry -----------------------------------------
	protected function createComponentEditSer2netForm($noID)
	{
		$noID = $this->getParameter('noID'); // id se nepredava v argumentech funkce jak by se dalo ocekatvat, ale musim siho vyzvednou pomoci getParameter
		//Debugger::barDump($noID, 'noID');
		$this->initArrs();
		$this->Ser2netReadSeting();
		// add other values to some array
		$conf=$this->template->ser2net_config[$noID];
		if (array_search($conf["device"], $this->template->devices)==false) $this->template->devices[$conf["device"]]=$conf["device"]; // if device is not on actual aviable devices list, then add it here
		$form = new UI\Form;
		$form->addHidden('noID', $noID);
		$form->addSelect('enable', 'Enable:', $this->template->states);
		$form->addSelect('device', 'Device:', $this->template->devices);
		$form->addSelect('protocol', 'Protocol:', $this->template->protocols);
		$form->addText('port', 'Port number');
		$form->addSelect('rate', 'Baud rate:', $this->template->rates);
		$form->addText('timeout', 'Connection time out:');
		$form->addSelect('parity', 'Parity:', $this->template->parities);
		$form->addSelect('stopBit', 'Stop bits:', $this->template->stopBits);
		$form->addSelect('dataBit', 'Data bits:', $this->template->dataBits);
		$form->addSelect('flow', 'Flow control:', $this->template->flows);

		$form->addSubmit('insert', 'Edit');
		$form->onSuccess[] = array($this, 'editSer2net');

		// set defaut values
		$defualtArr=array();
		if ( $conf["flow"]=="" ) $conf["flow"]="none";
		foreach(array('enable','protocol','device','rate','parity','stopBit','dataBit','flow','port','timeout') as $options){
			$defualtArr["$options"]=$conf["$options"];
			$conf["port"];
		}

		//Debugger::barDump($defualtArr, 'defualtArr');
		$form->setDefaults($defualtArr);
		return $form;
	}

	//vlola se po uspesne odeslani formulare
	public function editSer2net(UI\Form $form, $values)
	{
		if (!$this->getUser()->isLoggedIn()) {
			$this->error('For edit you must be login.');
		}
		$noID=$values->noID;
		$this->Ser2netReadSeting();
		$conf=array();
		$conf["protocol"]=$values->protocol;
		$conf["port"]=$values->port;
		$conf["enable"]=$values->enable;
		$conf["timeout"]=$values->timeout;
		$conf["device"]=$values->device;
		$conf["rate"]=$values->rate;
		$conf["parity"]=$values->parity;
		$conf["stopBit"]=$values->stopBit;
		$conf["dataBit"]=$values->dataBit;
		$conf["flow"]=$values->flow;
		if ( $conf["flow"]=="none" ) $conf["flow"]=""; // flow none hook
		//Debugger::barDump($conf, 'conf');
		$this->template->ser2net_config[$noID]=$conf;
		$this->netWriteInterfaceSeting();

		$this->flashMessage('Sucessfuly edit serial to network port.');
		$this->redirect('Homepage:');
	}

	public function renderEditSer2netForm($noID)
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

	public function renderDeleteQuery($noID)
	{
		$noID = $this->getParameter('noID');
		$this->initArrs();
		$this->template->noID = $noID;
		$this->Ser2netReadSeting();
		$this->template->conf = $this->template->ser2net_config[$noID];
	}

	public function renderDeleteItem($noID){
		//$noID = $this->getParameter('noID'); // tady yase nevim proc ele noID paramter prebimram z parametru funkce
		$this->initArrs();
		$this->Ser2netReadSeting();
		$new_config=array();
		Debugger::barDump($noID, 'noID');
		foreach($this->template->ser2net_config as $id => $conf){
			if ($id!=$noID) $new_config[]=$conf;
		}
		$this->template->ser2net_config=$new_config;
		$this->netWriteInterfaceSeting();
		$this->flashMessage("Item was deleted.");
		$this->redirect('Homepage:');
	}

	public function renderStartSer2net(){
		$output=shell_exec ("/usr/bin/sudo /opt/seahu/services/service_ser2net.sh enable");
		$this->flashMessage("Service was started.");
		$this->redirect('Homepage:');
	}

	public function renderStopSer2net(){
		$output=shell_exec ("/usr/bin/sudo /opt/seahu/services/service_ser2net.sh disable");
		$this->flashMessage("Service was stpped");
		$this->redirect('Homepage:');
	}


	/*
	** auxliary finction for praxe part yaml file to get basic values
	*/
	protected function parse_part_yaml_config($yaml){
					$con=yaml_parse($yaml);
					//Debugger::barDump($con, 'conf');
					//default values
					$conf=array();
					$conf["enable"]="off";
					$conf["timeout"]="";
					$conf["protocol"]="";
					$conf["port"]="";
					$conf["device"]="";
					$conf["rate"]="";
					$conf["parity"]="";
					$conf["dataBit"]="";
					$conf["stopBit"]="";
					$conf["flow"]="";
					if ( isset($con["connection"]["enable"])   ) $conf["enable"]=$con["connection"]["enable"];
					if ( isset($con["connection"]["timeout"])   ) $conf["timeout"]=$con["connection"]["timeout"];
					if ( isset($con["connection"]["accepter"]) )  $acceptor=explode(",",$con["connection"]["accepter"]);
					if ( isset($acceptor[0]) )  $conf["protocol"]=$acceptor[0];
					if ( isset($acceptor[1]) )  $conf["port"]=$acceptor[1];
					if ( isset($con["connection"]["connector"]) ) $connector=explode(",",$con["connection"]["connector"]);
					if ( isset($connector[1]) ) $conf["device"]=$connector[1];
					if ( isset($connector[2]) ) {
						$parametrs=$connector[2];
						if ( strlen($parametrs)>=strlen("300N81") ) {
							$conf["rate"]=substr($parametrs,0,-3);
							$conf["parity"]=substr($parametrs,-3,1);
							$conf["dataBit"]=substr($parametrs,-2,1);
							$conf["stopBit"]=substr($parametrs,-1);
						}
					}
					if ( isset($connector[3]) ) $options=explode(" ", $connector[3]);
					if ( isset($options[1]) ) $conf["flow"]=$options[1];
					//Debugger::barDump($conf, 'conf');
		return $conf;
	}

	protected function Ser2netReadSeting()
	{
		$output=shell_exec ("/usr/bin/sudo /opt/seahu/services/service_ser2net.sh status");
		if ($output=="OK\n") $this->template->service_status = true;
		else $this->template->service_status = false;
		$ser2net_config=array();
		// read setting from config file from my mark place
		$lines = file('/etc/ser2net.yaml');
		$start_my_config=false; // mark in configure file where start my part of config
		$yaml_connection="";
		foreach ($lines as $line_num => $line) {
			//Debugger::barDump($line);
			if ( strpos($line,"#--- start seahu config ---",0) !== false ) $start_my_config=true;
			if ($start_my_config==false) continue;
			// --- part with my config ---
			if (strpos($line,"connection:",0)!==false) { // zacatek sekce connection
				if ($yaml_connection!="") $ser2net_config[]=$this->parse_part_yaml_config($yaml_connection); // tam kde neco zacina tam musi neco skoncit -> ypracuj predchozi sekci
				$yaml_connection=$line;
			}
			if ($yaml_connection!="") $yaml_connection.=$line;
		}
		if ($yaml_connection!="") $ser2net_config[]=$this->parse_part_yaml_config($yaml_connection);
		//Debugger::barDump($ser2net_config, 'ser2net_config');
		$this->template->ser2net_config = $ser2net_config;
	}

	protected function netWriteInterfaceSeting()
	{
		// read config file up to my mar
		$lines = file('/etc/ser2net.yaml');
		$context="";
		foreach ($lines as $line_num => $line) {
			$context.=$line;
			if ( strpos($line,"#--- start seahu config ---",0) !== false ) break;
		}
		// after at add my own config text generateg from config arrray
		foreach($this->template->ser2net_config as $id => $conf){
			//Debugger::barDump($conf, 'conf');
			// create yaml array structure by value from conf array
			$yaml=array( "connection" => array (
					"enable" => $conf["enable"] ,
					"timeout" => intval($conf["timeout"]),
					"accepter" => $conf["protocol"].",".$conf["port"] ,
					"connector" => "serialdev,".$conf["device"].",".$conf["rate"].$conf["parity"].$conf["dataBit"].$conf["stopBit"].",local ".$conf["flow"] ,
				),
			);
			$yaml_text=yaml_emit($yaml);
			// do some cosmetic change in yaml text to corespond ser2net.yaml format
			$yaml_text=str_replace("---\n", "", $yaml_text); // delete head
			$yaml_text=str_replace("connection:", "connection: &seahu_$id", $yaml_text); // add identificator
			$yaml_text=str_replace("...\n", "", $yaml_text); // delete food
			//Debugger::barDump($yaml_text, 'new_config');
			$context.="\n";
			$context.=$yaml_text;
		}
		file_put_contents('/etc/ser2net.yaml', $context);
		// need edit /etc/sudoers to enable apache (www-data user) run script as superuser whithou press password
		// add line:
		//www-data  ALL=NOPASSWD: /opt/seahu/services/service_sert2net.sh
		//run scan bash program generate sids list
		//$output=shell_exec ("/usr/bin/sudo /opt/seahu/services/service_ser2net.sh stop");
		//$output=shell_exec ("/usr/bin/sudo /opt/seahu/services/service_ser2net.sh start");
	}
}

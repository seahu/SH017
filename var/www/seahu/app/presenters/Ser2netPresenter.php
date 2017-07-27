<?php

namespace App\Presenters;

use Nette;
use App\Model;
use  Nette\Application\UI;
use Nette\Application\UI\Form;
use Tracy\Debugger;

class Ser2netPresenter extends BasePresenter
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
		
		$this->template->rates = array(
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
		$this->template->parities= array (
			"NONE",
			"EVEN",
			"ODD", 
		);
		$this->template->stopBits = array(
			"1STOPBIT",
			"2STOPBITS",
		);
		$this->template->dataBits = array(
			"7DATABITS",
			"8DATABITS",
		);
		$this->template->flows = array(
			"XONXOFF",
			"RTSCTS",
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
		array_unshift($this->template->flows,"NONE");
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
		$this->Ser2netReadSeting();
		$control_port=$values->control_port;
		if ($control_port=="") unset($this->template->control_port);
		else $this->template->control_port=$control_port;	
		//Debugger::barDump($conf, 'conf');
		$this->netWriteInterfaceSeting();

		$this->flashMessage('Sucessfuly change control port.');
		//$this->redirect('Homepage:');
		$this->redirect('Ser2net:');
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
		array_unshift($this->template->flows,"NONE");
		$form = new UI\Form;
		$form->addSelect('device', 'Device:', $this->template->devices);
		$form->addText('port', 'Port number');
		$form->addSelect('rate', 'Baud rate:', $this->template->rates);
		$form->addText('timeout', 'Connection time out:');
		$form->addSelect('parity', 'Parity:', $this->template->parities);
		$form->addSelect('stopBit', 'Stop bits:', $this->template->stopBits);
		$form->addSelect('dataBit', 'Data bits:', $this->template->dataBits);
		$form->addSelect('flow', 'Flow control:', $this->template->flows);
		if (isset($this->template->control_port)) {
			$form->addCheckbox('remoteContol', 'Remote controls (by RFC 2217)');
		}
		$form->addSubmit('insert', 'add');
		$form->onSuccess[] = array($this, 'addSer2net');
		
		
		// get next defaut port
		$port=1000;
		foreach($this->template->ser2net_config as $conf) if ($conf["port"]>$port) $port=$conf["port"];
		$port=$port+1;

		// set defaults
		$form->setDefaults(array(
			//'device' => 0,
			'port' => $port,
			'rate' => 4,
			'timeout' => "0",
			//'parity' => 0,
			//'stopBit' => 0,
			'dataBit' => 1,
			'flow' => 0,
			//'remoteContol' => FALSE,
		));
		
		return $form;
	}
	
	//vlola se po uspesne odeslani formulare
	public function addSer2net(UI\Form $form, $values)
	{	
		$this->Ser2netReadSeting();
		$conf=array();
		$conf["port"]=$values->port;
		$conf["state"]="raw";
		$conf["timeout"]=$values->timeout;
		$conf["device"]=$this->template->devices[$values->device];
		$conf["rate"]=$this->template->rates[$values->rate];
		$conf["parity"]=$this->template->parities[$values->parity];
		$conf["stopBit"]=$this->template->stopBits[$values->stopBit];
		$conf["dataBit"]=$this->template->dataBits[$values->dataBit];
		$conf["flow"]=$this->template->flows[$values->flow];
		if ($conf["flow"]=="NONE") $conf["flow"]="";
		if (isset($this->template->control_port)) {
			if ($values->remoteContol==TRUE) $conf["remoteContol"]="remctl";
			else $conf["remoteContol"]="";
		}
		else  $conf["remoteContol"]="";
		Debugger::barDump($conf, 'conf');
		$this->template->ser2net_config[]=$conf;
		$this->netWriteInterfaceSeting();

		$this->flashMessage('Sucessfuly add new serial to network port.');
		//$this->redirect('Homepage:');
		$this->redirect('Ser2net:');
	}
	
	public function renderAddSer2netForm($noID)
	{
		//jen definice pro router, aby nasel cestu a priradil si sablonu showNewCard.late v ni je pak vlozena komponenta newCardForm ktera vytvori a vozi formular definovany vyse
	}

	// --------------- edit configure entry -----------------------------------------
	protected function createComponentEditSer2netForm($noID)
	{
		$noID = $this->getParameter('noID'); // id se nepredava v argumentech funkce jak by se dalo ocekatvat, ale musim siho vyzvednou pomoci getParameter
		$this->initArrs();
		$this->Ser2netReadSeting();
		// add other values to some array
		array_unshift($this->template->flows,"NONE");
		$form = new UI\Form;
		$form->addHidden('noID', $noID);
		$form->addSelect('device', 'Device:', $this->template->devices);
		$form->addText('port', 'Port number');
		$form->addSelect('rate', 'Baud rate:', $this->template->rates);
		$form->addText('timeout', 'Connection time out:');
		$form->addSelect('parity', 'Parity:', $this->template->parities);
		$form->addSelect('stopBit', 'Stop bits:', $this->template->stopBits);
		$form->addSelect('dataBit', 'Data bits:', $this->template->dataBits);
		$form->addSelect('flow', 'Flow control:', $this->template->flows);
		if (isset($this->template->control_port)) {
			$form->addCheckbox('remoteContol', 'Remote controls (by RFC 2217)');
		}
		$form->addSubmit('insert', 'Edit');
		$form->onSuccess[] = array($this, 'editSer2net');
		
		Debugger::barDump($noID, 'noID');
		// get defaut values
		$conf=$this->template->ser2net_config[$noID];
		Debugger::barDump($conf, 'conf');
		// set defaults
		$defualtArr=array();
		foreach(array(
							'device'=>$this->template->devices,
							'rate'=>$this->template->rates,
							'parity'=>$this->template->parities,
							'stopBit'=>$this->template->stopBits,
							'dataBit'=>$this->template->dataBits,
							'flow'=>$this->template->flows) as $options => $values){
			$value_options=$this->getArrKeyByVal($values,$conf[$options]);
			if ($value_options===FALSE) continue;
			$defualtArr["$options"]=$value_options;
		}
		foreach(array('port','timeout') as $options){
			$defualtArr["$options"]=$conf["$options"];
			$conf["port"];
		}
		if (isset($this->template->control_port)) {
			$defualtArr['remoteContol'] = ( $conf["remoteContol"]=="remctl") ? 1 : 0 ; 
		}
		Debugger::barDump($defualtArr, 'defualtArr');
		$form->setDefaults($defualtArr);
		return $form;
	}

	//vlola se po uspesne odeslani formulare
	public function editSer2net(UI\Form $form, $values)
	{	
		$noID=$values->noID;
		$this->Ser2netReadSeting();
		$conf=array();
		$conf["port"]=$values->port;
		$conf["state"]="raw";
		$conf["timeout"]=$values->timeout;
		$conf["device"]=$this->template->devices[$values->device];
		$conf["rate"]=$this->template->rates[$values->rate];
		$conf["parity"]=$this->template->parities[$values->parity];
		$conf["stopBit"]=$this->template->stopBits[$values->stopBit];
		$conf["dataBit"]=$this->template->dataBits[$values->dataBit];
		$conf["flow"]=$this->template->flows[$values->flow];
		if ($conf["flow"]=="NONE") $conf["flow"]="";
		if (isset($this->template->control_port)) {
			if ($values->remoteContol==TRUE) $conf["remoteContol"]="remctl";
			else $conf["remoteContol"]="";
		}
		else  $conf["remoteContol"]="";
		Debugger::barDump($conf, 'conf');
		$this->template->ser2net_config[$noID]=$conf;
		$this->netWriteInterfaceSeting();

		$this->flashMessage('Sucessfuly edit serial to network port.');
		//$this->redirect('Homepage:');
		$this->redirect('Ser2net:');
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

	public function renderDeleteItem($id){
		$noID = $this->getParameter('noID');
		$this->initArrs();
		$this->Ser2netReadSeting();
		$new_config=array();
		foreach($this->template->ser2net_config as $conf){
			if ($conf["id"]!=$noID) $new_config[]=$conf;  
		}
		$this->template->ser2net_config=$new_config;
		$this->netWriteInterfaceSeting();
		$this->flashMessage("Item was deleted.");
		//$this->redirect('Homepage:');
		$this->redirect('Ser2net:');
	}

	protected function Ser2netReadSeting()
	{
		// prepare emptny seting
		$ser2net_config=array();
		// read setting from interface file
		$lines = file('/etc/ser2net.conf');
		$id=0;
		foreach ($lines as $line_num => $line) {
			if (substr($line, 0, 1)=="#" or
				substr($line, 0, 7)=="BANNER:"
				) continue;
			if (substr($line, 0, 12)=="CONTROLPORT:") $this->template->control_port=substr($line, 12); 
			$words=explode(":", trim($line) );
			$count_words=count($words);
			if ($count_words<5 OR $count_words>6) continue;
			$conf=array();
			$conf["port"]=$words[0];
			$conf["state"]=$words[1];
			$conf["timeout"]=$words[2];
			$conf["device"]=$words[3];
			$conf["rate"]=$words[4];
			$conf["parity"]="";
			$conf["stopBit"]="";
			$conf["dataBit"]="";
			$conf["flow"]="";
			$conf["remoteContol"]="";
			if ($count_words=4){
				$options=explode(" ", trim($words[4]) );
				$conf["rate"]=$options[0];
				array_shift($options);
				//Debugger::barDump($options, 'options');
				foreach($options as $option){
					foreach($this->template->parities as $val) 		if ($option==$val) $conf["parity"]=$val;
					foreach($this->template->stopBits as $val) 		if ($option==$val) $conf["stopBit"]=$val;
					foreach($this->template->dataBits as $val) 		if ($option==$val) $conf["dataBit"]=$val;
					foreach($this->template->flows as $val) 			if ($option==$val) $conf["flow"]=$val;
					foreach($this->template->remoteContols as $val) 	if ($option==$val) $conf["remoteContol"]=$val;
				}
			}
			//$conf["remoteContol"]="remctl";
			$conf["id"]=$id;
			$ser2net_config[]=$conf;
			$id++;
		}
		Debugger::barDump($ser2net_config, 'ser2net_config');
		$this->template->ser2net_config = $ser2net_config;
	}

	protected function netWriteInterfaceSeting()
	{
		// write new configure to /etc/ser2net.conf file
		$context=file_get_contents("/etc/ser2net.conf.base");
		if (isset($this->template->control_port)) $context.="CONTROLPORT:".$this->template->control_port."\n";
		foreach($this->template->ser2net_config as $conf){
			$context.=$conf["port"].":".$conf["state"].":".$conf["timeout"].":".$conf["device"].":".$conf["rate"]." ".$conf["parity"]." ".$conf["stopBit"]." ".$conf["dataBit"]." ".$conf["flow"]." ".$conf["remoteContol"]."\n";
		}
		file_put_contents('/etc/ser2net.conf', $context);
	}
}

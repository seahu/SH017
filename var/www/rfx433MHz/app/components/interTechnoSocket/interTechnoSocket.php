<?php
namespace App\Presenters;
//namespace App\Model;

use Nette;
use App\Model;

use Nette\Application\UI;
use Tracy\Debugger;

/*
01011110111000000010000110010000	butA ON
01011110111000000010000110000000	butA OFF

01011110111000000010000110010001	butB ON
01011110111000000010000110000001	butB OFF

01011110111000000010000110010010	butC ON
01011110111000000010000110000010	butC OFF

01011110111000000010000110100000	all OFF

*/


//class KanluxApoTm3RemoveControl extends UI\Control
class InterTechnoSocket extends domoticzControl
{
	/** @Persistent */
    public $view="ahoj";
	//-------------------- vychozi nastaveni --------------------
	
	protected function defaultConfig()
	{
		$this->config=array(
			'familyCode'=>'01011110111000000010000110',
			'onCode'=>'1',
			'offCode'=>'0',
			'buttonCode'=>'0000',
			'protocolNumber'=>'7',
			'status'=>'OFF',
			'name'=>'Socket A',
			'scanning'=>'',
			'scanCode'=>'',
			'scanCodes'=>''
		);
	}

	// onCode=00
	// offCode=01
	// full code:
	// familyCode+buttonCode+OnOffCode
	//---------------------- zpracovani RFX signalu -------------------------------------------------
	// pro prijem RFX paketu pobezi na pozadi jiny program, ktery pri obdrzeni paktu, posle pres wget 
	// obsah tohoto paketu do resenteru a ten naslene vola nasledujici fci

	/*
	 * dekoduje prijaty kod 1='10', 0='01'
	 */
	protected function decode($signalCode)
	{
		$code="";
		for ($i=0; $i<strlen($signalCode); $i=$i+2) {
			$xx=substr($signalCode,$i,2);
			if ($xx=="10") $x="1";
			else $x="0"; //$xx=="01"
			$code=$code.$x;
		}
		return $code;
	}
	
	/*
	 * zakoduje prijaty kod 1='10', 0='01'
	 */
	protected function encode($signalCode)
	{
		$chars = str_split($signalCode);
		$signalCode="";
		foreach($chars as $char){
		 if     ($char=='1') $signalCode=$signalCode."10";
		 elseif ($char=='0') $signalCode=$signalCode."01";
		}
		return $signalCode;
	}

	/*
	 * obslouzi RFX signal, pokud nepatri tomuto zarizeni vrati FALSE, jinak po obslouzeni vrati TRUE
	 */
	public function serveRFXsignal($signalCode)
	{
		$arrSignalCode=explode ( ' ' , $signalCode);
		if (count($arrSignalCode)!=2 ) return FALSE;
		if ($arrSignalCode[1]!=$this->config->protocolNumber) return FALSE;
		$signalCode=$arrSignalCode[0];
		$signalCode=$this->decode($signalCode);
		if ($signalCode==FALSE) return FALSE;
		// this device not sending data only receiving
		Debugger::barDump($signalCode, 'signalCode');
		if ($this->config->scanning!="")
		{
			$this->config->scanCodes[]=$signalCode;
			$this->saveConfig();
		}
		Debugger::barDump($this->config, 'this->config');
		return FALSE;
	}
	
	/*
	 * obnovi stav vzdaleneho zarizeni (nikde neni zaruceno, ze vyslany kod zarizeni skutecne prijme proto je vhodne vysilani stale opakovat)
	 */
	public function refreshRFX()
	{
		if ($this->config->status=='ON') $this->sendRfxCode($this->config->familyCode.$this->config->onCode." ".$this->config->protocolNumber);
		if ($this->config->status=='OFF') $this->sendRfxCode($this->config->familyCode.$this->config->offCode." ".$this->config->protocolNumber);
	}
	
	protected function socketOn()
	{
		// vysli rfx signal
		$this->sendRfxCode($this->encode($this->config->familyCode."0".$this->config->onCode.$this->config->buttonCode)." ".$this->config->protocolNumber);
		// serve domoticz handle (ale jen pokud se jedna o zmenu)
		if ($this->config->status!='ON')
		{
			$idx=$this->findDeviceByName($this->config->name);
			$this->setStatusSwitch($idx,TRUE);
		}
		// na zaver uloz nove nastaveni
		$this->config->status='ON';
		$this->saveConfig(); 
	}

	protected function socketOff()
	{
		// vysli rfx signal
		$this->sendRfxCode($this->encode($this->config->familyCode."0".$this->config->offCode.$this->config->buttonCode)." ".$this->config->protocolNumber);
		// serve domoticz handle
		if ($this->config->status!='OFF')
		{
			$idx=$this->findDeviceByName($this->config->name);
			$this->setStatusSwitch($idx,FALSE);
		}
		// na zaver uloz nove nastaveni
		$this->config->status='OFF';
		$this->saveConfig(); 
	}
	
	protected function allOff()
	{
		// vysli rfx signal
		$code=$this->encode($this->config->familyCode."100000")." ".$this->config->protocolNumber;
		//$code==substr_replace($code,"100000",26,6);
		$this->sendRfxCode($code);
		// serve domoticz handle
		if ($this->config->status!='OFF')
		{
			$idx=$this->findDeviceByName($this->config->name);
			$this->setStatusSwitch($idx,FALSE);
		}
		// na zaver uloz nove nastaveni
		$this->config->status='OFF';
		$this->saveConfig(); 
	}
	


	//-------------------- ochytani signalu nette pro obsluhu componenty -----------------------------

	//-----------------
	// Control buttons
	//-----------------
	public function handleControl()
	{
		$this->view="Control"; // nastaveni promenne vyber na zaklade ktere se vybere spravne zobrazeni ve fci render()
	}
	
	public function handleSetSocket($status)
	{
		$this->view="Control"; // nastaveni promenne vyber na zaklade ktere se vybere spravne zobrazeni ve fci render()
		Debugger::barDump($status, 'status');
		Debugger::barDump($this->config, 'this->config');
		if ($status=="on") $this->socketOn();
		if ($status=="off") $this->socketOff();
	}
	
	public function handleAllOff()
	{
		$this->view="Control"; // nastaveni promenne vyber na zaklade ktere se vybere spravne zobrazeni ve fci render()
		$this->allOff();
	}

	public function renderControl()
	{
		// priprava dat pro sablonu
		$this->template->name=$this->getName();
		$this->template->config=$this->config;
		// prirava tlacitek (config, pripadne start, stop, ci neco jineho)
		// vyber sablony
		$this->template->setFile(__DIR__.'/interTechnoSocket.latte');
		//vykreslneni
		Debugger::barDump($this->template, 'this->template');
	}
	
	//----------------------------------
	// Seting RFX family and button code
	//----------------------------------
	protected function getCode()
	{
		//Debugger::barDump($this->config->familyCode, 'familyCode');
		$familyCode=substr($this->config->familyCode,0,26);
		$buttonCode=$this->config->buttonCode;
		//Debugger::barDump($familyCode, 'familyCode');
		return $familyCode.$buttonCode;
	}
	
	protected function setCode($code)
	{
		//Debugger::barDump($code, 'set code');
		$familyCode=substr($code,0,26);
		$new_code=substr_replace($this->config->familyCode,$familyCode,0,26);
		$this->config->familyCode=$new_code;
		$buttonCode=substr($code,26);
		$this->config->buttonCode=$buttonCode;
	}

	public function handleInterTechnoSocketSetCode()
	{
		//Debugger::barDump("on", 'Config');
		$this->view="InterTechnoSocketSetCode"; // nastaveni promenne vyber na zaklade ktere se vybere spravne zobrazeni ve fci render()
	}
	
	public function renderInterTechnoSocketSetCode()
	{
		//priradit sablonu
		$this->template->setFile(__DIR__.'/interTechnoSocket_setCode.latte');
		//naplnit data pro rendrovani
		$this->template->name=$this->getName();
		$this->template->config=$this->config;
		$this->template->code=array();
		for($i=0; $i<strlen($this->getCode()); $i=$i+1)
		{
			if (substr($this->getCode(),$i,1)==1) $this->template->code[]=TRUE;
			else $this->template->code[]=FALSE;
		}
		$this->template->codeLen=strlen($this->getCode());
		Debugger::barDump($this->template, 'this->template');		
	}

	//----------------------------------
	// Seting RFX code by scan
	//----------------------------------

	public function handleInterTechnoSocketScanCode()
	{
		//Debugger::barDump("on", 'Config');
		$this->view="InterTechnoSocketScanCode"; // nastaveni promenne vyber na zaklade ktere se vybere spravne zobrazeni ve fci render()
	}

	public function handleStopScanRFXCode(){
		parent::handleStopScanRFXCode();
		$this->config->familyCode=substr($this->config->scanCode,0,26);
		$this->config->buttonCode=substr($this->config->scanCode,28,4);
		$this->saveConfig(); 
	}

	public function renderScanRFXcode()
	{
		$this->renderInterTechnoSocketScanCode();
	}

	public function renderInterTechnoSocketScanCode()
	{
		//priradit sablonu
		$this->template->setFile(__DIR__.'/interTechnoSocket_scanCode.latte');
		//naplnit data pro rendrovani
		$this->template->name=$this->getName();
		$this->template->config=$this->config;
		Debugger::barDump($this->template, 'this->template');		
	}


	//---------------
	// rename buttons
	//---------------

	public function renderRenameDevice() // vyrendruje formular ze sablony
	{
		$this->template->setFile(__DIR__.'/interTechnoSocket_RenameSocket.latte'); // vybere sablonu kde se do ktere se vklda pres tovarnicku formular vyse
	}	
	
	//------------------
	// Domoticz function
	//------------------
	public function handleAddDomoticz() // odchyceni signalu
	{
		$id_HW=$this->getDomoticzHardwareId();
		$this->createNewDeviceSwitch($id_HW,$this->config->name);
		$idx=$this->findDeviceByName($this->config->name);
		$urlOn='http://localhost'.$this->link("SetSocket!", array("on"));
		$urlOff='http://localhost'.$this->link("SetSocket!", array("off"));
		//$this->setSwitchScripts($idx,"script:///opt/seahu/rfx433MHz/send_433Mhz.py ".$this->config->familyCode.$this->config->onCode,"script:///opt/seahu/rfx433MHz/send_433Mhz.py ".$this->config->familyCode.$this->config->offCode);
		$this->setSwitchScripts($idx,$urlOn,$urlOff);
		$this->presenter->flashMessage('Device witch name: "'.$this->config->name.'" is created');
	}

	//--------------
	// Defalt render
	//--------------
	public function renderDefual()
	{
		// priprava dat pro sablonu
		$this->template->name=$this->getName();
		$this->template->config=$this->config;

		// vyber sablony
		$this->template->setFile(__DIR__.'/interTechnoSocket.latte');
		//vykreslneni
		Debugger::barDump($this->template, 'this->template');
		
	}
	

}


<?php
namespace App\Presenters;
//namespace App\Model;

use Nette;
use App\Model;

use Nette\Application\UI;
use Tracy\Debugger;


//class KanluxApoTm3RemoveControl extends UI\Control
class GeneralOnOffSwitch extends domoticzControl
{

	//-------------------- vychozi nastaveni --------------------
	
	protected function defaultConfig()
	{
		$this->config=array(
			'CodeOn'=>'00000000000000000000 1',
			'CodeOff'=>'00000000000000000000 1',
			'status'=>'OFF',
			'name'=>'General Switch ON OFF',
			'scanning'=>"",
			'scanCodes'=>array()
		);
	}

	/*
	 * obslouzi RFX signal, pokud nepatri tomuto zarizeni vrati FALSE, jinak po obslouzeni vrati TRUE
	 */
	public function serveRFXsignal($signalCode)
	{
		// this device not sending data only receiving
		Debugger::barDump($signalCode, 'signalCode');
		if ($this->config->scanning!="")
		{
			$this->config->scanCodes[]=$signalCode;
			$this->saveConfig();
		}
		Debugger::barDump($this->config, 'this->config');
		return FALSE; // pokracuj v dalsim zpracovani
	}
	
	/*
	 * obnovi stav vzdaleneho zariceni (nikde neni zaruceno, ze vyslany kod zarizeni skutecne prijme proto je vhodne vysilani stale opakovat)
	 */
	public function refreshRFX()
	{
		if ($this->config->status=='ON') $this->sendRfxCode($this->config->CodeOn);
		if ($this->config->status=='OFF') $this->sendRfxCode($this->config->CodeOff);
	}
	
	protected function switchOn()
	{
		// vysli rfx signal
		$this->sendRfxCode($this->config->CodeOn);
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

	protected function switchOff()
	{
		// vysli rfx signal
		$this->sendRfxCode($this->config->CodeOff);
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
	
	public function handleSetSwitch($status)
	{
		$this->view="Control"; // nastaveni promenne vyber na zaklade ktere se vybere spravne zobrazeni ve fci render()
		Debugger::barDump($status, 'status');
		Debugger::barDump($this->config, 'this->config');
		if ($status=="on") $this->switchOn();
		if ($status=="off") $this->switchOff();
	}

	public function renderControl()
	{
		// priprava dat pro sablonu
		$this->template->name=$this->getName();
		$this->template->config=$this->config;
		// prirava tlacitek (config, pripadne start, stop, ci neco jineho)
		// vyber sablony
		$this->template->setFile(__DIR__.'/generalOnOffSwitch.latte');
		//vykreslneni
		Debugger::barDump($this->template, 'this->template');
	}
	
	//-------------------
	// Scanning RFX code
	//-------------------
	// vse potrebne je jiz v deviceCompomemnt.php jen je poreba prepsat fci renderSetRFXCode() tak ,aby obsahovala fci renderScanRFXCode()
	// je potreba napsat nasleduji fce
	
	public function renderSetRFXCode() // prepsat redka v nem vypiseaaktualni kod a vytvorit tlacitko scan a na nej vytvorit funkci pro hnadle a tlacitko stop ktere vypne priznak a zobrazi aktulani stav komonenty
	{
		$this->renderScanRFXCode();
	}
		
	//------------------
	// Domoticz function
	//------------------
	public function handleAddDomoticz() // odchyceni signalu
	{
		$id_HW=$this->getDomoticzHardwareId();
		$this->createNewDeviceSwitch($id_HW,$this->config->name);
		$idx=$this->findDeviceByName($this->config->name);
		$urlOn='http://localhost'.$this->link("SetSwitch!", array("on"));
		$urlOff='http://localhost'.$this->link("SetSwitch!", array("off"));
		//Debugger::barDump($urlOn, 'urlOn');
		//$this->setSwitchScripts($idx,"script:///opt/seahu/rfx433MHz/send_433Mhz.py ".$this->config->socketCode.$this->config->onCode,"script:///opt/seahu/rfx433MHz/send_433Mhz.py ".$this->config->socketCode.$this->config->offCode);
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
		$this->template->setFile(__DIR__.'/generalOnOffSwitch.latte');
		//vykreslneni
		Debugger::barDump($this->template, 'this->template');
		
	}

}


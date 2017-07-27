<?php
namespace App\Presenters;
//namespace App\Model;

use Nette;
use App\Model;

use Nette\Application\UI;
use Tracy\Debugger;


//class KanluxApoTm3RemoveControl extends UI\Control
class EuroPatronPanicButton extends domoticzControl
{

	//-------------------- vychozi nastaveni --------------------
	
	protected function defaultConfig()
	{
		$this->config=array(
			'CodePush'=>'00000000000000000000',
			'lastPush'=>0,
			'name'=>'Panic',
			'scanning'=>"",
			'scanCodes'=>array()
		);
	}

	/*
	 * obslouzi RFX signal, pokud nepatri tomuto zarizeni vrati FALSE, jinak po obslouzeni vrati TRUE
	 */
	public function serveRFXsignal($signalCode)
	{
		$arrSignalCode=explode ( ' ' , $signalCode);
		if (count($arrSignalCode)!=2 ) return FALSE;
		if ($arrSignalCode[1]!="1") return FALSE; // protocolNumber
		//Debugger::barDump($arrSignalCode[1], 'protocolNumber');
		$signalCode=$arrSignalCode[0];
		if (strlen($signalCode)!=24 ) return FALSE; // len packet
		// this device not sending data only receiving
		$this->scanRfxCode($signalCode);
		if ($signalCode==$this->config->CodePush) $this->buttonPush();
		return FALSE; // pokracuj v dalsim zpracovani
	}
 	
	protected function buttonPush()
	{
		// serve domoticz handle (ale jen pokud se jedna o zmenu)
		$idx=$this->findDeviceByName($this->config->name);
		$this->setStatusSwitch($idx,TRUE);
		// na zaver uloz nove nastaveni
		$this->config->lastPush=time();
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
	
	public function handlePushButton()
	{
		$this->view="Control"; // nastaveni promenne vyber na zaklade ktere se vybere spravne zobrazeni ve fci render()
		Debugger::barDump($this->config, 'this->config');
		$this->buttonPush();
	}

	public function renderControl()
	{
		// priprava dat pro sablonu
		$this->template->name=$this->getName();
		$this->template->config=$this->config;
		// prirava tlacitek (config, pripadne start, stop, ci neco jineho)
		// vyber sablony
		$this->template->setFile(__DIR__.'/euroPatronPanicButton.latte');
		//vykreslneni
		Debugger::barDump($this->template, 'this->template');
	}
	
	//-------------------
	// Scanning RFX code
	//-------------------
	// vse potrebne je jiz v deviceCompomemnt.php jen je poreba prepsat fci renderSetRFXCode() tak ,aby obsahovala fci renderScanRFXCode()
	// je potreba napsat nasleduji fce
	
	//public function renderScanRFXCode() // prepsat redka v nem vypiseaaktualni kod a vytvorit tlacitko scan a na nej vytvorit funkci pro hnadle a tlacitko stop ktere vypne priznak a zobrazi aktulani stav komonenty
	//{
	//	$this->renderScanRFXCode();
	//}
	public function handleScanRFXCode()
	{
		//Debugger::barDump("on", 'Config');
		$this->view="ScanRFXCode"; // nastaveni promenne vyber na zaklade ktere se vybere spravne zobrazeni ve fci render()
	}

	public function handleStopScanRFXCode() // vytvorit fci vypne odchytavaci priznak pro funkci serveRFXsignal
	{
		parent::handleStopScanRFXCode();
		$this->config->CodePush=$this->config->scanCode;
		$this->saveConfig(); 
		$this->view="ScanRFXCode";
	}

	public function renderScanRFXCode()
	{
		parent::renderScanRFXCode();
		$this->template->setFile(__DIR__.'/euroPatronPanicButton_scanCode.latte');
	}

	//---------------
	// rename buttons
	//---------------

	public function renderRenameDevice() // vyrendruje formular ze sablony
	{
		$this->template->setFile(__DIR__.'/euroPatronPanicButton_Rename.latte'); // vybere sablonu kde se do ktere se vklda pres tovarnicku formular vyse
	}


	//------------------
	// Domoticz function
	//------------------
	public function handleAddDomoticz() // odchyceni signalu
	{
		$id_HW=$this->getDomoticzHardwareId();
		$this->createNewDeviceSwitch($id_HW,$this->config->name);
		//$idx=$this->findDeviceByName($this->config->name);
		$this->presenter->flashMessage('Device witch name: "'.$this->config->name.'" is created');
	}

	public function renderDefual()
	{
		parent::renderDefual();
		$this->template->setFile(__DIR__.'/euroPatronPanicButton.latte');
	}
}

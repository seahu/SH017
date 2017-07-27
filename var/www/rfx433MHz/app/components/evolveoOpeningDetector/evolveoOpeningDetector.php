<?php
namespace App\Presenters;
//namespace App\Model;

use Nette;
use App\Model;

use Nette\Application\UI;
use Tracy\Debugger;
/************************************
* sample codes:
* -------------
normalni alarm
Received 000010001100001001010000 1

alarm slaba baterka
Received 000010001100001001010000 1
Received 000010001100001000001001 1

normalni vniknuti
Received 000010001100001000000110 1

vniknuti slaba baterka
Received 000010001100001000000110 1
Received 000010001100001000001001 1

vysledek:
-- family code---   -type code-
00001000110000100   1010000          1 alarm
00001000110000100   0000110          1 vniknuti
00001000110000100   0001001          1 slaba baterka
* 
000010001100001000001001 1
************************************/

//class KanluxApoTm3RemoveControl extends UI\Control
class EvolveoOpeningDetector extends domoticzControl
{

	//-------------------- vychozi nastaveni --------------------
	
	protected function defaultConfig()
	{
		$this->config=array(
			'familyCode'=>'00000000000000000000',
			'alarm'=>array('status'=>'OFF','IDdomoticz'=>'','name'=>'alarm','typeCode'=>"1010000", 'lastContact'=>0),
			'penetrate'=>array('status'=>'OFF','IDdomoticz'=>'','name'=>'penetrate','typeCode'=>"0000110", 'lastContact'=>0),
			'lowBattery'=>array('typeCode'=>"0001001", 'lastContact'=>0),
			'scanning'=>"",
			'scanCode'=>"",
			'scanCodes'=>array()
		);
	}

	/*
	 * obslouzi RFX signal, pokud nepatri tomuto zarizeni vrati FALSE, jinak po obslouzeni vrati TRUE
	 */
	public function serveRFXsignal($signalCode)
	{
		Debugger::barDump($signalCode, 'XXXXXXX');
		$arrSignalCode=explode ( ' ' , $signalCode);
		if (count($arrSignalCode)!=2 ) return FALSE;
		if ($arrSignalCode[1]!="1") return FALSE; // protocolNumber
		Debugger::barDump($arrSignalCode[1], 'protocolNumber');
		$signalCode=$arrSignalCode[0];
		Debugger::barDump($signalCode, 'signal code');
		if (strlen($signalCode)!=24 ) return FALSE; // len packet
		// this device not sending data only receiving
		$this->scanRfxCode($signalCode);
		Debugger::barDump($this->config, 'config');
		if (substr($signalCode,0,17) == $this->config->familyCode) $this->serveSignal(substr($signalCode,17));
		return FALSE; // pokracuj v dalsim zpracovani
	}
 	
	protected function serveSignal($type)
	{
		Debugger::barDump($type, 'serve signal type');
		if ($type==$this->config->alarm->typeCode) $this->alarm();
		if ($type==$this->config->penetrate->typeCode) $this->penetrate();
		if ($type==$this->config->lowBattery->typeCode) $this->low_battery();
		// na zaver uloz nove nastaveni
		$this->saveConfig(); 
	}

	protected function alarm()
	{
		$idx=$this->findDeviceByName($this->config->alarm->name);
		$this->setStatusSwitch($idx,TRUE);
		$this->config->alarm->status="ON";
		$this->config->alarm->lastContact=time();
	}

	protected function penetrate()
	{
		$idx=$this->findDeviceByName($this->config->penetrate->name);
		$this->setStatusSwitch($idx,TRUE);
		$this->config->penetrate->status="ON";
		$this->config->penetrate->lastContact=time();
	}

	protected function low_battery()
	{
		$idx=$this->findDeviceByName($this->config->alarm->name); // stav baterie k alarmu
		// pres json api domoticu nejde zmentit jen stav baterrie musi se nastavit stav celeho switche
		// coz je tak trochu neprijemne, protoze nizky stav baterri cidlo posila pri alarmu i vniknuti (otevreni vika
		// a ja to tu vzdy priradim jen k alarmu, na druhou se celkem nic nedeje, kdyz nekdo oddela viko tak klidne muzu zaslat i alarm signal).
		if ($this->config->alarm->status=="ON")	$this->setStatusSwitch($idx,TRUE,0);
		else $this->setStatusSwitch($idx,FALSE,0);
		$this->config->lowBattery->lastContact=time();
	}
	
	protected function reset()
	{
		$idx=$this->findDeviceByName($this->config->alarm->name);
		$this->setStatusSwitch($idx,FALSE,100);
		$this->config->alarm->status="OFF";
		$idx=$this->findDeviceByName($this->config->penetrate->name);
		$this->setStatusSwitch($idx,FALSE);
		$this->config->penetrate->status="OFF";
	}

	//-------------------- ochytani signalu nette pro obsluhu componenty -----------------------------

	//-----------------
	// Control buttons
	//-----------------
	public function handleControl()
	{
		$this->view="Control"; // nastaveni promenne vyber na zaklade ktere se vybere spravne zobrazeni ve fci render()
	}
	
	public function handleAlarmButton()
	{
		$this->view="Control"; // nastaveni promenne vyber na zaklade ktere se vybere spravne zobrazeni ve fci render()
		Debugger::barDump($this->config, 'this->config');
		$this->alarm();
		$this->saveConfig();
	}

	public function handlePenetrateButton()
	{
		$this->view="Control"; // nastaveni promenne vyber na zaklade ktere se vybere spravne zobrazeni ve fci render()
		Debugger::barDump($this->config, 'this->config');
		$this->penetrate();
		$this->saveConfig();
	}

	public function handleLowBatteryButton()
	{
		$this->view="Control"; // nastaveni promenne vyber na zaklade ktere se vybere spravne zobrazeni ve fci render()
		Debugger::barDump($this->config, 'this->config');
		$this->low_battery();
		$this->saveConfig();
	}

	public function handleResetButton()
	{
		$this->view="Control"; // nastaveni promenne vyber na zaklade ktere se vybere spravne zobrazeni ve fci render()
		Debugger::barDump($this->config, 'this->config');
		$this->reset();
		$this->saveConfig();
	}


	public function renderControl()
	{
		// priprava dat pro sablonu
		$this->template->name=$this->getName();
		$this->template->config=$this->config;
		// prirava tlacitek (config, pripadne start, stop, ci neco jineho)
		// vyber sablony
		$this->template->setFile(__DIR__.'/evolveoOpeningDetector.latte');
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
		$this->config->familyCode=substr($this->config->scanCode,0,17);
		$this->saveConfig(); 
		$this->view="ScanRFXCode";
	}

	public function renderScanRFXCode()
	{
		parent::renderScanRFXCode();
		$this->template->setFile(__DIR__.'/evolveoOpeningDetector_scanCode.latte');
	}

	//---------------
	// rename buttons
	//---------------
	public function createComponentFormRenameDevice() //tomarnicka pro fromulare pouziteho v sablone
	{
		$form = new UI\Form;
		//$form->addHidden('makerId',$this->params['makerId']);
		$form->addText('alarm', 'Alarm:');
		$form->addText('penetrate', 'Penetrate:');
		$form->addSubmit('edit', 'Edit');
		$form->onSuccess[] = array($this, 'exeRenameDevice');
		// set defaults
		$form->setDefaults(array(
			'but_A' => $this->config->but_A->name,
			'but_B' => $this->config->but_B->name,
			'but_C' => $this->config->but_C->name,
			'but_ALL_OFF' => $this->config->but_ALL_OFF->name,
		));
		return $form;
	}

	public function exeRenameDevice(UI\Form $form, $values) //obslouzi odeslane data
	{
		$this->config->alarm->name=$values->alarm;
		$this->config->penetrate->name=$values->penetrate;
		$this->saveConfig(); // na zaver uloz nove nastaveni
		// normalne v nasleduje presmerovani, ale zde se jen vratim a az v presenteru prije metoda render tak se to vse pekne vyrendruje podle aktualnich dat
	}

	public function handleRename() // odchyceni signalu
	{
		$this->view="RenameDevice"; // nastaveni promenne vyber na zaklade ktere se vybere spravne zobrazeni ve fci render()
	}

	public function renderRenameDeice() // vyrendruje formular ze sablony
	{
		$this->template->setFile(__DIR__.'/evolveoOpeningDetector_Rename.latte'); // vybere sablonu kde se do ktere se vklda pres tovarnicku formular vyse
	}


	//------------------
	// Domoticz function
	//------------------
	public function handleAddDomoticz() // odchyceni signalu
	{
		$id_HW=$this->getDomoticzHardwareId();
		$this->createNewDeviceSwitch($id_HW,$this->config->alarm->name);
		$this->createNewDeviceSwitch($id_HW,$this->config->penetrate->name);
		$this->reset();		
		$this->presenter->flashMessage('Device witch name: "'.$this->config->alarm->name.'" is created');
	}

	//------------------
	// Default render
	//------------------
	public function renderDefual()
	{
		parent::renderDefual();
		$this->template->setFile(__DIR__.'/evolveoOpeningDetector.latte');
	}
}

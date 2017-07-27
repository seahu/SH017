<?php
namespace App\Presenters;
//namespace App\Model;

use Nette;
use App\Model;

use Nette\Application\UI;
use Tracy\Debugger;


//class KanluxApoTm3RemoveControl extends UI\Control
class InterTechnoRemoveControl extends domoticzControl
{
	//-------------------- vychozi nastaveni --------------------
	
	protected function defaultConfig()
	{
		$this->config=array(
			'familyCode'=>'01011110111000000010000110',
			'onCode'=>'1',
			'offCode'=>'0',
			'but_A'=>array('status'=>'OFF','IDdomoticz'=>'','name'=>'Button A','buttonCode'=>"0000"),
			'but_B'=>array('status'=>'OFF','IDdomoticz'=>'','name'=>'Button B','buttonCode'=>"0001"),
			'but_C'=>array('status'=>'OFF','IDdomoticz'=>'','name'=>'Button C','buttonCode'=>"0010"),
			'but_ALL_OFF'=>array('IDdomoticz'=>'','name'=>'ALL OFF'),
			'protocolNumber'=>'7',
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
	 * obslouzi RFX signal, pokud neptri tomuto zarizeni vrati FALSE, jinak po obslouzeni vrati TRUE
	 */
	public function serveRFXsignal($signalCode)
	{
		$arrSignalCode=explode ( ' ' , $signalCode);
		if (count($arrSignalCode)!=2 ) return FALSE;
		if ($arrSignalCode[1]!=$this->config->protocolNumber) return FALSE;
		$signalCode=$arrSignalCode[0];
		$signalCode=$this->decode($signalCode);
		if ($signalCode==FALSE) return FALSE;
		// for scan code
		Debugger::barDump($signalCode, 'signalCode');
		if ($this->config->scanning!="")
		{
			$this->config->scanCodes[]=$signalCode;
			$this->saveConfig();
		}
		// for scan remote key event
		$familyCode=substr($signalCode,0,26);
		if ($familyCode!=$this->config->familyCode) return FALSE;
		$buttonCode=substr($signalCode,28,4);
		$onOffCode=substr($signalCode,27,1);
		$buttons=array($this->config->but_A, $this->config->but_B, $this->config->but_C );
		foreach ( $buttons as $item )
		{
			if ($buttonCode==$item->buttonCode)
			{
				if ($onOffCode==$this->config->onCode) $this->buttonOn($item);
				if ($onOffCode==$this->config->offCode) $this->buttonOff($item);
			}
		}
		$buttonAllOff=substr($signalCode,26,6);
		if ($buttonAllOff=="100000") $this->buttonAllOff();
	}
	
	protected function buttonOn($button)
	{
		Debugger::barDump($button, 'button');
		$button->status='ON';
		// serve domoticz handle
		$idx=$this->findDeviceByName($button->name);
		$this->setStatusSwitch($idx,TRUE);
		// na zaver uloz nove nastaveni
		$this->saveConfig(); 
	}

	protected function buttonOff($button)
	{
		$button->status='OFF';
		// serve domoticz handle
		$idx=$this->findDeviceByName($button->name);
		$this->setStatusSwitch($idx,FALSE);
		// na zaver uloz nove nastaveni
		$this->saveConfig(); 
	}

	protected function buttonAllOff()
	{
		// serve domoticz handle
		$idx=$this->findDeviceByName($this->config->but_ALL_OFF->name);
		$this->setStatusSwitch($idx,FALSE);
	}


	//-------------------- ochytani signalu nette pro obsluhu componenty -----------------------------

	//-----------------
	// Control buttons
	//-----------------
	public function handleControl()
	{
		$this->view="Control"; // nastaveni promenne vyber na zaklade ktere se vybere spravne zobrazeni ve fci render()
	}
	
	public function handleSetButton($but, $status)
	{
		$this->view="Control"; // nastaveni promenne vyber na zaklade ktere se vybere spravne zobrazeni ve fci render()
		Debugger::barDump($but, 'but');
		Debugger::barDump($status, 'status');
		Debugger::barDump($this->config, 'this->config');
		if ($status=="on") $this->buttonOn($this->config->$but);
		if ($status=="off") $this->buttonOff($this->config->$but);
	}

	public function renderControl()
	{
		// priprava dat pro sablonu
		$this->template->name=$this->getName();
		$this->template->config=$this->config;
		// prirava tlacitek (config, pripadne start, stop, ci neco jineho)
		// vyber sablony
		$this->template->setFile(__DIR__.'/interTechnoRemoteControl.latte');
		//vykreslneni
		Debugger::barDump($this->template, 'this->template');
	}
	

	//----------------------------------
	// Seting RFX code by scan
	//----------------------------------

	public function handleInterTechnoSocketScanCode()
	{
		//Debugger::barDump("on", 'Config');
		$this->view="InterTechnoRemoteControlScanCode"; // nastaveni promenne vyber na zaklade ktere se vybere spravne zobrazeni ve fci render()
	}

	public function handleStopScanRFXCode(){
		parent::handleStopScanRFXCode();
		$this->config->familyCode=substr($this->config->scanCode,0,26);
		$this->saveConfig(); 
	}

	public function renderScanRFXcode()
	{
		$this->renderInterTechnoRemoteControlScanCode();
	}

	public function renderInterTechnoRemoteControlScanCode()
	{
		//priradit sablonu
		$this->template->setFile(__DIR__.'/interTechnoRemoteControl_scanCode.latte');
		//naplnit data pro rendrovani
		$this->template->name=$this->getName();
		$this->template->config=$this->config;
		Debugger::barDump($this->template, 'this->template');		
	}

	
	//---------------
	// rename buttons
	//---------------
	public function createComponentFormRenameButtons() //tomarnicka pro fromulare pouziteho v sablone
	{
		$form = new UI\Form;
		//$form->addHidden('makerId',$this->params['makerId']);
		$form->addText('but_A', 'A:');
		$form->addText('but_B', 'B:');
		$form->addText('but_C', 'C:');
		$form->addText('but_ALL_OFF', 'ALL OFF:');
		$form->addSubmit('edit', 'Edit');
		$form->onSuccess[] = array($this, 'exeRenameButtons');
		// set defaults
		$form->setDefaults(array(
			'but_A' => $this->config->but_A->name,
			'but_B' => $this->config->but_B->name,
			'but_C' => $this->config->but_C->name,
			'but_ALL_OFF' => $this->config->but_ALL_OFF->name,
		));
		return $form;
	}

	public function exeRenameButtons(UI\Form $form, $values) //obslouzi odeslane data
	{
		$this->config->but_A->name=$values->but_A;
		$this->config->but_B->name=$values->but_B;
		$this->config->but_C->name=$values->but_C;
		$this->config->but_ALL_OFF->name=$values->but_ALL_OFF;
		$this->saveConfig(); // na zaver uloz nove nastaveni
		// normalne v nasleduje presmerovani, ale zde se jen vratim a az v presenteru prije metoda render tak se to vse pekne vyrendruje podle aktualnich dat
	}

	public function handleRename() // odchyceni signalu
	{
		$this->view="RenameButtons"; // nastaveni promenne vyber na zaklade ktere se vybere spravne zobrazeni ve fci render()
	}

	public function renderRenameButtons() // vyrendruje formular ze sablony
	{
		$this->template->setFile(__DIR__.'/interTechnoRemoteControl_RenameButons.latte'); // vybere sablonu kde se do ktere se vklda pres tovarnicku formular vyse
	}

	//------------------
	// Domoticz function
	//------------------
	public function handleAddDomoticz() // odchyceni signalu
	{
		$this->addSwitchToDomoticz($this->config->but_A);
		$this->addSwitchToDomoticz($this->config->but_B);
		$this->addSwitchToDomoticz($this->config->but_C);
		$this->addSwitchToDomoticz($this->config->but_ALL_OFF);
	}

	//pridani switche do domoticzu
	protected function addSwitchToDomoticz($objButton)
	{
		$id_HW=$this->getDomoticzHardwareId();
		$this->createNewDeviceSwitch($id_HW,$objButton->name);
		$idx=$this->findDeviceByName($objButton->name);
		//$this->setSwitchScripts($idx,$objButton->buttonCode);
		$this->presenter->flashMessage('Device witch name: "'.$objButton->name.'" is created');
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
		$this->template->setFile(__DIR__.'/interTechnoRemoteControl.latte');
		//vykreslneni
		Debugger::barDump($this->template, 'this->template');
		
	}
	

}


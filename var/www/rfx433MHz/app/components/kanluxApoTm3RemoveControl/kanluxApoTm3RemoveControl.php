<?php
namespace App\Presenters;
//namespace App\Model;

use Nette;
use App\Model;

use Nette\Application\UI;
use Tracy\Debugger;


//class KanluxApoTm3RemoveControl extends UI\Control
class KanluxApoTm3RemoveControl extends domoticzControl
{
	//-------------------- vychozi nastaveni --------------------
	
	protected function defaultConfig()
	{
		$this->config=array(
			'familyCode'=>'1111111111',
			'onCode'=>'0100',
			'offCode'=>'0001',
			'but_A'=>array('status'=>'OFF','IDdomoticz'=>'','name'=>'Button A','buttonCode'=>"0001010101"),
			'but_B'=>array('status'=>'OFF','IDdomoticz'=>'','name'=>'Button B','buttonCode'=>"0100010101"),
			'but_C'=>array('status'=>'OFF','IDdomoticz'=>'','name'=>'Button C','buttonCode'=>"0101000101"),
			'but_D'=>array('status'=>'OFF','IDdomoticz'=>'','name'=>'Button D','buttonCode'=>"0101010001"),
			'but_E'=>array('status'=>'OFF','IDdomoticz'=>'','name'=>'Button E','buttonCode'=>"0101010100"),
			'protocolNumber'=>'1'
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
	 * obslouzi RFX signal, pokud neptri tomuto zarizeni vrati FALSE, jinak po obslouzeni vrati TRUE
	 */
	public function serveRFXsignal($signalCode)
	{
		// frist 10 bit is used as family code
		if (strlen($signalCode)!=(24+2) ) return FALSE;
		// last char signalCode is protocol number must be 1
		if (substr($signalCode,25,1)!=$this->config->protocolNumber) return FALSE;
		// next continue signalCode withou protocol number
		$signalCode=substr($signalCode,0,24);
		if ( substr($signalCode,0,10)!=substr($this->config->familyCode,0,10) ) return FALSE;
		$buttonCode=substr($signalCode,10,10);
		$onOffCode=substr($signalCode,20,4);
		$buttons=array($this->config->but_A, $this->config->but_B, $this->config->but_C, $this->config->but_D, $this->config->but_E );
		foreach ( $buttons as $item )
		{
			if ($buttonCode==$item->buttonCode)
			{
				if ($onOffCode==$this->config->onCode) $this->buttonOn($item);
				if ($onOffCode==$this->config->offCode) $this->buttonOff($item);
			}
			
		}
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
		$this->template->setFile(__DIR__.'/kanluxApoTm3RemoveControl.latte');
		//vykreslneni
		Debugger::barDump($this->template, 'this->template');
	}
	
	//-----------------------
	// Seting RFX family code
	//-----------------------
	protected function getCode()
	{
		return $this->config->familyCode;
	}
	
	protected function setCode($code)
	{
		$this->config->familyCode=$code;
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
		$form->addText('but_D', 'D:');
		$form->addText('but_E', 'E:');
		$form->addSubmit('edit', 'Edit');
		$form->onSuccess[] = array($this, 'exeRenameButtons');
		// set defaults
		$form->setDefaults(array(
			'but_A' => $this->config->but_A->name,
			'but_B' => $this->config->but_B->name,
			'but_C' => $this->config->but_C->name,
			'but_D' => $this->config->but_D->name,
			'but_E' => $this->config->but_E->name,
		));
		return $form;
	}

	public function exeRenameButtons(UI\Form $form, $values) //obslouzi odeslane data
	{
		$this->config->but_A->name=$values->but_A;
		$this->config->but_B->name=$values->but_B;
		$this->config->but_C->name=$values->but_C;
		$this->config->but_D->name=$values->but_D;
		$this->config->but_E->name=$values->but_E;
		$this->saveConfig(); // na zaver uloz nove nastaveni
		// normalne v nasleduje presmerovani, ale zde se jen vratim a az v presenteru prije metoda render tak se to vse pekne vyrendruje podle aktualnich dat
	}

	public function handleRename() // odchyceni signalu
	{
		$this->view="RenameButtons"; // nastaveni promenne vyber na zaklade ktere se vybere spravne zobrazeni ve fci render()
	}

	public function renderRenameButtons() // vyrendruje formular ze sablony
	{
		$this->template->setFile(__DIR__.'/kanluxApoTm3RemoveControl_RenameButons.latte'); // vybere sablonu kde se do ktere se vklda pres tovarnicku formular vyse
	}

	//------------------
	// Domoticz function
	//------------------
	public function handleAddDomoticz() // odchyceni signalu
	{
		$this->addSwitchToDomoticz($this->config->but_A);
		$this->addSwitchToDomoticz($this->config->but_B);
		$this->addSwitchToDomoticz($this->config->but_C);
		$this->addSwitchToDomoticz($this->config->but_D);
		$this->addSwitchToDomoticz($this->config->but_E);
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
		$this->template->setFile(__DIR__.'/kanluxApoTm3RemoveControl.latte');
		//vykreslneni
		Debugger::barDump($this->template, 'this->template');
		
	}
	

}


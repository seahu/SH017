<?php
namespace App\Presenters;
//namespace App\Model;

use Nette;
use App\Model;

use Nette\Application\UI;
use Tracy\Debugger;


//class KanluxApoTm3RemoveControl extends UI\Control
class KanluxApoTm3Socket extends domoticzControl
{
	//-------------------- vychozi nastaveni --------------------
	
	protected function defaultConfig()
	{
		$this->config=array(
			'socketCode'=>'11111111110101010101',
			'onCode'=>'0100',
			'offCode'=>'0001',
			'protocolNumber'=>'1',
			'status'=>'OFF',
			'name'=>'Socket A'
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
	 * obslouzi RFX signal, pokud nepatri tomuto zarizeni vrati FALSE, jinak po obslouzeni vrati TRUE
	 */
	public function serveRFXsignal($signalCode)
	{
		// this device not sending data only receiving
		Debugger::barDump($signalCode, 'signalCode');
		return FALSE;
	}
	
	/*
	 * obnovi stav vzdaleneho zariceni (nikde neni zaruceno, ze vyslany kod zarizeni skutecne prijme proto je vhodne vysilani stale opakovat)
	 */
	public function refreshRFX()
	{
		if ($this->config->status=='ON') $this->sendRfxCode($this->config->socketCode.$this->config->onCode." ".$this->config->protocolNumber);
		if ($this->config->status=='OFF') $this->sendRfxCode($this->config->socketCode.$this->config->offCode." ".$this->config->protocolNumber);
	}
	
	protected function socketOn()
	{
		// vysli rfx signal
		$this->sendRfxCode($this->config->socketCode.$this->config->onCode." ".$this->config->protocolNumber);
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
		$this->sendRfxCode($this->config->socketCode.$this->config->offCode." ".$this->config->protocolNumber);
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

	public function renderControl()
	{
		// priprava dat pro sablonu
		$this->template->name=$this->getName();
		$this->template->config=$this->config;
		// prirava tlacitek (config, pripadne start, stop, ci neco jineho)
		// vyber sablony
		$this->template->setFile(__DIR__.'/kanluxApoTm3Socket.latte');
		//vykreslneni
		Debugger::barDump($this->template, 'this->template');
	}
	
	//-----------------------
	// Seting RFX family code
	//-----------------------
	protected function getCode()
	{
		Debugger::barDump($this->config->socketCode, 'socketCode');
		$familyCode=substr($this->config->socketCode,0,10);
		Debugger::barDump($familyCode, 'familyCode');
		$mFamilyCode="";
		for($i=0;$i<5;$i++)
		{
			$xx=substr($familyCode,$i*2,2);
			if ($xx=="11") $x="1";
			else $x="0"; // $xx=="01"
			$mFamilyCode=$mFamilyCode.$x;
		}
		// v druhe pulce koodu maji prepinace jinou vyslednou hodnotu tak to musim prekodovat
		$socketCode=substr($this->config->socketCode,10,10);
		Debugger::barDump($socketCode, 'socketCode');
		$mScoketCode="";
		for($i=0;$i<5;$i++)
		{
			$xx=substr($socketCode,$i*2,2);
			if ($xx=="00") $x="1";
			else $x="0"; // $xx=="01"
			$mScoketCode=$mScoketCode.$x;
		}
		Debugger::barDump($mScoketCode, 'mScoketCode');
		return $mFamilyCode.$mScoketCode;
	}
	
	protected function setCode($code)
	{
		Debugger::barDump($code, 'set code');
		$mFamilyCode=substr($code,0,5);
		$familyCode="";
		for ($i=0;$i<5;$i++)
		{
			$x=substr($mFamilyCode,$i,1);
			if ($x=="1") $xx="11";
			else $xx="01"; //$x=="0"
			$familyCode=$familyCode.$xx;
		}
		// prekodovad zpet
		$mSocketCode=substr($code,5,5);
		Debugger::barDump($mSocketCode, 'set socketCode');
		$scoketCode="";
		for($i=0;$i<5;$i++)
		{
			$x=substr($mSocketCode,$i,1);
			if ($x=="1") $xx="00";
			else $xx="01"; //$x=="0"
			$scoketCode=$scoketCode.$xx;
		}
		Debugger::barDump($scoketCode, 'set mScoketCode');
		$this->config->socketCode=$familyCode.$scoketCode;
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
		$this->template->setFile(__DIR__.'/kanluxApoTm3Socket.latte');
		//vykreslneni
		Debugger::barDump($this->template, 'this->template');
		
	}
	

}


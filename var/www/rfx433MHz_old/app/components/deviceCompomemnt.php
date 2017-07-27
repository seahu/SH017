<?php
namespace App\Presenters;
//namespace App\Model;
//namespace App\Components;

use Nette;
use App\Model;

use Nette\Application\UI;
use Tracy\Debugger;


//class KanluxApoTm3RemoveControl extends UI\Control
class deviceControl extends UI\Control
{
	var $config; // code set by switch on real devices (vhodne umistit do samostatne tridy rozsirujici tridu komponentu)
	// cache se pouzije z rodice
	var $cache;
	var $view;
	
	/*
	 * Nacte nastaveni a posledni stav z cache
	 * normalne bych to dal do konstruktoru, ale kdyz se vytvari instance tak jeste nezna svuj nazev ani predka to se nastavuje v presenteru az zavolanim metody 'addComponent(nova instance, nazev komponenty)'
	 * proto je potreba naceteni konfigurace spustit az po pripojeni k presenteru, tj. az zna svuj nazev
	 * (vhodne umistit do samostatne tridy rozsirujici tridu komponentu)
	*/
	public function loadConfig()
	{
		$parent=$this->getParent(); // zjisteni predka komponenty
		$this->cache=$parent->cache; // poziti cache z predka
		$name=$this->getName(); // zjisteni nazvu komponenty (podle nazvu konponenty ukladam jeji aktualni nastaveni a stav, vse ulozeno ve spolecne cache)
		$JsonDataFromCahe = $this->cache->load($name);
		if ($JsonDataFromCahe === NULL) 
		{
			$this->defaultConfig();
			$this->config = json_decode(json_encode($this->config)); // pri prevodu na json pouzity v cache se nektere pole meni v objekt, pomoci teto finty to srovnam i pro nove inicializavene instance
		}
		else $this->config = json_decode($JsonDataFromCahe);
		$this->saveConfig();
		$this->view=NULL;
	}
	
	/*
	 * ulozeni konfugurace (vhodne umistit do samostatne tridy rozsirujici tridu komponentu)
	*/
	public function saveConfig()
	{
		$name=$this->getName();
		if (isset($this->config)) $this->cache->save($name, json_encode($this->config) );
		else $this->cache->save($name, NULL );
		//Debugger::barDump($this->config, 'config');
	}

	//-------------------- vychozi nastaveni --------------------
	
	protected function defaultConfig()
	{
		// jan ukazkova sablona
		$this->config=array(
			'code'=>'1111111111',
			'mame'=>"",
			'scanning'=>"",
			'scanCodes'=>array()
		);
	}

	//-------------------- RFX-------------- --------------------

	/*
	 * obslouzi RFX signal, pokud neptri tomuto zarizeni vrati FALSE, jinak po obslouzeni vrati TRUE
	 */
	public function serveRFXsignal($signalCode)
	{
		//sablona pro funkci obsluhujici rfx kod z rijimace (tj. hlavne pro zpracovani dat z zarizeni, ktera neco odesilaji, tlacitka, teplmery atd. a zarizeni s obousmernou komunikcai)
	}

	/*
	 * obnovi stav vzdaleneho zariceni (nikde neni zaruceno, ze vyslany kod zarizeni skutecne prijme proto je vhodne vysilani stale opakovat)
	 */
	public function refreshRFX()
	{
		//sablona pro funkci znovuodesilajici stav vzdaleneho zarizeni (tj. hlavne pro zarizeni prijmajici povely)
	}
	
	/*
	 * odvisila zadany kod (kod se predpoklada jako string s binarni reprezentaci kodu)
	 */
	public function sendRfxCode($code)
	{
		$shell="/usr/bin/sudo /opt/seahu/rfx433MHz/codesend ".bindec($code); // v predkovi vytvorit slolecnou fci na rfx odeslini a prijem
		//$shell="/usr/bin/sudo /opt/seahu/rfx433MHz/send_433Mhz.py ".$code; // v predkovi vytvorit slolecnou fci na rfx odeslini a prijem
		Debugger::barDump($shell, 'shell');
		shell_exec ($shell);
	}

	//-------------------- ochytani signalu nette pro obsluhu componenty -----------------------------

	
	//--------------------------------------
	// Seting RFX family code by DIP switch
	//--------------------------------------
	public function handleSetRFXpartCode($switch,$status)
	{
		$this->view="SetRFXcode"; // nastaveni promenne vyber na zaklade ktere se vybere spravne zobrazeni ve fci render()
		if ($status=="on") $val=1;
		else $val=0;
		$new_code=substr_replace($this->getCode(),$val,$switch*2,1);
		$this->setCode($new_code);
		$this->saveConfig(); // na zaver uloz nove nastaveni
	}
	
	protected function getCode()
	{
		//sablona pro funkci vracejici kod pro jeho pouziti v konfiguraci
	}
	
	protected function setCode($code)
	{
		//sablona pro ulozeni codu
	}

	public function handleSetRFXCode()
	{
		//Debugger::barDump("on", 'Config');
		$this->view="SetRFXcode"; // nastaveni promenne vyber na zaklade ktere se vybere spravne zobrazeni ve fci render()
	}
	
	public function renderSetRFXCode()
	{
		//priradit sablonu
		$this->template->setFile(__DIR__.'/deviceCompomemnt_setRFXcode.latte');
		//naplnit data pro rendrovani
		$this->template->name=$this->getName();
		$this->template->config=$this->config;
		$this->template->code=array();
		for($i=0; $i<strlen($this->getCode()); $i=$i+2)
		{
			if (substr($this->getCode(),$i,1)==1) $this->template->code[]=TRUE;
			else $this->template->code[]=FALSE;
		}
		$this->template->codeLen=strlen($this->getCode())/2;
		//Debugger::barDump($this->template, 'this->template');		
	}

	//----------------------------
	// Seting RFX code by scanning
	//----------------------------

	/*
	 * pri scanovani kodu se pocita s tim ze v $this->conig existuje polozka 'scanning' kde se uklada aktualni priznak urcujici ktery z kodu se momentalne skenuje ON OFF nebo nic
	 * a polozka 'scanCodes' do ktere se ukladaji naskenovane kody (naskenuje se jich vic a pri ukonceni sknovani se vybre ten kod , ktery se nachazi nejvickrat)
	 */


	/*
	 * funkce obsluhjici scanovani rfx kodu pro ucely budouciho ulozeni
	 * pri pouziti scanoveni je potreba tutu fci volat z fce serveRFXsignal($signalCode)
	 */
	public function scanRfxCode($signalCode)
	{
		if ($this->config->scanning!="")
		{
			$this->config->scanCodes[]=$signalCode;
			$this->saveConfig();
		}
	}

	public function handleStartScanRFXCodeOn() // vytvorit fci nastvi odchytavaci priznak pro funkci serveRFXsignal($signalCode) ktera pote naskenovany cod ulozi
	{
		$this->view="SetRFXcode"; // nastaveni promenne vyber na zaklade ktere se vybere spravne zobrazeni ve fci render()
		$this->config->scanning="ON";
		$this->saveConfig(); 
	}

	public function handleStartScanRFXCodeOff() // vytvorit fci nastvi odchytavaci priznak pro funkci serveRFXsignal($signalCode) ktera pote naskenovany cod ulozi
	{
		$this->view="SetRFXcode"; // nastaveni promenne vyber na zaklade ktere se vybere spravne zobrazeni ve fci render()
		$this->config->scanning="OFF";
		$this->saveConfig(); 
	}

	public function handleStopScanRFXCode() // vytvorit fci vypne odchytavaci priznak pro funkci serveRFXsignal
	{
		$this->view="SetRFXcode"; // nastaveni promenne vyber na zaklade ktere se vybere spravne zobrazeni ve fci render()
		//find code who place most times
		Debugger::barDump($this->config->scanCodes, 'this->config->scanCodes');
		$selectCode='';
		$maxCount=0;
		foreach($this->config->scanCodes as $code)
		{
			$count=0;
			foreach($this->config->scanCodes as $item)
			{
				if ($item==$code) $count=$count+1;
			}
			if ($count>$maxCount) 
			{
				$selectCode=$code;
				$maxCount=$count;
			}
		}
		Debugger::barDump($selectCode, 'selectCode');
		if ($selectCode!='')
		{
			if ($this->config->scanning=="ON") $this->config->CodeOn=$selectCode;
			if ($this->config->scanning=="OFF") $this->config->CodeOff=$selectCode;
		}
		Debugger::barDump($this->config, 'this->config');
		$this->config->scanning="";
		$this->config->scanCodes=array();
		$this->saveConfig(); 
	}

	/*
	 * funckce  nastvujici vychozi render componenty pro scanovani kodu
	 * pro pouziti je potreba ve vlastni komponente prepsat fci. renderSetRFXCode() a z ni zavolat tuto fci
	 * pripadne napsat renderSetRFXCode() vlastni a toto brat jen jako vzor
	 */
	public function renderScanRFXCode() // prepsat redka v nem vypiseaaktualni kod a vytvorit tlacitko scan a na nej vytvorit funkci pro hnadle a tlacitko stop ktere vypne priznak a zobrazi aktulani stav komonenty
	{
		//prirada sablonu ve ktere je tlacitko odkazujici na handle StartScanRFXCode a handleStopScanRFXCode
		$this->template->setFile(__DIR__.'/deviceCompomemnt_scanRFXcode.latte');
		//naplnit data pro rendrovani
		$this->template->name=$this->getName();
		$this->template->config=$this->config;
		//Debugger::barDump($this->template, 'this->template');	
	}

	//---------------
	// rename buttons
	//---------------
	public function createComponentFormRenameDevice() //tomarnicka pro fromulare pouziteho v sablone
	{
		$form = new UI\Form;
		//$form->addHidden('makerId',$this->params['makerId']);
		$form->addText('name', 'Device name:');
		$form->addSubmit('edit', 'Edit');
		$form->onSuccess[] = array($this, 'exeRenameDevice');
		// set defaults
		$form->setDefaults(array(
			'name' => $this->config->name,
		));
		return $form;
	}

	public function exeRenameDevice(UI\Form $form, $values) //obslouzi odeslane data
	{
		$this->config->name=$values->name;
		$this->saveConfig(); // na zaver uloz nove nastaveni
		// normalne v nasleduje presmerovani, ale zde se jen vratim a az v presenteru prije metoda render tak se to vse pekne vyrendruje podle aktualnich dat
	}

	public function handleRename() // odchyceni signalu
	{
		$this->view="RenameSwitch"; // nastaveni promenne vyber na zaklade ktere se vybere spravne zobrazeni ve fci render()
	}

	public function renderRenameSwitch() // vyrendruje formular ze sablony
	{
		$this->template->setFile(__DIR__.'/deviceCompomemnt_RenameDevice.latte'); // vybere sablonu kde se do ktere se vklda pres tovarnicku formular vyse
	}

	//-----------------
	// visual function
	//-----------------
	
	/*
	 * Default render preda do rendru hodnoty z $this->config a vyrendruje latte sablonu dle nazvu tridy komponenty
	 */
	public function renderDefual()
	{
		$this->template->config=$this->config;

		// vyber sablony
		$className=get_class ($this);
		$className=(substr($className, strrpos($className, '\\') + 1));
		$this->template->setFile(__DIR__.'/'.$className.'.latte');
		//vykreslneni
		Debugger::barDump($className, 'className');
	}
	
	// tuto fci vola obsluha presenteru v okamziku kdy potrebuje vykreslit koponentu
	// z promenne $this->view vytahne nazev fce kterea ma obslouzit render tu nastavuji pri obsluze signalu (handle)
	// tim jsem docilil, ze stejna komponenta v zavislosti na obsluze udalsoti vypada pokazde jinak
	// pokud tato promenne neni nastavena tak se standartne vola renderDefaut 
	public function render(){
		if ($this->view==NULL) $this->renderDefual();
		else {
			$renderName="render".$this->view;
			Debugger::barDump($renderName, 'renderName');
			$this->$renderName();
		}
		// misto pro paticku
		$this->template->render();
	}
	


}


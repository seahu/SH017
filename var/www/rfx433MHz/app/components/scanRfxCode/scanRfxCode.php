<?php
namespace App\Presenters;

use Nette;
use App\Model;

use Nette\Application\UI;
use Tracy\Debugger;

//----------------------------
// Seting RFX code by scanning
//----------------------------

//class ScanRfxCode extends UI\Control
class ScanRfxCode extends domoticzControl
{
	// po napojeni komponenty na rodice nacti ulozenou konfiguraci (attached se totiz vola pri napojovani komponenty na rodice)
	protected function attached( $obj )
	{
		parent::attached( $obj );
		Debugger::barDump($this, 'this scancode');
		$this->loadConfig();
		Debugger::barDump($this->config, 'this config');
	}

	// vychozi konfigurace pokud jeste zadna neexistuje
	protected function defaultConfig()
	{
		$this->config=array(
			'code'=>'1',
			'scanning'=>"",
			'scanCodes'=>array()
		);
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

	public function handleStartScanRFXCode() // vytvorit fci nastvi odchytavaci priznak pro funkci serveRFXsignal($signalCode) ktera pote naskenovany cod ulozi
	{
		$this->config->scanning="RUN";
		$this->saveConfig(); 
	}

	public function handleStopScanRFXCode() // vytvorit fci vypne odchytavaci priznak pro funkci serveRFXsignal
	{
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
			if ($this->config->scanning=="RUN") $this->config->code=$selectCode;
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


	

	
	public function render()
	{
		//Debugger::barDump($this, 'this scancode');
		//$this->loadConfig();
		$this->template->setFile(__DIR__ . '/scanRfxCode.latte');
		$this->template->name=$this->getName();
		$this->template->config=$this->config;
		$this->template->render();
	}
}

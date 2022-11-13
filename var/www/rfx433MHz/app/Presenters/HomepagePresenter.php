<?php

namespace App\Presenters;

use Nette;
use App\Model;
use  Nette\Application\UI;
use Nette\Application\UI\Form;
use Tracy\Debugger;
use Nette\Caching\Cache;



class HomepagePresenter extends BasePresenter
{
	var $supportedDevices;
	var $useDevices;
	var $cache;
	
	// pocatecni inicalizace
	public function startup()
	{
		parent::startup(); //puvodni start up presenteru
		$this->initArrs();
		$this->createDeviceComponents();
		Debugger::barDump($this, 'this');
	}

	// toto patri do modelu
	protected function initArrs(){
		// list supported devices
		$webShop="http://seahu-shop-eng.webnode.cz"; # my webshop
		$storage = new Nette\Caching\Storages\FileStorage(__DIR__.'/../../temp');
		$this->cache = new Cache($storage);
		$this->supportedDevices = array(
			array(
				'id'=>0,'maker'=>"Kanlux",'image'=>"images/makers/kanlux.svg", 'products'=>array(
					//array('id'=>0,'name'=>"APO TM3 socket",'image'=>"images/products/Kanlux-APO-TM-socket.svg",'obj'=>new pokus,'bay'=>"http://shop.cz/products/AOP_TM3_socket"),
					array('id'=>0,'name'=>"APO TM3 socket",'image'=>"images/products/Kanlux-APO-TM-socket.svg",'obj'=>'App\Presenters\KanluxApoTm3Socket','bay'=>$webShop."/products/Kanlux_AOP_TM3"),
					array('id'=>1,'name'=>"APO TM3 remove control",'image'=>"images/products/Kanlux-APO-TM-remote-control.svg",'obj'=>'App\Presenters\KanluxApoTm3RemoveControl','bay'=>$webShop."/products/Kanlux_AOP_TM3"),
				)
			),
			array(
				'id'=>1,'maker'=>"general",'image'=>"images/makers/general.svg", 'products'=>array(
					array('id'=>0,'name'=>"general switch",'image'=>"images/products/OnOffSwitch.svg",'obj'=>'App\Presenters\GeneralOnOffSwitch','bay'=>$webShop."/products/general_switch"),
					array('id'=>1,'name'=>"general button",'image'=>"images/products/OnOffButton.svg",'obj'=>'App\Presenters\GeneralOnOffButton','bay'=>$webShop."/products/general_button"),
					//array('id'=>1,'name'=>"APO TM3 remove control",'image'=>"images/products/Kanlux-APO-TM-remote-control.svg",'obj'=>'App\Presenters\KanluxApoTm3RemoveControl','bay'=>"http://shop.cz/products/AOP_TM3_remote_controller"),
				)
			),
			array(
				'id'=>2,'maker'=>"InterTechno",'image'=>"images/makers/interTechno.svg", 'products'=>array(
					array('id'=>0,'name'=>"InterTechno socket ITR-1500",'image'=>"images/products/InterTechno-ITR-1500.svg",'obj'=>'App\Presenters\InterTechnoSocket','bay'=>$webShop."/products/InterTechno_Socket-ITR-1500"),
					array('id'=>1,'name'=>"InterTechno remote control ITT-1500",'image'=>"images/products/InterTechno-ITT-1500.svg",'obj'=>'App\Presenters\InterTechnoRemoveControl','bay'=>$webShop."/products/InterTechno_Remote_Control-ITT-1500")
				)
			),
			array(
				'id'=>3,'maker'=>"EuroPatron",'image'=>"images/makers/EuroPatron.svg", 'products'=>array(
					array('id'=>0,'name'=>"EuroPtron panic button",'image'=>"images/products/EuroPatron_WS_JA.svg",'obj'=>'App\Presenters\EuroPatronPanicButton','bay'=>$webShop."/products/EuroPatronPanicButton"),
					array('id'=>1,'name'=>"EuroPtron magnetic contact",'image'=>"images/products/EuroPatron_WS_XWMC.svg",'obj'=>'App\Presenters\EuroPatronMagneticContact','bay'=>$webShop."/products/EuroPatronMagneticContact")
				)
			),
			array(
				'id'=>4,'maker'=>"Evolveo",'image'=>"images/makers/Evolveo.svg", 'products'=>array(
					array('id'=>0,'name'=>"Evolveo Wireles detector of opening ACS MST",'image'=>"images/products/Evolveo_ACS_MST.svg",'obj'=>'App\Presenters\EvolveoOpeningDetector','bay'=>$webShop."/products/EvolveoOpeningDetector")
				)
			)
		);
		
		$useDevicesJson = $this->cache->load('useDevicesJson');
		if ($useDevicesJson === NULL) {
			// nahrad obsah souboru s ulozenymi daty ve formatu json a prekopat ho na nasledujici pole
			$this->useDevices = array (
				// kod vyrobce, kod vyrobku, id domoticz, rfx kod, config array, component name (doplnene vzdy pri inicializaci pole)
			);
		}
		else {
			$this->useDevices = json_decode($useDevicesJson);
		}
		// pozor kdyz neco strcim do $this->template tak se to stava tridou a kyz je to pole tak to se rozpadne do podtrid (tj. uz to 
		// neni pole ale objekt se stenou strukturou a obsahem, ale s poejkterm se pracuje jinak nez s polem.
	}

	protected function save_useDevices(){
		$this->cache->save('useDevicesJson', json_encode($this->useDevices) );
	}

	public function getDeviceObj($makerId, $productId){
		Debugger::barDump($this->supportedDevices, 'supportedDevices');
		$r=$this->supportedDevices[$makerId]['products'][$productId]['obj'];
		//Debugger::barDump($r, 'ret');
		return $r;
	}

	public function createDeviceComponents()
	{
		$i=0;
		foreach($this->useDevices as $device)
		{
			$objStr=$this->supportedDevices[$device->makerId]['products'][$device->productId]['obj']; //vyzvednu nazev objektu komponenty
			$obj=new $objStr(); // vytvorim instanci komponenty
			$this->addComponent($obj,$device->componentName); // pripojim ji k presenteru s nejakym rozumnym nazvem, ktery jsem komponente dal uz pri vytvoreni device
			$obj->loadConfig(); // nacteni konfigurace a posledniho stavu z cache
			//Debugger::barDump($this->useDevices, 'useDevices');
			//Debugger::barDump($this->useDevices[$i]->componentName, 'deviceComponent');
			$i=$i+1;
		}
		//Debugger::barDump($this->useDevices, 'useDevices');
	}

	protected function getNextDeviceComponentId()
	{
		$max=0;
		foreach($this->useDevices as $device)
		{
			if ($device->componentId > $max) $max=$device->componentId;
		}
		return $max+1;
	}
	
	public function renderDefault()
	{
		//$this->initArrs();
		$this->template->anyVariable = 'any value';
		$this->template->json=json_encode ($this->supportedDevices);
		$this->template->json2=json_encode ($this->useDevices);
		$a=array();
		$i=0;
		foreach($this->useDevices as $key => $device){
			//Debugger::barDump($device, 'device');
			$maker=$this->supportedDevices[$device->makerId]['maker'];
			$maker_img=$this->supportedDevices[$device->makerId]['image'];
			$product=$this->supportedDevices[$device->makerId]['products'][$device->productId]['name'];
			$obj=$this->getDeviceObj($device->makerId, $device->productId);
			$product_img=$this->supportedDevices[$device->makerId]['products'][$device->productId]['image'];
			$product_bay=$this->supportedDevices[$device->makerId]['products'][$device->productId]['bay'];
			$objStr=$this->supportedDevices[$device->makerId]['products'][$device->productId]['obj'];
			//$compomentName=$this->template->supportedDevices[$device->makerId]['products'][$device->productId]['componentName'];
			$componentName=$device->componentName;
			//$obj=new $objStr();
			//$this->addComponent(new $objStr(),"$i");
			$a[]=array('key'=>$key, 'maker'=>$maker,'maker_img'=>$maker_img,'product'=>$product,'product_img'=>$product_img,'componentName'=>$componentName,'product_bay'=>$product_bay);
			$i=$i+1;
		}
		//Debugger::barDump($a, 'a');
		$this->template->devices=$a;
		//$this->netGetData();
		//$this->Ser2netReadSeting();
	}
	
	//--------------- select maker for new device ------
	protected function createComponentSelectMakerForm()
	{
		
		$this->initArrs();
		$makers=array();
		foreach ($this->supportedDevices as $key => $value)
		{
			$makers[]=$value['maker'];
		}
		$form = new UI\Form;
		$form->addSelect('makerId', 'Maker:', $makers);
		$form->addSubmit('select', 'Select');
		$form->onSuccess[] = array($this, 'setSelectMaker');
		return $form;
	}

	//vlola se po uspesne odeslani formulare
	public function setSelectMaker(UI\Form $form, $values)
	{	
		$this->template->makerId=$values->makerId;
		Debugger::barDump($this->template->makerId, '$this->template->makerId');
		//Debugger::barDump($conf, 'conf');
		$this->redirect('Homepage:SelectProductForm',array("makerId" => $values->makerId));
	}	
	
	//jen definice pro router, aby nasel cestu a priradil si sablonu showNewCard.late v ni je pak vlozena komponenta newCardForm ktera vytvori a vozi formular definovany vyse
	public function renderSelectMakerForm()
	{
	}

	//--------------- select product for new device ------
	protected function createComponentSelectProducterForm()
	{
		$this->initArrs();
		if (isset($this->params['makerId'])) $makerId=$this->params['makerId']; // po presmeroveni
		else $makerId=$this->request->post['makerId']; // pred validaci formulare
		//Debugger::barDump($this->template->makerId, '$this->template->makerId');
		Debugger::barDump($this, 'this');
		Debugger::barDump($this->getview(), 'view');
		//$this->setview("selectProductForm");
		$products=array();
		foreach ($this->supportedDevices as $key => $value)
		{
			Debugger::barDump($value, 'this parmas $key');
			//if ($value['id']==$this->params['makerId']) {
			if ($value['id']==$makerId) {
				foreach ($value['products'] as $key1 => $value1)
				{
					Debugger::barDump($value1, "producs $key");
					$products[]=$value1['name'];
				}
			}
		}
		$form = new UI\Form;
		//$form->addHidden('makerId',$this->params['makerId']);
		$form->addHidden('makerId',$makerId);
		$form->addSelect('productId', 'Product:', $products);
		$form->addSubmit('select', 'Select');
		$form->onSuccess[] = array($this, 'setSelectDevice');
		return $form;
	}

	//vlola se po uspesne odeslani formulare
	public function setSelectDevice(UI\Form $form, $values)
	{	
		if ($this->getUser()->isLoggedIn()) {
			$this->template->makerId=$values->makerId;
			$this->template->productId=$values->productId;
			//Debugger::barDump($conf, 'conf');
			//$this->template->useDevices = array (
			//	// kod vyrobce, kod vyrobku, id domoticz, rfx kod
			//);
			$this->useDevices[]= array (
				'makerId'=>$values->makerId,
				'productId'=>$values->productId,
				'rfxCode'=>"", # nepouzivam zde ale v konfigu kazdeho zarizeni, nektere techto kodu maji i vice - zde smazat
				'domoticzId'=>"", # zatim jsem nepouzil, aktulane dohledavam domoticzId dle nazvu zarizeni ulezeneho v konfigu kazdeho zarizeni zvlast
				'componentId'=>$this->getNextDeviceComponentId(),
				'componentName'=>"deviceComponent_".$this->getNextDeviceComponentId() );
			$this->save_useDevices();
			$this->flashMessage('New device is sucesfully add.');
		}
		else $this->flashMessage('For this you must be login.');
		$this->redirect('Homepage:');
	}	
	
	//jen definice pro router, aby nasel cestu a priradil si sablonu showNewCard.late v ni je pak vlozena komponenta newCardForm ktera vytvori a vozi formular definovany vyse
	public function renderSelectProductForm()
	{

	}

	//---------------------------
	// dotaz na potvrzeni smazani
	//---------------------------
	public function renderDeleteProductQuery($id)
	{
		//jen definice pro router , aby nasel cestu a priradil si sablonu dle nazvu funkce
		$device=$this->useDevices[$id];
		$maker=$this->supportedDevices[$device->makerId]['maker'];
		$maker_img=$this->supportedDevices[$device->makerId]['image'];
		$product=$this->supportedDevices[$device->makerId]['products'][$device->productId]['name'];
		$obj=$this->getDeviceObj($device->makerId, $device->productId);
		$product_img=$this->supportedDevices[$device->makerId]['products'][$device->productId]['image'];
		$objStr=$this->supportedDevices[$device->makerId]['products'][$device->productId]['obj'];
		$componentName=$device->componentName;
		$component=$this->getComponent($componentName);
		if ( isset($component->config->name) )
		{
			$name=$component->config->name;
		}
		else
		{
			$name="";
		}
		$this->template->device=array('maker'=>$maker,'maker_img'=>$maker_img,'product'=>$product,'product_img'=>$product_img,'componentName'=>$componentName, 'name'=>$name);
		$this->template->id=$id;
		Debugger::barDump($this->template, 'delete query template');
	}



	//--------------------------
	// smazazeni vybrane polozky
	//--------------------------
	public function renderDeleteProduct($id) // vhodnejsi by bylo pouzitu handle, ale takto to taky funguje
	{
		if ($this->getUser()->isLoggedIn()) {
			// smazat ulozeni configurace z cache
			$name=$this->useDevices[$id]->componentName;
			Debugger::barDump($name, 'name');
			$component=$this->getComponent($name);
			Debugger::barDump($component, 'component');
			unset($component->config);
			$component->saveConfig();
			// smazat z tabulky devices
			unset($this->useDevices[$id]);
			$this->useDevices=array_values($this->useDevices);
			$this->save_useDevices();
			$this->flashMessage('Device was deleted.');
		}
		else $this->flashMessage('For this you must be login.');
		$this->redirect('Homepage:');
	}


	//---------------------------------
	// obsluha pozadavku na rfx refresh
	//---------------------------------
	public function renderRefreshRfx()
	{
		// url pro obsluhu pak vypda: http://localhost/rfx433Mhz/homepage/refresh-rfx
		//  tuto chvili uz vsechny komponenety existuji (vytvarim je na zacatku presenteru), tj. staci je projit a zkusit jest-li boudou reagovat na rfx code
		foreach ($this->getComponents(FALSE) as $component) {
			$component->refreshRFX();
		}
		$this->redirect('Homepage:');
	}	


	//---------------------
	// obsluha rfxpozadavku
	//---------------------
	public function renderServeRfx($code)
	{
		// url pro obsluhu pak vypda: http://localhost/rfx433Mhz/homepage/serve-rfx?code=10001101101
		//  tuto chvili uz vsechny komponenety existuji (vytvarim je na zacatku presenteru), tj. staci je projit a zkusit jest-li boudou reagovat na rfx code
		foreach ($this->getComponents(FALSE) as $component) {
			$component->serveRFXsignal($code);
		}
		//$this->redirect('Homepage:');
	}	

}

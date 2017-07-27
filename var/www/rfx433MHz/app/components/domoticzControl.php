<?php
namespace App\Presenters;
//namespace App\Model;
//namespace App\Components;

use Nette;
use App\Model;

use Nette\Application\UI;
use Tracy\Debugger;



//class KanluxApoTm3RemoveControl extends UI\Control
//class domoticzControl extends UI\Control
class domoticzControl extends deviceControl
{

	//------------------
	// Domoticz function
	//------------------
	public function handleAddDomoticz()
	{
		// sablona odchyceni signalu - je poreba prebit svou verzi
	}

	//find last device from Domoticz by name
	protected function findDeviceByName($name)
	{
		$jsonData = file_get_contents('http://localhost:8080/json.htm?type=devices&filter=all&used=true');
		if ($jsonData === NULL) {
			$this->flashMessage('Can\'t contact Domoticz.');
			return NULL;
		}
		else $data = json_decode($jsonData);
		Debugger::barDump($data, 'data');
		if (isset($data->result)==false) return NULL;
		$idx=FALSE;
		foreach( $data->result as $item) // zarizeni stejneho jmne muze byt vice vratim to posledni, takze to projdu cele
		{
			if ($item->Name==$name) $idx=$item->idx;
		}
		return $idx;
	}
	
	//get obj all parameters of device
	protected function getAllParametersOfDevice($idx)
	{
		$jsonData = file_get_contents('http://localhost:8080/json.htm?type=devices&filter=all&used=true');
		if ($jsonData === NULL) {
			$this->flashMessage('Can\'t contact Domoticz.');
			return NULL;
		}
		else $data = json_decode($jsonData);
		foreach( $data->result as $item) // zarizeni stejneho jmne muze byt vice vratim to posledni, takze to projdu cele
		{
			if ($item->idx==$idx) return $item;
		}
		return FALSE;
	}
	
	//add script to switch device
	protected function setSwitchScripts($idx,$scriptOn="",$scriptOff="")
	{
		$param=$this->getAllParametersOfDevice($idx);
		$StrParam1=base64_encode($scriptOn);
		$StrParam2=base64_encode($scriptOff);
		
		$jsonData = file_get_contents(
			'http://localhost:8080/json.htm?type=setused'.
			'&idx='.$idx.
			'&name='.urlencode($param->Name).
			'&strparam1='.$StrParam1.
			'&strparam2='.$StrParam2.
			'&protected='.$param->Protected.
			'&used=true');
		
	}

	//set domoticz switch status [battery level 255 = no battery device, else 0-100]
	protected function setStatusSwitch($idx,$set,$battery="")
	{
		if ($set==TRUE) $set='On';
		else $set='Off';
		$url='http://localhost:8080/json.htm?type=command&param=switchlight'.
			'&idx='.$idx.
			'&switchcmd='.$set;
		if ($battery!="") $utl=$url."&battery=$battery";
		$jsonData = file_get_contents($url);
	}
	

	// cerate new Dmoticz device
	protected function createNewDeviceSwitch($id_HW,$name)
	{
		//$gett="http://localhost:8080/json.htm?type=createvirtualsensor&idx=".$id_HW."&sensorname=".urlencode($name)."&sensortype=6";
		//Debugger::barDump($gett, 'gett');
		return file_get_contents("http://localhost:8080/json.htm?type=createvirtualsensor&idx=".$id_HW."&sensorname=".urlencode($name)."&sensortype=6");
	}
	
	// get domoticz hardware id
	protected function getDomoticzHardwareId()
	{
		$id_HW=$this->findDomoticzDummyHardware();
		if ($id_HW=== NULL) return NULL;
		if ($id_HW=== FALSE) // pokud nebylo dummy HW nalezeno pokus se ho vytvorit
		{
			if ($this->createDomoticzDummyHardware()==NULL) return NULL;
			$id_HW=$this->findDomoticzDummyHardware();
			if ($id_HW === NULL) return NULL;
		}
		Debugger::barDump($id_HW, 'id_HW');
		return $id_HW;
	}
	
	// tray find ID of dumy hardware from domoticz system
	protected function findDomoticzDummyHardware()
	{
		$jsonData = file_get_contents('http://localhost:8080/json.htm?type=hardware');
		if ($jsonData === NULL) {
			$this->flashMessage('Can\'t contact Domoticz.');
			return NULL;
		}
		else $data = json_decode($jsonData);
		//Debugger::barDump($data, 'data');
		foreach( $data->result as $item)
		{
			if ($item->Type==15) return $item->idx;
		}
		return FALSE; //Dummy hardware not exist need create new Dummy hardware
	}

	// create new Dummy Hardware for Domoticz
	// if any proble then return NULL
	protected function createDomoticzDummyHardware()
	{
		$ret = file_get_contents('/json.htm?type=command&param=addhardware&htype=15&port=1&name=dummy&enabled=true');
		if ($ret=== NULL ) $this->flashMessage('Can\'t contact Domoticz.');
		return $ret;
		
	}


}


<?php

namespace App\Presenters;

use Nette;
use App\Model;
use Nette\Application\UI;
use App\Model\ServiciesManager;
use Tracy\Debugger;

class HomepagePresenter extends BasePresenter
{
	private $serviciesManager;
	
	public function renderDefault()
	{
		$this->serviciesManager=new ServiciesManager();
		$this->serviciesManager->refereshStatus();
		$this->template->services=$this->serviciesManager->services;
		Debugger::barDump($this->template->services, 'tem');
		$this->template->emptny=true;
		foreach($this->template->services as $service) if  ($service['status']==true) $this->template->emptny=false;
		$this->template->anyVariable = 'any value';
	}

}

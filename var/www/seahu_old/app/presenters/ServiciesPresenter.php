<?php

namespace App\Presenters;

use Nette;
use App\Model;
use  Nette\Application\UI;
use Nette\Application\UI\Form;
use App\Model\ServiciesManager;
use Tracy\Debugger;


class ServiciesPresenter extends BasePresenter
{
	private $serviciesManager;
	
	public function __construct()
	{
		$this->serviciesManager=new ServiciesManager();
	}
	
	public function renderDefault()
	{
		$this->serviciesManager->refereshStatus();
		$this->template->services=$this->serviciesManager->services;
		Debugger::barDump($this->template->services, 'tem');
	}

	public function renderEnableServicie($id)
	{
		if (!$this->getUser()->isLoggedIn()) {
			$this->error('For edit you must be login.');
		}
		$this->serviciesManager->enableService($id);
		$this->redirect('Servicies:');
	}

	public function renderDisableServicie($id)
	{
		if (!$this->getUser()->isLoggedIn()) {
			$this->error('For edit you must be login.');
			//$this->redirect('Wifi:'); //pri vyrvareni component neni dostupne presmerovani
		}
		$this->serviciesManager->disableService($id);
		$this->redirect('Servicies:');
	}

}


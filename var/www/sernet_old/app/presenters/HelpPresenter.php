<?php

namespace App\Presenters;

use Nette;
use App\Model;
use Nette\Application\UI;
use App\Model\ServiciesManager;
use Tracy\Debugger;

class HelpPresenter extends BasePresenter
{
	private $serviciesManager;
	
	public function renderDefault()
	{
		//$this->template->anyVariable = 'any value';
	}

}
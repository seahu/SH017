<?php

namespace App\Presenters;

use Nette;
use App\Model;
use  Nette\Application\UI;
use Nette\Application\UI\Form;
use Tracy\Debugger;


class TerminalPresenter extends BasePresenter
{
	
	public function renderDefault()
	{
		$this->template->ip = $_SERVER['SERVER_ADDR'];
	}


}


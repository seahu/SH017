<?php

namespace App\Presenters;

use Nette;
use App\Model;
use  Nette\Application\UI;
use Nette\Application\UI\Form;
use Tracy\Debugger;


class PowerPresenter extends BasePresenter
{

	public function renderDefault()
	{}

	public function renderQueryShutDownAccept()
	{}

	public function renderQueryResetAccept()
	{}
		
	public function renderShutDown()		
	{
		if (!$this->getUser()->isLoggedIn()) {
			$this->error('For edit you must be login.');
			$this->redirect('Wifi:');
		}
		shell_exec ("/usr/bin/sudo /sbin/shutdown -h now");
		$this->flashMessage('Shut down.');
		$this->redirect('HomePage:');
	}

	public function renderReset()		
	{
		if (!$this->getUser()->isLoggedIn()) {
			$this->error('For edit you must be login.');
			$this->redirect('Wifi:');
		}
		shell_exec ("/usr/bin/sudo /sbin/shutdown -r now");
		$this->flashMessage('Reset.');
		$this->redirect('HomePage:');
	}

}


<?php

namespace App\Presenters;

use Nette;
use App\Model;
use  Nette\Application\UI;
use Nette\Application\UI\Form;
use Tracy\Debugger;


class NetPresenter extends BasePresenter
{

	public function renderDefault()
	{
		$this->template->anyVariable = 'any value';
		$this->netGetActualSeting();
	}
	
	// editace sitoveho nastaveni
	protected function createComponentUpdateNetForm($cardId)
	{
		$status = array(
			'0' => 'No',
			'1' => 'Yes',
		);
		$form = new UI\Form;
		$form->addRadioList('dhcp', 'DHCP:', $status);
		$form->addText('static_ip', 'IP:');
		$form->addText('static_netmask', 'Netmask:');
		$form->addText('static_gateway', 'gateway:');
		$form->addText('static_dns', 'Primary DNS server:');
		
		$form->addSubmit('insert', 'edit');
		$form->onSuccess[] = array($this, 'updateNet');
		
		// set defaults
		$this->netGetActualSeting();
		$form->setDefaults(array(
			'dhcp' => $this->template->dhcp,
			'static_ip' => $this->template->static_ip,
			'static_netmask' => $this->template->static_netmask,
			'static_gateway' => $this->template->static_gateway,
			'static_dns' => $this->template->static_dns,
		));
		return $form;
	}
	
	//vlola se po uspesne odeslani formulare
	public function updateNet(UI\Form $form, $values)
	{
	    if (!$this->getUser()->isLoggedIn()) {
		$this->error('For edit you must be login.');
	    }
		$arg =" -I \"".$values->dhcp."\"";
		$arg.=" -i \"".$values->static_ip."\"";
		$arg.=" -n \"".$values->static_netmask."\"";
		$arg.=" -g \"".$values->static_gateway."\"";
		$arg.=" -d \"".$values->static_dns."\"";
		$cmd="/usr/bin/sudo /opt/seahu/setNetSeting.sh $arg";
		Debugger::barDump($cmd, "seahu_command_line");
		$output=shell_exec ("$cmd");
		$this->flashMessage('Sucessfuly update net setting:');
		//$this->redirect('Homepage:');
		$this->redirect('Net:');
        }

	public function renderUpdateNetForm($noID)
	{
		//jen definice pro router, aby nasel cestu a priradil si sablonu showNewCard.late v ni je pak vlozena komponenta newCardForm ktera vytvori a vozi formular definovany vyse
	}

	protected function netGetActualSeting()
	{
		// prepare emptny seting
		$this->template->ip = '';
		$this->template->netmask= '';
		$this->template->gateway = '';
		$this->template->dns = '';
		$this->template->dhcp = 0;
		// read setting
		// need edit /etc/sudoers to enable apache (www-data user) run script as superuser whithou press password
		// add line:
		//www-data  ALL=NOPASSWD: /etc/network/pi/getNetSeting.sh
		//run scan bash program generate output with seting report
		$output=shell_exec ("/usr/bin/sudo /opt/seahu/getNetSeting.sh");
		$lines=explode("\n", $output );
		// greb variables from lines etc line: mac:dc:a6:32:e3:5c:c6  to $this->template->mac="dc:a6:32:e3:5c:c6"
		foreach ( array("mac","actual_ip","actual_netmask","actual_gateway","actual_dns","static_ip","static_netmask","static_gateway","static_dns","dhcp") as $item) {
		    foreach($lines as $line){
			if ( substr_compare("$item:",$line,0, strlen("$item:"))==0 ) $this->template->$item = substr($line,strlen("$item:"));
		    }
		}
		Debugger::barDump($this->template, "template");

	}
}

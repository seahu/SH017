<?php

namespace App\Presenters;

use Nette;
use App\Model;
use  Nette\Application\UI;
use Nette\Application\UI\Form;
use Tracy\Debugger;

//Debugger::barDump($this->config, 'config');

class OpenvpnPresenter extends BasePresenter
{
	public function renderDefault()
	{
		$this->template->anyVariable = 'any value';
		//$this->netGetData();
		$this->openvpnGetActualSeting();
		Debugger::barDump($this->template, 'template');
	}
	
	public  function renderDisableOpenvpn()
	{
		$output=shell_exec ("/usr/bin/sudo /opt/seahu/services/service_openvpn.sh stop"); 
		$this->redirect('Openvpn:');
	}
	
	// Edit Openvpn
	//----------------------

	protected function createComponentUpdateOpenvpnForm($id)
	{
		if (!$this->getUser()->isLoggedIn()) {
			$this->error('For edit you must be login.');
			//$this->redirect('Wifi:'); //pri vyrvareni component neni dostupne presmerovani
		}
		// zjisteni aktualnich hodnot

		$this->openvpnReadSeting();
		
		
		$form = new UI\Form;
		$form->addCheckbox('enable', 'Enable OpenVPN');
		$form->addTextArea('conf', 'Config:')->setAttribute('rows', 15)->setAttribute('cols', 50);
		$form->addText('username', 'Yser name (auth.txt):');
		$form->addText('passwd', 'Password (auth.txt):');
		$form->addUpload('file_ca','Certificate (ca.crt) file');

		$form->addSubmit('insert', 'edit');
		$form->onSuccess[] = array($this, 'updateOpenvpn');
		
		// set defaults
		$form->setDefaults(array(
			'enable' => $this->template->enable,
			'conf' => $this->template->conf,
			'username' => $this->template->username,
			'passwd' => $this->template->passwd,
		));
		return $form;
	}
	
	//vola se po uspesne odeslani formulare
	public function updateOpenvpn(UI\Form $form, $values)
	{	
		if (!$this->getUser()->isLoggedIn()) {
			$this->error('For edit you must be login.');
		}
		file_put_contents('/etc/openvpn/openvpn.conf', $values->conf);
		Debugger::barDump($this->template, "OpenVPN update");
		$context = $values->username."\n";
		$context.= $values->passwd."\n";
		file_put_contents('/etc/openvpn/auth.txt', $context);
		if ( $values->file_ca->isOk() ) {
			$values->file_ca->move('/etc/openvpn/ca.crt');
		}
		if ( $values->enable==TRUE) {
			shell_exec ("/usr/bin/sudo /opt/seahu/services/service_openvpn.sh enable >/dev/null 2>/dev/null &");
			$this->flashMessage('Starting OpenVPN');
			$this->flashMessage('Sucessfuly update OpenVPN setting:');
			$this->redirect('Openvpn:wait');
		}
		else {
			shell_exec ("/usr/bin/sudo /opt/seahu/services/service_openvpn.sh disable >/dev/null 2>/dev/null &");
			$this->flashMessage('Stoping OpenVPN');
			$this->flashMessage('Sucessfuly update OpenVPN setting:');
			$this->redirect('Openvpn:');
		}
		//$this->redirect('Homepage:');
	}
	
	public function renderUpdateOpenvpnForm($noID)
	{
		//jen definice pro router, aby nasel cestu a priradil si sablonu showNewCard.late v ni je pak vlozena komponenta newCardForm ktera vytvori a vozi formular definovany vyse
	}

	
	/*
	 * fuction for view actual status and base config (properties)
	 */
	protected function openvpnGetActualSeting()
	{
		// need edit /etc/sudoers to enable apache (www-data user) run script as superuser whithou press password
		// add line:
		//www-data  ALL=NOPASSWD: /etc/network/pi/getOpenvpnSeting.sh
		//run scan bash program generate output with seting report
		$output=shell_exec ("/usr/bin/sudo /opt/seahu/services/service_openvpn.sh status");
		$lines=explode("\n", $output );
		if ($lines[0]=="OK") $this->template->enable = TRUE;
		else $this->template->enable = FALSE;
		// read setting
		// need edit /etc/sudoers to enable apache (www-data user) run script as superuser whithou press password
		// add line:
		//www-data  ALL=NOPASSWD: /etc/network/pi/getNetSeting.sh
		//run scan bash program generate output with seting report
		$output=shell_exec ("/usr/bin/sudo /opt/seahu/getOpenvpnSeting.sh");
		$lines=explode("\n", $output );
		// greb variables from lines etc line: mac:dc:a6:32:e3:5c:c6  to $this->template->mac="dc:a6:32:e3:5c:c6"
		foreach ( array("tun_ip1","tun_ip2","tun_netmask") as $item) {
		    foreach($lines as $line){
			if ( substr_compare("$item:",$line,0, strlen("$item:"))==0 ) $this->template->$item = substr($line,strlen("$item:"));
		    }
		}
		//Debugger::barDump($this->template, "template");
	}

	/*
	 * function for get values for update conigure
	 */
	protected function openvpnReadSeting()
	{
		// prepare emptny seting
		$this->template->username = '';
		$this->template->passwd = '';
		$this->template->ca = '';
		$this->template->conf = "";

		
		// read setting from openvpn config file
		$lines = file('/etc/openvpn/openvpn.conf');
		foreach($lines as $line) $this->template->conf.=$line;
		foreach ($lines as $line_num => $line) {
			$words=explode(" ", trim($line) );
			if ($words[0]=="login")  $this->template->username = $words[1];
			if ($words[0]=="passwd")  $this->template->passwd = $words[1];
		}
		
		// read username and password from auth.txt file
		$lines = file('/etc/openvpn/auth.txt');
		$this->template->username = $lines[0];
		$this->template->passwd = $lines[1];
		
		// get status
		$output=shell_exec ("/usr/bin/sudo /opt/seahu/services/service_openvpn.sh status");
		$lines=explode("\n", $output );
		if ($lines[0]=="OK") $this->template->enable = TRUE;
		else $this->template->enable = FALSE;
		
		Debugger::barDump($this->template, 'inerface seting');
	}

}


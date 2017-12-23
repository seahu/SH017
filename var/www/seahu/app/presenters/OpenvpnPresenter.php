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
	//priprava rozklikavacich dat
	private $proto = array(
			'0' => '',
			'1' => 'udp',
			'2' => 'tcp',
		);
	private	$dev = array(
			'0' => '',
			'1' => 'tun',
			'2' => 'tab',
		);
		
	public function renderDefault()
	{
		$this->template->anyVariable = 'any value';
		//$this->netGetData();
		$this->openvpnGetActualSeting();
		Debugger::barDump($this->template, 'template');
	}
	
	public  function renderDisableOpenvpn()
	{
		$output=shell_exec ("/usr/bin/sudo /etc/init.d/opevpn stop"); 
		//$output=shell_exec ("/usr/bin/sudo /opt/seahu/restartWifi.sh"); // pridat zruseni automatickeho startu po nabehu pocitace
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
		$form->addText('remote', 'Remote:');
		$form->addText('port', 'Port:');
		$form->addSelect('proto', 'Protocol type:', $this->proto);
		$form->addSelect('dev', 'Dev type:', $this->dev);
		$form->addText('mtu','Max MTU');
	
		$form->addText('username', 'Yser name:');
		$form->addText('passwd', 'Password:');
		$form->addCheckbox('lzo', 'Comp-lzo');
		$form->addUpload('file_ca','Certificate CA file');
		$form->addSubmit('insert', 'edit');
		$form->onSuccess[] = array($this, 'updateOpenvpn');
		
		// set defaults
		$form->setDefaults(array(
			'remote' => $this->template->remote,
			'port' => $this->template->port,
			'proto' => $this->searchKey($this->proto, $this->template->proto),
			'dev' => $this->searchKey($this->dev, $this->template->dev),
			'username' => $this->template->username,
			'passwd' => $this->template->passwd,
			'mtu' => $this->template->mtu,
			'lzo' => $this->template->lzo,
			'enable' => $this->template->enable,
		));
		return $form;
	}
	
	//vola se po uspesne odeslani formulare
	public function updateOpenvpn(UI\Form $form, $values)
	{	
		if (!$this->getUser()->isLoggedIn()) {
			$this->error('For edit you must be login.');
		}
		$context ="remote ".$values->remote."\n";
		$context.="client\n";
		if ($values->port!="") 		$context.="port ".$values->port."\n";
		if ($values->proto!="") 	$context.="proto ".$this->proto[$values->proto]."\n";
		if ($values->dev!="") 		$context.="dev ".$this->dev[$values->dev]."\n";
		if ($values->mtu!="")		$context.="link-mtu ".$values->mtu."\n";
		if ($values->lzo==TRUE)		$context.="comp-lzo yes\n";
		$context.="ca ca.crt\n";
		$context.="auth-user-pass auth.txt\n";
		file_put_contents('/etc/openvpn/openvpn.conf', $context);
		Debugger::barDump($this->template, "OpenVPN update");
		$context = $values->username."\n";
		$context.= $values->passwd."\n";
		file_put_contents('/etc/openvpn/auth.txt', $context);
		Debugger::barDump($values->lzo, 'lzo');
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
		if ($lines[1]=="OK") $this->template->enable = TRUE;
		else $this->template->enable = FALSE;
		$output=shell_exec ("/usr/bin/sudo /opt/seahu/getOpenvpnSeting.sh");
		$lines=explode("\n", $output );
		$this->template->ip1 = $lines[0];
		$this->template->ip2 = $lines[1];
		$this->template->netmask = $lines[2];
	}

	/*
	 * function for get values for update conigure
	 */
	protected function openvpnReadSeting()
	{
		// prepare emptny seting
		$this->template->remote = '';
		$this->template->port = '';
		$this->template->proto = '';
		$this->template->dev = '';
		$this->template->username = '';
		$this->template->passwd = '';
		$this->template->ca = '';
		$this->template->mtu = '';
		$this->template->lzo = '';

		// read setting from openvpn config file
		$lines = file('/etc/openvpn/openvpn.conf');
		foreach ($lines as $line_num => $line) {
			$words=explode(" ", trim($line) );
			if ($words[0]=="remote")  $this->template->remote = $words[1];
			if ($words[0]=="port")  $this->template->port = $words[1];
			if ($words[0]=="proto")  $this->template->proto = $words[1];
			if ($words[0]=="dev")  $this->template->dev = $words[1];
			if ($words[0]=="login")  $this->template->login = $words[1];
			if ($words[0]=="passwd")  $this->template->passwd = $words[1];
			if ($words[0]=="ca")  $this->template->ca = $words[1];
			if ($words[0]=="link-mtu")  $this->template->mtu = $words[1];
			if ($words[0]=="comp-lzo")  $this->template->lzo = $words[1];
		}
			
		// read username and password from auth.txt file
		$lines = file('/etc/openvpn/auth.txt');
		$this->template->username = $lines[0];
		$this->template->passwd = $lines[1];
		
		// get status
		$output=shell_exec ("/usr/bin/sudo /opt/seahu/services/service_openvpn.sh status");
		$lines=explode("\n", $output );
		if ($lines[1]=="OK") $this->template->enable = TRUE;
		else $this->template->enable = FALSE;
		
		Debugger::barDump($this->template, 'inerface seting');
	}

	protected function searchKey($array, $val)
	{
		$key=array_search($val, $array);
		if ($key==FALSE) $key=0;
		return $key;
	}

}


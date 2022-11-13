<?php

namespace App\Presenters;

use Nette;
use App\Model;
use  Nette\Application\UI;
use Nette\Application\UI\Form;
use Tracy\Debugger;

//Debugger::barDump($this->config, 'config');
Debugger::barDump("A", 'config');

class WifiPresenter extends BasePresenter
{

	public function renderDefault()
	{
		$this->template->anyVariable = 'any value';
		//$this->netGetData();
		$this->wifiGetActualSeting();
		//Debugger::barDump($this->template, 'template');
	}
	
	public  function renderDisableWiFi()
	{
		$cmd="/usr/bin/sudo /opt/seahu/setWifiSeting.sh -W 0";
		Debugger::barDump($cmd, "seahu_command_line");
		$output=shell_exec ("$cmd");
		$this->flashMessage('Sucessfuly disable WiFi setting:');
		//$this->redirect('Homepage:');
		$this->redirect('Wifi:');
	}
	
	// Edit Wi-Fi as CLIENT
	//----------------------
	//vyber dospupnych siti
	protected function createComponentUpdateWifiForm($id)
	{
		if (!$this->getUser()->isLoggedIn()) {
			$this->error('For edit you must be login.');
			//$this->redirect('Wifi:'); //pri vyrvareni component neni dostupne presmerovani
		}
		// zjisteni aktualnich hodnot
		//$this->wifiReadInterfaceSeting(array(),"CLIENT");
		$this->wifiGetActualSeting();
		// nejdrive zjistim seznam dostupnych siti
		// need edit /etc/sudoers to enable apache (www-data user) run script as superuser whithou press password
		// add line:
		//www-data  ALL=NOPASSWD: /opt/seahu/scanWifi.sh
		//run scan bash program generate sids list
		$output=shell_exec ("/usr/bin/sudo /opt/seahu/scanWifi.sh");
		$lines=explode("\n", $output );
		$sids=array();
		$sidsPrint=array();
		$y=false;
		foreach($lines as $line) {
			$words=explode(";", $line );
			if (count($words)!=7) break;
			$mac=$words[0];
			$sid=$words[1];
			$gHz=$words[2];
			$chanel=$words[3];
			$noise=$words[4];
			$signal=$words[5];
			$encrypt=$words[6];
			if ($encrypt=="off") $encrypt="(free)";
			if ($encrypt=="on") $encrypt="(passwd)"; 
			$sids[$sid]="$sid [$signal] $encrypt";
			if ($sid==$this->template->client_sid) $y=true;
		}
		if ($y==false) {
			$sids[$this->template->client_sid]=$this->template->client_sid." (unaviable)";
		}
		//$this->template->xx = $aa;
		// countries
		$countries = array(
			'CZ' => 'Ceská Republika',
			'SK' => 'Slovensko',
			'GB' => 'Velká Británie',
		);
		//pak to ostatni
		$status1 = array(
			'0' => 'Off',
			'1' => 'On',
		);
		$status2 = array(
			'0' => 'Disable',
			'1' => 'Enable',
		);
		$form = new UI\Form;
		$form->addSelect('client_country', 'Country:', $countries);
		$form->addRadioList('client_sid', 'SID:', $sids);
		$form->addText('client_psk', 'Wifi password:');
		$form->addRadioList('dhcp', 'DHCP:', $status1);
		$form->addText('static_ip', 'Static IP:');
		$form->addText('static_netmask', 'Static Netmask:');
		$form->addText('static_gateway', 'Static Gateway:');
		$form->addText('static_dns', 'Static Primary DNS server:');
		
		$form->addSubmit('insert', 'edit');
		$form->onSuccess[] = array($this, 'updateWifi');

		Debugger::barDump($this->template, "thist->template");
		
		// set defaults
		if (array_key_exists($this->template->client_country, $countries)==false ) $this->template->client_country=array_key_first($countries);
		$form->setDefaults(array(
			'client_country' => $this->template->client_country,
			'client_sid' => $this->template->client_sid,
			'client_psk' => $this->template->client_psk,
			'dhcp' => $this->template->dhcp,
			'static_ip' => $this->template->static_ip,
			'static_netmask' => $this->template->static_netmask,
			'static_gateway' => $this->template->static_gateway,
			'static_dns' => $this->template->static_dns
		));
		return $form;
	}
	
	//vola se po uspesne odeslani formulare
	public function updateWifi(UI\Form $form, $values)
	{	
		if (!$this->getUser()->isLoggedIn()) {
			$this->error('For edit you must be login.');
		}
		$arg= " -W 1";
		$arg.=" -A CLIENT";
		$arg.=" -k \"".$values->client_country."\"";
		$arg.=" -s \"".$values->client_sid."\"";
		$arg.=" -p \"".$values->client_psk."\"";
		$arg.=" -I \"".$values->dhcp."\"";
		$arg.=" -i \"".$values->static_ip."\"";
		$arg.=" -n \"".$values->static_netmask."\"";
		$arg.=" -g \"".$values->static_gateway."\"";
		$arg.=" -d \"".$values->static_dns."\"";
		$cmd="/usr/bin/sudo /opt/seahu/setWifiSeting.sh $arg";
		Debugger::barDump($cmd, "seahu_command_line");
		$output=shell_exec ("$cmd");
		$this->flashMessage('Sucessfuly update WiFi setting:');
		//$this->redirect('Homepage:');
		$this->redirect('Wifi:');
	}
	

	// Edit Wi-Fi as AP
	//----------------------
	//
	protected function createComponentUpdateWifiApForm($id)
	{
		if (!$this->getUser()->isLoggedIn()) {
			$this->error('For edit you must be login.');
			//$this->redirect('Wifi:'); //pri vyrvareni component neni dostupne presmerovani
		}
		// zjisteni aktualnich hodnot
		$this->wifiGetActualSeting();
		//$this->wifiReadInterfaceSeting(array(),"AP");

		// countries
		$countries = array(
			'CZ' => 'Ceská Republika',
			'SK' => 'Slovensko',
			'GB' => 'Velká Británie',
		);
		//pak to ostatni
		$status1 = array(
			'0' => 'Off',
			'1' => 'On',
		);
		$status2 = array(
			'0' => 'Disable',
			'1' => 'Enable',
		);
		$form = new UI\Form;
		$form->addSelect('ap_country', 'Country:', $countries);
		$form->addText('ap_sid', 'SSID:');
		$form->addText('ap_psk', 'Wifi password:');
		$form->addText('ap_channel', 'Channel (1-13):');
		$form->addText('static_ip', 'IP:');
		$form->addText('static_netmask', 'Netmask:');
		$form->addText('dhcpd_pool_ip1', 'DHCP server range from IP:');
		$form->addText('dhcpd_pool_ip2', 'DHCP server range to IP:');
		$form->addRadioList('forward', 'Forward packets:', $status2);
		$form->addRadioList('nat', 'NAT:', $status2);
		
		$form->addSubmit('insert', 'edit');
		$form->onSuccess[] = array($this, 'updateWifiAp');
		
		// set defaults
		if (array_key_exists($this->template->ap_country, $countries)==false ) $this->template->ap_country=array_key_first($countries);
		$form->setDefaults(array(
			'ap_country' => $this->template->ap_country,
			'ap_sid' => $this->template->ap_sid,
			'ap_psk' => $this->template->ap_psk,
			'ap_channel' => $this->template->ap_channel,
			'static_ip' => $this->template->static_ip,
			'static_netmask' => $this->template->static_netmask,
			'dhcpd_pool_ip1' => $this->template->dhcpd_pool_ip1,
			'dhcpd_pool_ip2' => $this->template->dhcpd_pool_ip2,
			'forward' => $this->template->forward,
			'nat' => $this->template->nat,
		));
		Debugger::barDump($this->template);
		return $form;
	}
	
	//vlola se po uspesne odeslani formulare
	public function updateWifiAp(UI\Form $form, $values)
	{	
		if (!$this->getUser()->isLoggedIn()) {
			$this->error('For edit you must be login.');
		}
		$arg= " -W 1";
		$arg.=" -A AP";
		$arg.=" -D 1";  // enable own dhcp server
		$arg.=" -I 0"; // set static IP (no dhcp)
		$arg.=" -K \"".$values->ap_country."\"";
		$arg.=" -S \"".$values->ap_sid."\"";
		$arg.=" -P \"".$values->ap_psk."\"";
		$arg.=" -c \"".$values->ap_channel."\"";
		$arg.=" -i \"".$values->static_ip."\"";
		$arg.=" -n \"".$values->static_netmask."\"";
		$arg.=" -a \"".$values->dhcpd_pool_ip1."\"";
		$arg.=" -b \"".$values->dhcpd_pool_ip2."\"";

		$cmd="/usr/bin/sudo /opt/seahu/setWifiSeting.sh $arg";
		Debugger::barDump($cmd, "seahu_command_line");
		$output=shell_exec ("$cmd");
		$this->flashMessage('Sucessfuly update WiFi setting:');
		//$this->redirect('Homepage:');
		$this->redirect('Wifi:');
	}
	
	public function renderUpdateNetForm($noID)
	{
		//jen definice pro router, aby nasel cestu a priradil si sablonu showNewCard.late v ni je pak vlozena komponenta newCardForm ktera vytvori a vozi formular definovany vyse
	}

	
	protected function wifiGetActualSeting()
	{
		// need edit /etc/sudoers to enable apache (www-data user) run script as superuser whithou press password
		// add line:
		//www-data  ALL=NOPASSWD: /etc/network/pi/getWifiSeting.sh
		//run scan bash program generate output with seting report
		$output=shell_exec ("/usr/bin/sudo /opt/seahu/getWifiSeting.sh");
		$lines=explode("\n", $output );
		// greb variables from lines etc line: mac:dc:a6:32:e3:5c:c6  to $this->template->mac="dc:a6:32:e3:5c:c6"
		foreach ( array("enable_wlan","wifi_mode","mac","actual_ip","actual_netmask","actual_gateway","actual_dns","static_ip","static_netmask","static_gateway","static_dns","dhcp","ap_country","ap_sid","ap_psk","ap_channel","client_country","client_sid","client_psk","client_signal","client_connect", "forward","enable_dhcpd","dhcpd_pool_ip1","dhcpd_pool_ip2","nat") as $item) {
		    foreach($lines as $line){
			if ( substr_compare("$item:",$line,0, strlen("$item:"))==0 ) $this->template->$item = substr($line,strlen("$item:"));
		    }
		}
		//$this->template->mac = $lines[0];
		//$this->template->ip = $lines[1];
		//$this->template->netmask = $lines[2];
		//$this->template->gateway = $lines[3];
		//$this->template->dns = $lines[4];
		//if ($lines[5]=="dhcp") $this->template->dhcp = 1;
		//else  $this->template->dhcp = 0;
		//$this->template->sid = $lines[6];
		//$this->template->psk = $lines[7];
		//$this->template->type = $lines[8];
		//$this->template->channel = $lines[9];
		//$this->template->forward = $lines[10];
		//if ($lines[11]=="#wlan0_is_enable") $this->template->enable = 1;
		//else $this->template->enable = 0;
		//$this->template->dhcpd_wlan0 = $lines[12];
		//$this->template->dhcpd_range_IP1 = $lines[13];
		//$this->template->dhcpd_range_IP2 = $lines[14];
		//$this->template->nat = $lines[15];
		//$this->template->state = $lines[16];
		
		
		//$this->template->xx = $aa;
	}

	protected function removeQuote($string)
	{
		if ( substr($string,0,1)=="\"" ) $string=substr($string,1);
		if ( substr($string,-1,1)=="\"" ) $string=substr($string,0,-1);
		return $string;
	}
}

<?php

namespace App\Presenters;

use Nette;
use App\Model;
use  Nette\Application\UI;
use Nette\Application\UI\Form;
use Tracy\Debugger;

//Debugger::barDump($this->config, 'config');

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
		$output=shell_exec ("/usr/bin/sudo /sbin/ifdown wlan0"); // I must iterface down with same seting as it was start
		$this->template->enable = 0;
		$this->template->dhcpd_wlan0 = 0;
		$this->wifiWriteInterfaceSeting(); // update wifi seting
		$output=shell_exec ("/usr/bin/sudo /opt/seahu/restartWifi.sh");
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
		$this->wifiReadInterfaceSeting(array(),"CLIENT");
		// nejdrive zjistim seznam dostupnych siti
		// need edit /etc/sudoers to enable apache (www-data user) run script as superuser whithou press password
		// add line:
		//www-data  ALL=NOPASSWD: /etc/network/pi/scanWifi.sh
		//run scan bash program generate sids list
		$output=shell_exec ("/usr/bin/sudo /opt/seahu/scanWifi.sh");
		$lines=explode("\n", $output );
		$ssids=array();
		$ssidsPrint=array();
		$y=false;
		foreach($lines as $line) {
			$words=explode(";", $line );
			if (count($words)!=7) break;
			$mac=$words[0];
			$ssid=$words[1];
			$gHz=$words[2];
			$chanel=$words[3];
			$noise=$words[4];
			$signal=$words[5];
			$encrypt=$words[6];
			if ($encrypt=="off") $encrypt="(free)";
			if ($encrypt=="on") $encrypt="(passwd)"; 
			$ssids[$ssid]="$ssid [$signal] $encrypt";
			if ($ssid==$this->template->ssid) $y=true;
		}
		if ($y==false) {
			$ssids[$this->template->ssid]=$this->template->ssid." (unaviable)";
		}
		//$this->template->xx = $aa;

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
		$form->addRadioList('ssid', 'SSID:', $ssids);
		$form->addText('psk', 'Wifi password:');		
		$form->addRadioList('dhcp', 'DHCP:', $status1);
		$form->addText('ip', 'IP:');
		$form->addText('netmask', 'Netmask:');
		$form->addText('gateway', 'gateway:');
		$form->addText('dns', 'Primary DNS server:');
		
		$form->addSubmit('insert', 'edit');
		$form->onSuccess[] = array($this, 'updateWifi');
		
		// set defaults
		$form->setDefaults(array(
			'ssid' => $this->template->ssid,
			'psk' => $this->template->psk,
			'dhcp' => $this->template->dhcp,
			'ip' => $this->template->ip,
			'netmask' => $this->template->netmask,
			'gateway' => $this->template->gateway,
			'dns' => $this->template->dns
		));
		return $form;
	}
	
	//vola se po uspesne odeslani formulare
	public function updateWifi(UI\Form $form, $values)
	{	
		if (!$this->getUser()->isLoggedIn()) {
			$this->error('For edit you must be login.');
		}
		// get all items of actual setting
		$this->wifiGetActualSeting();
		$output=shell_exec ("/usr/bin/sudo /sbin/ifdown wlan0"); // I must iterface down with same seting as it was start
		$this->wifiReadInterfaceSeting(array(),"CLIENT");
		$this->template->enable = 1;
		// change values from form
		$this->template->type = "CLIENT";
		$this->template->ssid=$values->ssid;
		$this->template->psk=$values->psk;		
		$this->template->ip=$values->ip;
		$this->template->netmask=$values->netmask;
		$this->template->gateway=$values->gateway;
		$this->template->dns=$values->dns;
		$this->template->dhcp=$values->dhcp;
		$this->template->dhcpd_wlan0 = 0; // in client mode always disable dhcpd for wifi
		$this->template->forward = 0; // in client mode always disable forward for wifi
		$this->template->nat = 0; // in client mode always disable nat for wifi
		Debugger::barDump($this->template, "CLIENT update");
		$this->wifiWriteInterfaceSeting(); // update wifi seting
		$output=shell_exec ("/usr/bin/sudo /opt/seahu/restartWifi.sh");
		$this->flashMessage('Sucessfuly update WiFi setting:');
		//$this->redirect('Homepage:');
		$this->redirect('Wifi:');
	}
	

	// Edit Wi-Fi as AP
	//----------------------
	//vyber dospupnych siti
	protected function createComponentUpdateWifiApForm($id)
	{
		if (!$this->getUser()->isLoggedIn()) {
			$this->error('For edit you must be login.');
			//$this->redirect('Wifi:'); //pri vyrvareni component neni dostupne presmerovani
		}
		// zjisteni aktualnich hodnot
		$this->wifiGetActualSeting();
		$this->wifiReadInterfaceSeting(array(),"AP");

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
		$form->addText('ssid', 'SSID:');
		$form->addText('psk', 'Wifi password:');
		$form->addText('channel', 'Channel (1-13):');
		$form->addText('ip', 'IP:');
		$form->addText('netmask', 'Netmask:');
		$form->addText('dhcpd_range_IP1', 'DHCP server range from IP:');
		$form->addText('dhcpd_range_IP2', 'DHCP server range to IP:');
		$form->addRadioList('forward', 'Forward packets:', $status2);
		$form->addRadioList('nat', 'NAT:', $status2);
		
		$form->addSubmit('insert', 'edit');
		$form->onSuccess[] = array($this, 'updateWifiAp');
		
		// set defaults
		$form->setDefaults(array(
			'ssid' => $this->template->ssid,
			'psk' => $this->template->psk,
			'channel' => $this->template->channel,
			'ip' => $this->template->ip,
			'netmask' => $this->template->netmask,
			'dhcpd_range_IP1' => $this->template->dhcpd_range_IP1,
			'dhcpd_range_IP2' => $this->template->dhcpd_range_IP2,
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
		// get all items of actual setting
		$this->wifiGetActualSeting();
		$output=shell_exec ("/usr/bin/sudo /sbin/ifdown wlan0"); // I must iterface down with same seting as it was start
		$this->wifiReadInterfaceSeting(array(),"AP");
		$this->template->enable = 1;
		// change values from form
		$this->template->ssid=$values->ssid;
		$this->template->psk=$values->psk;
		$this->template->channel=$values->channel;
		$this->template->ip=$values->ip;
		$this->template->netmask=$values->netmask;
		$this->template->dhcpd_range_IP1=$values->dhcpd_range_IP1;
		$this->template->dhcpd_range_IP2=$values->dhcpd_range_IP2;
		$this->template->forward=$values->forward;
		$this->template->nat=$values->nat;
		// static seting fro AP mode and set run dhcpd
		$this->template->type="AP";
		$this->template->dhcp = 0;
		$this->template->dhcpd_wlan0 = 1;
		// save new setting
		Debugger::barDump($this->template, 'template');
		$this->wifiWriteInterfaceSeting(); // update wifi seting
		$output=shell_exec ("/usr/bin/sudo /opt/seahu/restartWifi.sh");
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
		$this->template->mac = $lines[0];
		$this->template->ip = $lines[1];
		$this->template->netmask = $lines[2];
		$this->template->gateway = $lines[3];
		$this->template->dns = $lines[4];
		if ($lines[5]=="dhcp") $this->template->dhcp = 1;
		else  $this->template->dhcp = 0;
		$this->template->sid = $lines[6];
		$this->template->psk = $lines[7];
		$this->template->type = $lines[8];
		$this->template->channel = $lines[9];
		$this->template->forward = $lines[10];
		if ($lines[11]=="#wlan0_is_enable") $this->template->enable = 1;
		else $this->template->enable = 0;
		$this->template->dhcpd_wlan0 = $lines[12];
		$this->template->dhcpd_range_IP1 = $lines[13];
		$this->template->dhcpd_range_IP2 = $lines[14];
		$this->template->nat = $lines[15];
		$this->template->state = $lines[16];
		
		
		//$this->template->xx = $aa;
	}

	protected function wifiReadInterfaceSeting($ssids=array(), $type="")
	{
		// prepare emptny seting
		$this->template->enable = 0;
		$this->template->ssid = '';
		$this->template->ssidId = '';
		$this->template->psk = '';
		$this->template->ip = '';
		$this->template->netmask= '';
		$this->template->gateway = '';
		$this->template->dns = '';
		$this->template->dhcp = 0;
		$this->template->type = "CLIENT";
		$this->template->forward = 0;
		$this->template->nat = 0;
		$this->template->channel = 0;

		// read setting from interface file
		$lines = file('/etc/network/interfaces.d/wlan0');
		foreach ($lines as $line_num => $line) {
			$words=explode(" ", trim($line) );
			if ($words[0]=="#wlan0_is_enable")  $this->template->enable = 1;
			if ($words[0]=="address")  $this->template->ip = $words[1];
			if ($words[0]=="netmask")  $this->template->netmask = $words[1];
			if ($words[0]=="gateway")  $this->template->gateway = $words[1];
			if ($words[0]=="dns-nameservers")  $this->template->dns = $words[1];
			if ($words[0]=="iface")  {
				if ($words[3]=="dhcp") $this->template->dhcp = 1;
				else 	$this->template->dhcp = 0;			
			}
			if ($words[0]=="hostapd")  $this->template->type = "AP";
		}
		if ( $type=="") $type=$this->template->type; // if not specify who type I need read, than read actual type (AP or CLIENT)
		if ( $type == "CLIENT" ) {
			// Wi-Fi as CLIENT 
			// read setting from /etc/wpa_supplicant/wpa_supplicant.conf
			$lines = file('/etc/wpa_supplicant/wpa_supplicant.conf');
			foreach ($lines as $line_num => $line) {
				$words=explode("=", trim($line) );
				if ($words[0]=="ssid")  $this->template->ssid = $this->removeQuote($words[1]);
				if ($words[0]=="psk")   $this->template->psk  = $this->removeQuote($words[1]);
			}
			// vybrani stejne polozky (pri nastavovani defoltni honoty formulare je potreba zadat poradove cislo a ne hodnotu)
			foreach($ssids as $ssid) {
				if ( $ssid == $this->template->ssid ) $this->template->ssidID = $ssid;
			}
		}
		else {
			// Wi-Fi as AP
			$lines = file('/etc/hostapd/hostapd.conf');
			foreach ($lines as $line_num => $line) {
				$words=explode("=", trim($line) );
				if ($words[0]=="ssid")  $this->template->ssid = $words[1];
				if ($words[0]=="wpa_passphrase")   $this->template->psk  = $words[1];
				if ($words[0]=="channel") $this->template->channel = $words[1];
			}
		}
		// get actual forward setting from file /etc/sysctl.conf
		$lines = file('/etc/sysctl.conf');
		foreach ($lines as $line_num => $line) {
			if ($line=="net.ipv4.ip_forward=1\n")  $this->template->forward = 1;
		}
		
		// get actual firewall setting from /etc/seahu/firewall.sh
		$lines = file('/etc/seahu/firewall.sh');
		foreach ($lines as $line_num => $line) {
			if ($line=="/sbin/iptables --table nat --append POSTROUTING --out-interface eth0 -j MASQUERADE\n")  $this->template->nat = 1;
		}
		Debugger::barDump($this->template, 'inerface seting');

	}

	protected function wifiWriteInterfaceSeting()
	{
		// write new config to interface file
		if ($this->template->enable == 0) {
			$context="#wlan0_is_disable\n";
			file_put_contents('/etc/network/interfaces.d/wlan0', $context);
			file_put_contents('/etc/dnsmasq.d/dnsmasq-wlan0.conf', $context);
			$this->template->forward = 0;
			$this->template->nat = 0;
		}
		else {
			$context="#wlan0_is_enable\n";
			$context.="allow-hotplug wlan0\n";
			if ($this->template->dhcp == 0) {
				$context.="iface wlan0 inet static\n";
				if ($this->template->ip != "" ) $context.="	address ". $this->template->ip ."\n";
				if ($this->template->netmask != "" ) $context.="	netmask ". $this->template->netmask ."\n";
				if ($this->template->gateway != "" ) $context.="	gateway ". $this->template->gateway ."\n";
				if ($this->template->dns != "") $context.="	dns-nameservers ". $this->template->dns ."\n";
			}
			else {
				$context.="iface wlan0 inet dhcp\n";
			}

			if ($this->template->type == "CLIENT") {
				// Wi-Fi as CLIENT 
				$context.="	wpa-conf /etc/wpa_supplicant/wpa_supplicant.conf\n";
				file_put_contents('/etc/network/interfaces.d/wlan0', $context);

				$context="country=GB\n";
				$context.="ctrl_interface=DIR=/var/run/wpa_supplicant GROUP=netdev\n";
				$context.="update_config=1\n";
				$context.="network={\n";
				$context.="	ssid=\"". $this->template->ssid ."\"\n";
				if ($this->template->psk=="") {
					$context.="	key_mgmt=NONE\n";
				}
				else {
					$context.="	psk=\"". $this->template->psk ."\"\n";
				}
				$context.="}\n";
				file_put_contents('/etc/wpa_supplicant/wpa_supplicant.conf', $context);
			}
			else {
				// Wi-Fi as AP
				$context.="	hostapd /etc/hostapd/hostapd.conf\n";
				file_put_contents('/etc/network/interfaces.d/wlan0', $context);
				
				$context="interface=wlan0\n";
				$context.="driver=nl80211\n";
				$context.="ssid=". $this->template->ssid ."\n";
				$context.="hw_mode=g\n";
				$context.="channel=". $this->template->channel ."\n";
				$context.="macaddr_acl=0\n";
				$context.="ignore_broadcast_ssid=0\n";
				$context.="auth_algs=1\n";
				$context.="wpa=3\n";
				$context.="wpa_passphrase=". $this->template->psk ."\n";
				$context.="wpa_key_mgmt=WPA-PSK\n";
				$context.="wpa_pairwise=TKIP\n";
				$context.="rsn_pairwise=CCMP\n";
				file_put_contents('/etc/hostapd/hostapd.conf', $context);
			}

			#save dhcpd config
			$context="interface=wlan0\n";
			if ($this->template->dhcpd_wlan0==0) $context.="no-dhcp-interface=wlan0\n";
			$context.="dhcp-range=".$this->template->dhcpd_range_IP1.",".$this->template->dhcpd_range_IP2.",12h\n";
			file_put_contents('/etc/dnsmasq.d/dnsmasq-wlan0.conf', $context);
		}

		#save forward config
		if ($this->template->forward==1){
			$output=shell_exec ("/usr/bin/sudo /sbin/sysctl -w net.ipv4.ip_forward=1");
			$output=shell_exec ("/usr/bin/sudo /bin/sed -i 's/#net.ipv4.ip_forward=1/net.ipv4.ip_forward=1/g' /etc/sysctl.conf");
		}
		else {
			$output=shell_exec ("/usr/bin/sudo /sbin/sysctl -w net.ipv4.ip_forward=0");
			$output=shell_exec ("/usr/bin/sudo /bin/sed -i 's/^net.ipv4.ip_forward=1/#net.ipv4.ip_forward=1/g' /etc/sysctl.conf");
		}
		
		#save firewal config
		$context="#!/bin/bash\n\n";
		$context.="# set SEAHU firewall\n";
		$context.="/sbin/iptables --flush\n";
		$context.="/sbin/iptables --delete-chain\n";
		$context.="/sbin/iptables --table nat --flush\n";
		$context.="/sbin/iptables --table nat --delete-chain\n";
		if ($this->template->nat==1) {
			$context.="/sbin/iptables --table nat --append POSTROUTING ! --out-interface wlan0 -j MASQUERADE\n";
			$context.="/sbin/iptables --append FORWARD --in-interface wlan0 -j ACCEPT\n";
		}
		file_put_contents('/etc/seahu/firewall.sh', $context);
	}
	
	protected function removeQuote($string)
	{
		if ( substr($string,0,1)=="\"" ) $string=substr($string,1);
		if ( substr($string,-1,1)=="\"" ) $string=substr($string,0,-1);
		return $string;
	}
}

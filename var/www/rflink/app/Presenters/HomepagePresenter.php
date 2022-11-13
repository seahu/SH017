<?php

namespace App\Presenters;

use Nette;
use App\Model;
use  Nette\Application\UI;
use Nette\Application\UI\Form;
use Tracy\Debugger;

class HomepagePresenter extends BasePresenter
{



	public function renderDefault()
	{
		$status = $this->status();
		$this->template->status = $status;
		$this->template->config = $this->config();
		$this->template->log = $this->get_log();
	}

	protected function status()
	{
		$cmd="/usr/bin/sudo /opt/seahu/services/service_rflink.sh status";
		$output=shell_exec ($cmd);
		Debugger::barDump($cmd, 'cmd');
		Debugger::barDump($output, 'cmd output');
		$lines=explode("\n", $output );
		if (count($lines)<2) return false;
		$last_line=$lines[count($lines)-2];
		Debugger::barDump($cmd, 'status cmd');
		Debugger::barDump($output, 'status output');
		Debugger::barDump($lines, 'status lines');
		Debugger::barDump($last_line, 'status last line');
		if ($last_line=="OK") return true;
		else return false; 
	}
	
	protected function config()
	{
		$config_file="/etc/rflink.conf";
		$this->template->config_file=$config_file;
		if (file_exists ( $config_file )==true) {
			$lines = file($config_file, FILE_IGNORE_NEW_LINES);
			return $lines;
		}
		else {
			return array();
		}
	}
	
	protected function get_log()
	{
		$output=shell_exec ("/usr/bin/sudo /bin/grep 'rflink.sh:' /var/log/syslog | /usr/bin/tail -n 20");
		Debugger::barDump($output, 'output');
		$lines=explode("\n", $output );
		$arr=array();
		foreach($lines as $line){
			$arr[]=substr($line,39);
		}
		return $arr;
		//return $lines;
		//return "ahoj";
	}
	
}

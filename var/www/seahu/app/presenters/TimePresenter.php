<?php

namespace App\Presenters;

use Nette;
use App\Model;
use  Nette\Application\UI;
use Nette\Application\UI\Form;


class TimePresenter extends BasePresenter
{

	public function renderDefault()
	{
		$this->template->time = time();
		$this->template->timeZone= file_get_contents('/opt/seahu/actualTimeZone.txt'); 
	}
	
	// editace sitoveho nastaveni
	protected function createComponentUpdateTimeForm($cardId)
	{
		$timeZones = array();
		$files = scandir("/usr/share/zoneinfo");
		foreach($files as $file){
			if ($file==".") continue;
			if ($file=="..") continue;
			if ($file=="zone.tab") continue;
			if ($file=="iso3166.tab") continue;
			if ($file=="leap-seconds.list") continue;
			// zkonroluj zda-li file neni adresar
			if( is_file("/usr/share/zoneinfo/".$file) ) $timeZones[$file]=$file;
			else {
				$files2 = scandir("/usr/share/zoneinfo/".$file);
				$subZones=array();
				foreach($files2 as $file2){
					if ($file2==".") continue;
					if ($file2=="..") continue;
					if( is_file("/usr/share/zoneinfo/".$file."/".$file2) ) $subZones[$file."/".$file2]=$file2;
				}
				$timeZones[$file]=$subZones;
			}
		}
		$form = new UI\Form;
		$form->addText('min', 'Min.:')
			->addRule(Form::INTEGER, 'Must be number')
    		->addRule(Form::RANGE, 'From 0 to 60', [0, 60]);
		$form->addText('hour', 'Hour.:')
			->addRule(Form::INTEGER, 'Must be number')
    		->addRule(Form::RANGE, 'From 0 to 24', [0, 24]);
		$form->addText('day', 'Day:')
			->addRule(Form::INTEGER, 'Must be number')
    		->addRule(Form::RANGE, 'From 1 to 31', [1, 31]);
		$form->addText('month', 'Month:')
			->addRule(Form::INTEGER, 'Must be number')
    		->addRule(Form::RANGE, 'From 1 to 12', [1, 12]);
		$form->addText('year', 'Year:')
			->addRule(Form::INTEGER, 'Must be number')
    		->addRule(Form::RANGE, 'From 0 to 31', [2000, 3000]);
		//$form->addRadioList('timeZone', 'Time zone:', $timeZones);
		$form->addSelect('timeZone', 'Time zone:', $timeZones);
		$form->addSubmit('send', 'Edit');
		$form->onSuccess[] = array($this, 'formSucceeded');
		
		// Get actual data
		$time=time();
		$min=date("i",$time);
		$hour=date("G",$time);
		$day=date("j",$time);
		$month=date("n",$time);
		$year=date("Y",$time);
		// set defaults
		$form->setDefaults(array(
			'min' => date("i",$time),
			'hour' => date("G",$time),
			'day' => date("j",$time),
			'month' => date("n",$time),
			'year' => date("Y",$time),
			'timeZone' => file_get_contents('/opt/seahu/actualTimeZone.txt')
		));
		return $form;
	}
	
	//vlola se po uspesne odeslani formulare
	public function formSucceeded(UI\Form $form, $values)
	{	
		// set system time
		$min=$values->min;
		$hour=$values->hour;
		$day=$values->day;
		$month=$values->month;
		$year=$values->year;
		$output=shell_exec ("/usr/bin/sudo /opt/seahu/setSystemTime.sh \"$min\" \"$hour\" \"$day\" \"$month\" \"$year\"");
		$content = file_get_contents('/usr/share/zoneinfo/'.$values->timeZone);
		file_put_contents('/etc/localtime', $content);
		file_put_contents('/opt/seahu/actualTimeZone.txt', $values->timeZone);
		$this->flashMessage('Sucessfuly update net setting:');
		//$this->redirect('Homepage:');
		$this->redirect('Time:');
	}
	
	public function renderUpdateTimeForm($noID)
	{
		//jen definice pro router, aby nasel cestu a priradil si sablonu showNewCard.late v ni je pak vlozena komponenta newCardForm ktera vytvori a vozi formular definovany vyse
	}

}

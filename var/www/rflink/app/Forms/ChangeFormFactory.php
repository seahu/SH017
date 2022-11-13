<?php

namespace App\Forms;

use Nette;
use Nette\Application\UI\Form;
use Nette\Security\User;
use Tracy\Debugger;


//class ChangeFormFactory extends Nette\Object
class ChangeFormFactory
{
	/** @var FormFactory */
	private $factory;

	/** @var User */
	private $user;


	public function __construct(FormFactory $factory, User $user)
	{
		$this->factory = $factory;
		$this->user = $user;
	}


	/**
	 * @return Form
	 */
	public function create()
	{
		$form = $this->factory->create();
		$form->addPassword('password', 'Current Password:')
			->setRequired('Please enter actual password.');
		$form->addPassword('newPassword', 'New Password:')
			->setRequired('Please enter new password.');
		$form->addPassword('retype', 'Retype new Password:')
			->setRequired('Please retype new password.');
		$form->addSubmit('send', 'Change');
		$form->onSuccess[] = array($this, 'formSucceeded');
		return $form;
	}

	public function formSucceeded(Form $form, $values)
	{
		if ($values->newPassword!=$values->retype) {
			$form->addError('New password and retype password not equal.');
			return;
		} 
		$output=shell_exec ("/usr/bin/sudo /opt/seahu/changePasswd.sh \"pi\" \"".$values->password."\" \"".$values->newPassword."\"");
		//$output=shell_exec ("/usr/bin/sudo /opt/seahu/changePasswd.sh \"pi\" \"".$values->password."\"");
		//Debugger::dump($output);
		Debugger::barDump($output, 'output');
		if ($output[0]=="1") $form->addError('The actual password is incorect.');
	}

}

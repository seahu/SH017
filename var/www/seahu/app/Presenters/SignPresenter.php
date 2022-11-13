<?php

namespace App\Presenters;

use Nette;
use App\Forms\SignFormFactory;
use App\Forms\ChangeFormFactory;
use Nette\Application\UI\Form;

class SignPresenter extends BasePresenter
{
	/** @var SignFormFactory @inject */
	public $factory;

	/** @var ChangeFormFactory @inject */	
	public $factory1;


	/**
	 * Sign-in form factory.
	 * @return Nette\Application\UI\Form
	 */
	protected function createComponentSignInForm()
	{
		$form = $this->factory->create();
		$form->onSuccess[] = function ($form) {
			$form->getPresenter()->flashMessage('Sucessfully login.');
			$form->getPresenter()->redirect('Homepage:');
		};
		return $form;
	}
	/*
	protected function createComponentSignInForm()
	{
	    $form = new Form;
	    $form->addText("username", "uzivatelske jmeno:");
	    $form->addText("password", "heslo:");
	    $form->addSubmit('send','prihlasit se');
	    $form->onSuccess[]= [$this, 'signInFormSuccess'];
	    return $form;
	}
	*/

	public function  signInFormSuccess( $form, $data)
	{
	    try {
		$this->getUser()->login($data->username, $data->password);
		$this->redirect('Homepage:');
	    } catch (Nette\Security\AuthenticationException $e) {
		$form->addError('Nesprávné přihlašovací jméno nebo heslo.');
	    }
	}

	/**
	 * Change password form factory.
	 * @return Nette\Application\UI\Form
	 */
	protected function createComponentChangeForm()
	{
		$form = $this->factory1->create();
		$form->onSuccess[] = function ($form) {
			$form->getPresenter()->flashMessage('Password sucessfully changed.');
			$form->getPresenter()->redirect('Homepage:');
		};
		return $form;
	}


	public function actionOut()
	{
		$section = $this->getSession('mySection'); // returns SessionSection with given name
		unset($section->agree);
		$this->getUser()->logout();
		$this->flashMessage('Sucessfully logout.');
		$this->redirect('Homepage:');
	}

}

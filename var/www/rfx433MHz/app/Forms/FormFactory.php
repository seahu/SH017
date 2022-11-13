<?php

namespace App\Forms;

use Nette;
use Nette\Application\UI\Form;


//class FormFactory extends Nette\Object
class FormFactory
{

	/**
	 * @return Form
	 */
	public function create()
	{
		return new Form;
	}

}

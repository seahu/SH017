<?php

namespace App\Presenters;

use Nette;
use App\Model;


/**
 * Base presenter for all application presenters.
 */
abstract class BasePresenter extends Nette\Application\UI\Presenter
{
    protected function startup(): void {
        parent::startup();
        $this->template->this_server_ip=$_SERVER['SERVER_ADDR'];
    }

}


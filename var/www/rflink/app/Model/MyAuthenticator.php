<?php

namespace App\Model;

use Nette;
use Nette\Security\SimpleIdentity;

class MyAuthenticator implements Nette\Security\Authenticator
{

    public function __construct( )
    {
    }

    /*
     * Performs an authentication.^M
     * @return Nette\Security\Identity^M
     * @throws Nette\Security\AuthenticationException^M
     */
    //public function authenticate(string $user, string $password): SimpleIdentity // for php > 7.3
    public function authenticate(string $user, string $password): Nette\Security\IIdentity // for php =< 7.3
    {
        /**
         * fro succesfull using pam for autorization need instal php5-auth-pam package, bat for rasbian not exist,
         *      therefore must be instaled by pecl. Good manual on https://www.raspberrypi.org/forums/viewtopic.php?f=36
         *      This done by next steps:
         *      sudo apt-get install libpam0g-dev
         *      sudo apt-get install php5-dev
         *      sudo apt-get install php-pear
         *      sudo pecl install PAM
         *      after that, the following line should be added to the appropriate php.ini:
         *      extension=pam.so
         *      or add thiscontex into file /etc/php5/mods-available/pam.ini and create simlink to /etc/php5/apache2/con
         *      use is simgle by next example:
         *      if(!pam_auth($username,$password,&$error)){
         *   echo 'No access, PAM said: '.$error;
         *      }
         * RESULT THIS IS NOT FUNCTION instead I use own bash script
        */
	$output=shell_exec ("/usr/bin/sudo /opt/seahu/checkPasswd.sh \"$user\" \"$password\"");
        if($output!="0"){
	    throw new Nette\Security\AuthenticationException('Invalid password.');
        }
	//return new SimpleIdentity($user); // for php > 7.3
	return new Nette\Security\Identity($user); // for php =< 7.3
    }
}

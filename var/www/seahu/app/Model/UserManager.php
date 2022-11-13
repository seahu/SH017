<?php

// !!!! deprected for new NETTE (do not use)!!!!

namespace App\Model;

use Nette;
use Nette\Security\Passwords;


/**
 * Users management.
 */
//class UserManager extends Nette\Object implements Nette\Security\IAuthenticator
class UserManager implements Nette\Security\IAuthenticator
{


	public function __construct()
	{
	}

	/**
	 * fro succesfull using pam for autorization need instal php5-auth-pam package, bat for rasbian not exist,
	 *	therefore must be instaled by pecl. Good manual on https://www.raspberrypi.org/forums/viewtopic.php?f=36&t=10992
	 *	This done by next steps:
	 *	sudo apt-get install libpam0g-dev
	 *	sudo apt-get install php5-dev
	 *	sudo apt-get install php-pear
	 *	sudo pecl install PAM
	 *	after that, the following line should be added to the appropriate php.ini:
	 *	extension=pam.so
	 *	or add thiscontex into file /etc/php5/mods-available/pam.ini and create simlink to /etc/php5/apache2/conf.d 
	 *	use is simgle by next example:
	 *	if(!pam_auth($username,$password,&$error)){
	 *   echo 'No access, PAM said: '.$error;
	 *	}
	 * RESULT THIS IS NOT FUNCTION instead I use own bash script
	*/


	/**
	 * Performs an authentication.
	 * @return Nette\Security\Identity
	 * @throws Nette\Security\AuthenticationException
	 */
	public function authenticate(array $credentials)
	{
		list($username, $password) = $credentials;
		$output=shell_exec ("/usr/bin/sudo /opt/seahu/checkPasswd.sh \"$username\" \"$password\"");
	 	if($output!="0"){
	    throw new Nette\Security\AuthenticationException('Bad password', self::INVALID_CREDENTIAL); 
	 	}
		return new Nette\Security\Identity($username);
		//return new Nette\Security\Identity($row[self::COLUMN_ID], $row[self::COLUMN_ROLE], $arr);
	}

}




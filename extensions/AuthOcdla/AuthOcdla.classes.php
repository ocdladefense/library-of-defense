<?php

class AuthOcdla extends AuthPlugin
{
	function userExists($username)
	{
		ttail( 'Processing user', 'authplugin');
		return true;
	}
	
	// automatically authenticate the user
	function authenticate($username, $password)
	{
		ttail( 'Authenticating user', 'authplugin');
		ttail( 'foobar user', 'authplugin');
		ttail('username is:'.$username,'authplugin');
		ttail('password is: '.$password,'authplugin');
		return true;
	}
	
	/**
	 * Check to see if the specific domain is a valid domain.
	 *
	 * @param $domain String: authentication domain.
	 * @return bool
	 * @public
	 */
	function validDomain( $domain )
	{
			# Override this!
			return true;
	}
	
	function autoCreate()
	{
		ttail( 'Creating user', 'authplugin');
		return true;
	}

	function allowPasswordChange()
	{
		return true;
	}

	function canCreateAccounts()
	{
		return false;
	}

	function addUser($user, $password, $email = '', $realname = '')
	{
		ttail( 'adduser', 'authplugin');
		return false;
	}

	function strict()
	{
		ttail( 'strict', 'authplugin');
		return true;
	}
	
	
	/**
	 * When creating a user account, optionally fill in preferences and such.
	 * For instance, you might pull the email address or real name from the
	 * external user database.
	 *
	 * The User object is passed by reference so it can be modified; don't
	 * forget the & on your function declaration.
	 *
	 * @param $user User object.
	 * @param $autocreate Boolean: True if user is being autocreated on login
	 */
	public function initUser( &$user, $autocreate = false )
	{
		/*	ttail( 'Init user', 'authplugin');
					$user->setEmail('membernation@ocdla.com.ocdladev');
			$user->setRealName('Member Nation');
			$user->setPassword('somepassword');
				$user->saveSettings();
				*/
			// $user->addGroup('subscriber');

	}
	
	/**
	 * When a user logs in, optionally fill in preferences and such.
	 * For instance, you might pull the email address or real name from the
	 * external user database.
	 *
	 * The User object is passed by reference so it can be modified; don't
	 * forget the & on your function declaration.
	 *
	 * @param User $user
	 * @access public
	 */
	function updateUser( &$user ) {
				// $user->addGroup('subscriber');
		/*$user->setEmail('membernation@ocdla.com.ocdladev');
		$user->setRealName('Member Nation');
		$user->setPassword('somepassword');
		return true;
		*/
	}

}
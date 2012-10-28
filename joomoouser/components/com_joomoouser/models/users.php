<?php
/**
 * @author      Tom Hartung <webmaster@tomhartung.com>
 * @package     Joomla
 * @subpackage  joomoouser
 * @copyright   Copyright (C) 2010 Tom Hartung. All rights reserved.
 * @since       1.5
 * @license     GNU/GPL, see LICENSE.php
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

jimport('joomla.application.component.model');

/**
 * Model for interfacing with jos_users table - for use by joomoouser component
 *
 * NOTE that unlike other jomoo* models we do NOT inherit from JoomoobaseModelJoomoobaseDb
 * ---------------------------------------------------------------------------------------
 * This is because we use methods provided by joomla's JUser class.
 */
class UsersModelUsers extends JModel
{
	/**
	 * JUser object
	 * @access private
	 * @var object JUser object
	 */
	private $_user = '';
	/**
	 * JParameter object containing com_user parameters
	 * @access private
	 * @var object JParameter object
	 */
	private $_userConfig = '';

	/**
	 * Constructor
	 * @access public
	 */
	public function __construct()
	{
		parent::__construct();

		//	$this->_tableName = "#__users";
		$this->_user = & JFactory::getUser();
	}
	/**
	 * determines whether an account already exists for this user
	 * at this time joomla acts a bit ungraciously if some one tries to
	 *    register an account using an email address that's already in the DB
	 * @access public
	 * @return boolean TRUE if this is a duplicate email address else FALSE
	 */
	public function duplicateEmail()
	{
		$emailToCheck = JRequest::getVar( 'email', '', 'post' );
		$db =& $this->getDBO();
		$query = 'SELECT ' . $db->nameQuote('username') . ', ' . $db->nameQuote('email') . ' ' .$db->nameQuote('bind') . ' ' .
		            'FROM #__users ' .
		            'WHERE ' . $db->nameQuote('email') . ' = ' . $db->Quote($emailToCheck);
		$db->setQuery( $query );
		$duplicateData = $db->loadObject();
		//	print 'query = ' . $query . '<br />';
		//	print 'duplicateData:<br />';
		//	print_r ( $duplicateData );
		//	print '<br />';

		if ( $duplicateData == null )
		{
			$duplicateEmail = FALSE;
		}
		else
		{
			$duplicateEmail = TRUE;
			$message = 'The email address "' . $emailToCheck . '" is already registered to ' .
			             'a user named "' . $duplicateData->username . '".  ';
			$duplicateData->bind ?
				$message .= 'That account has been disabled.  Contact a site administrator for assistance.' :
				$message .= 'Please login using your existing account or specify a different email address.';

			$this->setError( $message );
		}

		return $duplicateEmail;
	}
	/**
	 * stores data for a new user
	 * this is more or less patterned after function register_save in
	 *    components/com_user/controller.php
	 * @access public
	 * @return boolean TRUE if successful else FALSE
	 */
	public function store()
	{
		$this->_userConfig = &JComponentHelper::getParams( 'com_users' );
		$useractivation = $this->_userConfig->get('useractivation');

		$data = JRequest::get( 'post' );

		// we don't want users to edit certain fields so we will unset them
		// (inspired by similar code in components/com_user/controller.php kthxbai)
		unset($data['gid']);
		unset($data['registerDate']);
		unset($data['activation']);

		$useractivation ? $data['usertype'] = 1 : $data['usertype'] = 0;

		$data['usertype'] = $this->_getNewUsertype();
		$data['gid'] = $this->_getNewUserGid($data['usertype']);
		$data['params'] = $this->_getParamsString();
		$data['sendEmail'] = 1;

		if ( ! $this->_user->bind($data) )
		{
			$message  = 'An error occurred running the bind() method in JUser: "';
			$message .= $this->_user->getError();
			$message .= '"  You may want to try again later.';
			$this->setError( $message );
			return FALSE;
		}

		if ( $this->_userConfig->get('useractivation') )    // derived from components/com_user/controller.php
		{
			jimport('joomla.user.helper');
			$this->_user->set('activation', JUtility::getHash( JUserHelper::genRandomPassword()) );
			$this->_user->set('block', '1');
		}

		if ( ! $this->_user->save() )
		{
			$message  = 'An error occurred running the save() method in JUser: "';
			$message .= $this->_user->getError();
			$message .= '"  You may want to try again later.';
			$this->setError( $message );
			return FALSE;
		}

		// $id = $this->_user->id;

		if ( ! $this->_updateCore($data,$this->_user->id) )
		{
			return FALSE;
		}

		if ( ! $this->_sendConfirmationEmail( ) )
		{
			$message  = 'An error occurred sending the email. "';
			$message .= $this->getError();
			$message .= '"  You may want to try again later.';
			$this->setError( $message );
			return FALSE;
		}

		return TRUE;
	}
	/**
	 * deactivates a user
	 * @access public
	 * @return boolean TRUE if successful else FALSE
	 */
	public function deactivate()
	{
		$app = JFactory::getApplication();

		$this->_user->block = 1;
		$updateOnly = TRUE;         // we do not create new user!!
		$savedOk = $this->_user->save( $updateOnly );

		if ( $savedOk )
		{
			$error = $app->logout();  // see components/com_user/controller.php for ideas as to what to do with $error
		}
		else
		{
			$id = $this->_user->id;
			$message = 'Unable to deactivate user ' . $id . ': ' . $this->_user->getError();
			$this->setError( $message );
		}

		return $savedOk;
	}
	/**
	 * deletes a user
	 * @access public
	 * @return boolean TRUE if successful else FALSE
	 */
	public function delete()
	{
		$app = JFactory::getApplication();
		$deletedOk = $this->_user->delete();

		if ( $deletedOk )
		{
			$error = $app->logout();  // see components/com_user/controller.php for ideas as to what to do with $error
		}
		else
		{
			$id = $this->_user->id;
			$message = 'Unable to delete user ' . $id . ': ' . $this->_user->getError();
			$this->setError( $message );
		}

		return $deletedOk;
	}
	//
	// private functions used mostly by store()
	//
	/**
	 * Get the default usertype for a new user - this code is similar to that in
	 *    administrator/components/com_users/views/user/view.html.php
	 * @access private
	 * @return string containing new user type (eg. 'Registered')
	 */
	private function _getNewUsertype()
	{
		$newGrp = $this->_userConfig->get( 'new_usertype' );

		if ( ! $newGrp )
		{
			$newGrp = 'Registered';
		}

		return $newGrp;
	}
	/**
	 * Get the default group id for a new user - this code is similar to that in
	 *    administrator/components/com_users/views/user/view.html.php
	 * @access private
	 * @return string containing param name/value pairs
	 */
	private function _getNewUserGid( $newGrp )
	{
		$acl =& JFactory::getACL();
		$gid =  $acl->get_group_id( $newGrp, null, 'ARO' );
		return $gid;
	}
	/**
	 * when adding a new user we use the default values set in assets/constants.php
	 * @access private
	 * @return string containing param name/value pairs
	 */
	private function _getParamsString()
	{
		$paramsString = 'admin_language=' . JOOMOOUSER_DEFAULT_ADMIN_LANGUAGE . "\n" .
			'language=' . JOOMOOUSER_DEFAULT_LANGUAGE . "\n" .
			'editor=' . JOOMOOUSER_DEFAULT_EDITOR . "\n" .
			'helpsite=' . JOOMOOUSER_DEFAULT_HELPSITE . "\n" .
			'timezone=' . JOOMOOUSER_DEFAULT_TIMEZONE . "\n" .
			'show_page_title=' . JOOMOOUSER_DEFAULT_SHOW_PAGE_TITLE;

		return $paramsString;
	}
	/**
	 * when adding a new user we must also update one of the core acl tables
	 * else it won't show up in the back end list of users and the user can't log in
	 * @access private
	 * @return boolean TRUE if successful else FALSE
	 */
	private function _updateCore( $data, $id=0 )
	{
		if ( is_numeric($id) && 0 < $id )
		{
			$objectToInsert = new stdClass();
			$objectToInsert->id = 0;
			$objectToInsert->section_value = 'users';
			$objectToInsert->value = $id;
			$objectToInsert->order_value = 0;
			$objectToInsert->name = $data['name'];
			$objectToInsert->hidden = 0;
			$db = $this->getDBO();
			$db->insertObject( '#__core_acl_aro', $objectToInsert );
		}
		else
		{
			$message  = 'An error occurred running the _updateCore() method in UsersModelUsers: ';
			$message .= 'id is not set (0), so apparently the user was not created OK?  ';
			$message .= 'You may want to try again later.';
			$this->setError( $message );
			return FALSE;
		}

		return TRUE;
	}
	/**
	 * sends activation email to newly registered user
	 * @access private 
	 * @return boolean return value from call to sendEmail() (True if successful else False)
	 * @note this function is a slimmed-down version of _sendMail in components/com_user/controller.php; specifically:
	 *     it does not send emails to super administrators and
	 *     it uses the joomoo interface to the joomla mailer (defined in joomoobase)
	 */
	private function _sendConfirmationEmail()
	{
		//	print "Hello from _sendConfirmationEmail!<br />\n";
		$app = JFactory::getApplication();
		$name     = $this->_user->get('name');
		$email    = $this->_user->get('email');
		$username = $this->_user->get('username');

		$sitename = $app->getCfg( 'sitename' );
		$siteURL  = JURI::base();

		$subject = JText::_('Account created at ' . $sitename . ' for ' . $name );
		$subject = html_entity_decode($subject, ENT_QUOTES);
		$message = JText::_( "Hello " . $name . ",\n\nThank you for registering at " . $sitename . "\n\n" );

		if ( $this->_userConfig->get('useractivation') )
		{
			$message .= JText::_( "We have created your account but you must activate it before you can use it.\n\n" .
			            "To activate the account click on the following link or copy and paste it in your browser:\n" .
			            $siteURL . "index.php?option=com_joomoouser&task=activate&activation=" . $this->_user->get('activation') . "\n\n" .
			            "After activating your account you may login to " . $siteURL . " as " . $username . ", " );
		}
		else
		{
			$message .= JText::_( "You may now log in to " . $siteURL . " as " . $username . ", " );
		}

		$message .= JText::_( "using the password you specified when you registered." );
		$message = html_entity_decode( $message, ENT_QUOTES );

		require_once 'components' .DS. 'com_joomoobase' .DS. 'utilities' .DS. 'JoomoobaseEmailer.php';
		$mailer = new JoomoobaseEmailer();

		// $mailer->headers = $headers;
		// $mailfrom = $app->getCfg( 'mailfrom' );
		// $mailer->sender = $mailfrom;
		$mailer->sender = 'do_not_reply@' . $sitename;
		$mailer->recipient = $email;
		$mailer->subject = $subject;
		$mailer->body = $message;

		//	$emailedOk = $mailer->sendEmailJMail( );
		$emailedOk = $mailer->sendEmailJUtility( );

		if ( $emailedOk != TRUE )
		{
			//	print 'sendEmailJUtility() failed: ' . $mailer->getError() . "<br />\n";
			$message = 'sendEmailJUtility() failed: ' . $mailer->getError();
			$this->setError( $message );
		}

		return $emailedOk;
	}
}
?>

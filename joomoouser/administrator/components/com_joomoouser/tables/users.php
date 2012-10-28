<?php
/**
 * @author      Tom Hartung <webmaster@tomhartung.com>
 * @package     Joomla
 * @subpackage  JoomooUser
 * @copyright   Copyright (C) 2011 Tom Hartung. All rights reserved.
 * @since       1.5
 * @license     GNU/GPL, see LICENSE.php
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

jimport('joomla.application.component.controller');

/**
 * Joomla class interface to #__users table
 * Defines column names and database methods for the joomoouser table
 */
class TableUsers extends JTable
{
	/**
	 * @var int Primary Key
	 */
	public $id = null;
	/**
	 * @var string full name of user
	 */
	public $name = null;
	/**
	 * @var string username of user (eg. tomh, admin, etc.)
	 */
	public $username = null;
	/**
	 * @var string email address of user
	 */
	public $email = null;
	/**
	 * @var string user's password
	 */
	public $password = null;
	/**
	 * @var string type of user (Registered, Super Admin, etc.)
	 */
	public $usertype = null;
	/**
	 * @var boolean whether to block user
	 */
	public $block = null;
	/**
	 * @var boolean whether to send user occasional email updates
	 */
	public $sendEmail = null;
	/**
	 * @var int group id of user
	 */
	public $gid = null;
	/**
	 * @var timestamp date registered
	 */
	public $registerDate = null;
	/**
	 * @var timestamp date of most recent visit
	 */
	public $lastvisitDate = null;
	/**
	 * @var activation key - I believe it popuates when user forgets their password
	 */
	public $activation = null;
	/**
	 * @var string parameter string for user
	 */
	public $params = null;

	/**
	 * Constructor
	 */
	public function __construct( &$db )
	{
		parent::__construct( '#__users', 'id', $db );

		// print "Hello from TableUsers::__construct()<br />\n";
	}

	/**
	 * Validator: ensure required values are set
	 * @return boolean True if values are valid else False
	 */
	public function check()
	{
		// print "Hello from TableUsers::check()<br />\n";

		return true;
	}
}

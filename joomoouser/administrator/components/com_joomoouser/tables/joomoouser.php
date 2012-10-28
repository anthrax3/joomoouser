<?php
/**
 * @author      Tom Hartung <webmaster@tomhartung.com>
 * @package     Joomla
 * @subpackage  JoomooUser
 * @copyright   Copyright (C) 2010 Tom Hartung. All rights reserved.
 * @since       1.5
 * @license     GNU/GPL, see LICENSE.php
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

jimport('joomla.application.component.controller');

/**
 * Joomla class interface to #__joomoouser table
 * Defines column names and database methods for the joomoouser table
 */
class TableJoomooUser extends JTable
{
	/**
	 * @var int Primary Key
	 */
	public $id = null;
	/**
	 * @var int user id: foreign key to jos_users table
	 */
	public $user_id = null;
	/**
	 * @var enum
	 */
	public $comment_posted_email = null;
	/**
	 * @var timestamp
	 */
	public $timestamp = null;

	/**
	 * Constructor
	 */
	public function __construct( &$db )
	{
		parent::__construct( '#__joomoouser', 'id', $db );

		// print "Hello from TableJoomooUser::__construct()<br />\n";
	}

	/**
	 * Validator: ensure required values are set
	 * @return boolean True if values are valid else False
	 */
	public function check()
	{
		// print "Hello from TableJoomooUser::check()<br />\n";

		return true;
	}
}

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
 * Model for JoomooUser Component
 */
class JoomooUserModelJoomooUser extends JoomoobaseModelJoomoobaseDb
{
	/**
	 * Constructor - just a formality for this component
	 * @access public
	 */
	public function __construct()
	{
		parent::__construct();

		$this->_tableName = "#__joomoouser";
	}
	/**
	 * Gets row of data corresponding to ID
	 * @access public
	 * @return if successful returns array containing row of data else returns null
	 * @note overriding base class method so we can use the foreign key and process failure as appropriate
	 */
	public function getRow ( $user_id=0 )
	{
		// print "Oh haiii from JoomooUserModelJoomooUser::getRow()<br />\n";
		// print 'user_id = ' . $user_id . "<br />\n";
		$this->_row = null;

		if ( is_numeric($user_id) && $user_id > 0 )
		{
			$db =& $this->getDBO();
			$query = 'SELECT ' . $db->nameQuote('id' ) . ', ' . $db->nameQuote('user_id' ) . ', ' .
			                     $db->nameQuote('comment_posted_email' ) .
			          ' FROM ' . $db->nameQuote($this->_tableName ) .
			          ' WHERE ' . $db->nameQuote('user_id') . ' = ' . $db->quote($user_id);
			$db->setQuery( $query );
			$retVal = $db->loadObject();
			// print 'retVal: ' . print_r($retVal,true) . "<br />\n";

			if ( $retVal )
			{
				$this->_row = $retVal;
			}
		}

		return $this->_row;
	}
	/**
	 * Gets rows of users who want follow up emails for comments posted to all articles posted to entire site
	 * @access public
	 * @return if successful returns array of rows of data else returns null
	 */
	public function getEntireSiteRows()
	{
		$db =& $this->getDBO();
		$query = 'SELECT ' . $db->nameQuote('id' ) . ', ' . $db->nameQuote('user_id' ) . ', ' .
		                     $db->nameQuote('comment_posted_email' ) .
		          ' FROM ' . $db->nameQuote($this->_tableName ) .
		          ' WHERE ' . $db->nameQuote('comment_posted_email') . ' = ' . $db->quote(JOOMOOUSER_COMMENT_POSTED_EMAIL_ENTIRE_SITE);
		$db->setQuery( $query );
		$entireSiteRows = $db->loadObjectList();
		// print 'entireSiteRows: ' . print_r($entireSiteRows,true) . "<br />\n";

		return $entireSiteRows;
	}

}
?>

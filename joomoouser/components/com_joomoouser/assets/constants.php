<?php
/**
 * @author      Tom Hartung <webmaster@tomhartung.com>
 * @package     Joomla
 * @subpackage  joomoouser
 * @copyright   Copyright (C) 2010 Tom Hartung. All rights reserved.
 * @since       1.5
 * @license     TBD.
 */
	//
	// constants.php: constants and default values
	// -------------------------------------------
	//
	/**
	 * default value for admin_language param - used when adding a new user
	 */
	define( 'JOOMOOUSER_DEFAULT_ADMIN_LANGUAGE', 'en-GB' );
	/**
	 * default value for language param - used when adding a new user
	 */
	define( 'JOOMOOUSER_DEFAULT_LANGUAGE', 'en-GB' );
	/**
	 * default value for editor param - used when adding a new user
	 */
	define( 'JOOMOOUSER_DEFAULT_EDITOR', 'none' );
	/**
	 * default value for help site param - used when adding a new user
	 */
	define( 'JOOMOOUSER_DEFAULT_HELPSITE', 'http://help.joomla.org/' );
	/**
	 * default value for time zone param - used when adding a new user
	 */
	define( 'JOOMOOUSER_DEFAULT_TIMEZONE', '-7' );
	/**
	 * default value for show page title param - used when adding a new user
	 */
	define( 'JOOMOOUSER_DEFAULT_SHOW_PAGE_TITLE', '1' );

	/**
	 * values for comment_posted_email (saved in database)
	 */
	define( 'JOOMOOUSER_COMMENT_POSTED_EMAIL_FOLLOW_UP_COMMENT', 'C' );
	define( 'JOOMOOUSER_COMMENT_POSTED_EMAIL_FOLLOW_UP_AUTHOR', 'A' );
	define( 'JOOMOOUSER_COMMENT_POSTED_EMAIL_FOLLOW_UP', 'F' );
	define( 'JOOMOOUSER_COMMENT_POSTED_EMAIL_NEVER', 'N' );
	define( 'JOOMOOUSER_COMMENT_POSTED_EMAIL_ENTIRE_SITE', 'E' );
	// define( 'JOOMOOUSER_COMMENT_POSTED_EMAIL_DEFAULT', JOOMOOUSER_COMMENT_POSTED_EMAIL_NEVER );
	define( 'JOOMOOUSER_COMMENT_POSTED_EMAIL_DEFAULT', JOOMOOUSER_COMMENT_POSTED_EMAIL_FOLLOW_UP );
?>

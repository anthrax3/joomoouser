<?php
/********************************************************/
/* Copyright (C) 2011 Tom Hartung, All Rights Reserved. */
/********************************************************/

/**
 * @version     $Id: arrayExp.php,v 1.3 2009/05/04 14:04:01 tomh Exp tomh $
 * @author      Tom Hartung <webmaster@tomhartung.com>
 * @package     Joomla
 * @subpackage  joomoouser
 * @copyright   Copyright (C) 2011 Tom Hartung. All rights reserved.
 * @since       1.5
 * @license     GNU/GPL, see LICENSE.php .
 */

/**
 * ================================================
 * This class runs outside of the joomla! framework
 * ================================================
 * Therefore we define _JEXEC (rather than check to see if it's defined)
 */
define( '_JEXEC', 1 );
define( 'JPATH_BASE', dirname(__FILE__) );
define( 'JPATH_PLATFORM', dirname(__FILE__));
//print "<p>JPATH_BASE = '" . JPATH_BASE . "'</p>\n";
//print "<p>JPATH_PLATFORM = '" . JPATH_PLATFORM . "'</p>\n";

if ( !defined('DIRECTORY_SEPARATOR') )
{
	define( 'DIRECTORY_SEPARATOR', "/" );
}
define('DS', DIRECTORY_SEPARATOR);
$server_root = $_SERVER['DOCUMENT_ROOT'];
define( 'JPATH_SITE', $server_root );
define( 'JPATH_LIBRARIES', JPATH_BASE .DS. 'libraries');

require_once '..' .DS. '..' .DS. '..' .DS. 'configuration.php';
require_once '..' .DS. '..' .DS. '..' .DS. 'libraries' .DS. 'loader.php';
require_once '..' .DS. '..' .DS. '..' .DS. 'libraries' .DS. 'joomla' .DS. 'base' .DS. 'object.php';
require_once '..' .DS. '..' .DS. '..' .DS. 'libraries' .DS. 'joomla' .DS. 'factory.php';
require_once '..' .DS. '..' .DS. '..' .DS. 'libraries' .DS. 'joomla' .DS. 'database' .DS. 'database.php';
require_once '..' .DS. '..' .DS. '..' .DS. 'libraries' .DS. 'joomla' .DS. 'database' .DS. 'database' .DS. 'mysql.php';
require_once '..' .DS. '..' .DS. '..' .DS. 'libraries' .DS. 'joomla' .DS. 'database' .DS. 'table.php';
require_once '..' .DS. '..' .DS. '..' .DS. 'libraries' .DS. 'joomla' .DS. 'error' .DS. 'error.php';
require_once '..' .DS. '..' .DS. '..' .DS. 'libraries' .DS. 'joomla' .DS. 'environment' .DS. 'request.php';
require_once '..' .DS. '..' .DS. '..' .DS. 'libraries' .DS. 'joomla' .DS. 'filter' .DS. 'filterinput.php';
require_once '..' .DS. '..' .DS. '..' .DS. 'libraries' .DS. 'joomla' .DS. 'methods.php';
require_once '..' .DS. '..' .DS. '..' .DS. 'libraries' .DS. 'joomla' .DS. 'user' .DS. 'user.php';
require_once '..' .DS. '..' .DS. '..' .DS. 'libraries' .DS. 'joomla' .DS. 'utilities' .DS. 'utility.php';

require_once '..' .DS. '..' .DS. '..' .DS. 'components' .DS. 'com_joomoobase' .DS. 'utilities' .DS. 'JoomoobaseEmailer.php';
require_once '..' .DS. '..' .DS. '..' .DS. 'components' .DS. 'com_joomoouser' .DS. 'assets' .DS. 'constants.php';
require_once '..' .DS. '..' .DS. '..' .DS. 'administrator' .DS. 'components' .DS. 'com_joomoouser' .DS. 'tables' .DS. 'users.php';

/**
 * Class to send follow up comment emails.
 * Note that the code to instantiate and use this class appears at the end of this file!
 */
class SendFollowUpEmails extends JObject
{
	/**
	 * JConfig object containing our configuration options
	 * @access protected
	 */
	protected $_config;
	/**
	 * array of configuration options used to set up DB connection
	 * @access protected
	 */
	protected $_options;
	/**
	 * JDatabase object used to connect with DB
	 * @access protected
	 */
	protected $_db;
	/**
	 * database table name
	 * @access protected
	 */
	protected $_tableName;
	/**
	 * Object of type JTable
	 * @access protected
	 */
	protected $_table;
	/**
	 * author of comment
	 * @access private
	 */
	private $_commentAuthorName;
	/**
	 * text of comment
	 * @access private
	 */
	private $_text;
	/**
	 * link to article being commented on
	 * @access private
	 */
	private $_readmore_link;

	public function __construct()
	{
		parent::__construct();

		/*
		 * Read configuration information and establish connection to DB
		 */
		JFactory::getConfig( "../../../configuration.php" );
		$this->_config = new JConfig();
		$host = $this->_config->host;
		$user = $this->_config->user;
		$password = $this->_config->password;
		$db = $this->_config->db;
		$dbprefix = $this->_config->dbprefix;

		// print "host = " . $host . "<br />\n";
		// print "user = " . $user . "<br />\n";
		// print "password = " . $password . "<br />\n";
		// print "db = " . $db . "<br />\n";
		// print "dbprefix = " . $dbprefix . "<br />\n";

		$this->_options = array (
			'host'     => $host,
			'user'     => $user,
			'password' => $password,
			'database' => $db,
			'prefix'   => $dbprefix,
		);
//		$this->_db =& new JDatabaseMySQL( $this->_options );
		$this->_db = JFactory::getDbo();
		$this->_tableName = '#__users';
		$this->_table = new TableUsers( $this->_db );
		// print 'this->_db: ' . print_r($this->_db,true) . "\n";
	}

	/**
	 * send a follow up comment email to each user id in array
	 */
	public function sendTheEmails()
	{
		$this->_text = JRequest::getVar( 'text', 'post', '' );
		$this->_readmore_link = JRequest::getVar( 'readmore_link', 'post', '' );
		$this->_commentAuthorName = JRequest::getVar( 'commentAuthorName', 'post', '' );

		$idsToEmailString = JRequest::getVar( 'idsToEmail', 'post', '' );
		$comment_posted_email_string = JRequest::getVar( 'comment_posted_email', 'post', '' );

		$idsToEmailArray = explode( ',', $idsToEmailString );
		$comment_posted_email_array = explode( ',', $comment_posted_email_string );
		$totalEmailsSent = 0;

		for ( $index = 0; $index < count($idsToEmailArray); $index++ )
		{
			$idToEmail = $idsToEmailArray[$index];
			$comment_posted_email = $comment_posted_email_array[$index];
			if ( $this->_table->load($idToEmail) )
			{
				// print 'Sending email to address: ' . $this->_table->email . ' for reason code "' . $comment_posted_email . '".' . "\n";
				if ( $this->_sendAnEmail( $comment_posted_email ) )
				{
					$totalEmailsSent++;
				}
			}
		}

		print $totalEmailsSent;
	}

	/**
	 * send a single follow up comment email with content based on the reason code in the request
	 * @return True if successful which it apparently always is
	 */
	private function _sendAnEmail( $comment_posted_email=JOOMOOUSER_COMMENT_POSTED_EMAIL_DEFAULT )
	{
		$fromname = $this->_config->fromname;
		// print 'fromname = "' . $fromname . "\"\n";
		// print 'Text of comment is "' . $this->_text . "\n" . 'this->_readmore_link is "' . $this->_readmore_link . "\n";

		$subject = JText::_( $this->_commentAuthorName . ' commented on an article on ' . $fromname );
		$subject = html_entity_decode($subject, ENT_QUOTES);
		$message  = JText::_( "Hello " . $this->_table->name . ",\n\n" );
		$message .= JText::_( $this->_commentAuthorName . ' left this comment on ' . $fromname . ":\n\n----------\n" );
		$message .= JText::_( $this->_text . "\n----------\n\n" );
		$message .= JText::_( 'Follow this link to see the article and all comments:' . "\n" );
		$message .= JText::_( 'http://' . $fromname . '/' . $this->_readmore_link . "\n\n" );
		$message .= JText::_( 'You are receiving this email because ' );

		switch ( $comment_posted_email )
		{
			case JOOMOOUSER_COMMENT_POSTED_EMAIL_FOLLOW_UP_COMMENT:
				$message .= 'you have requested an email notification ';
				$message .= 'for comments posted to articles you ';
				$message .= 'have commented on.' . "\n";
				break;
			case JOOMOOUSER_COMMENT_POSTED_EMAIL_FOLLOW_UP_AUTHOR:
				$message .= 'you have requested an email notification ';
				$message .= 'for comments posted to articles ';
				$message .= 'you have written.' . "\n";
				break;
			case JOOMOOUSER_COMMENT_POSTED_EMAIL_FOLLOW_UP:
				$message .= 'you have requested an email notification ';
				$message .= 'for comments posted to articles ';
				$message .= 'you have written or commented on.' . "\n";
				break;
			case JOOMOOUSER_COMMENT_POSTED_EMAIL_ENTIRE_SITE:
				$message .= 'you have requested an email notification ';
				$message .= 'for comments posted to any article ';
				$message .= 'on the entire site.' . "\n";
				break;
			default:
			case JOOMOOUSER_COMMENT_POSTED_EMAIL_DEFAULT:
				$message .= 'you have apparently requested an email ';
				$message .= 'notification for comments posted to ';
				$message .= 'one or more articles on the site.' . "\n";
				break;
		}

		$message .= JText::_( "\n\n" );
		$message .= JText::_( "\n" );
		$message .= JText::_( "\n\n" );
		$message = html_entity_decode( $message, ENT_QUOTES );
		$recipientEmail = $this->_table->email;

		$mailer = new JoomoobaseEmailer();
		$mailer->sender = 'do_not_reply@' . $fromname;
		$mailer->recipient = $recipientEmail;
		$mailer->subject = $subject;
		$mailer->body = $message;
		// $mailer->headers = $headers;

		//	$emailedOk = True;
		//  $emailedOk = $mailer->sendEmailJMail( );
		$emailedOk = $mailer->sendEmailJUtility( );

		return $emailedOk;
	}
}

$sender = new SendFollowUpEmails();
$sender->sendTheEmails();

?>

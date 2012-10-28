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

jimport('joomla.application.component.controller');

/**
 * joomoouser Component Controller
 *
 */
class JoomooUserController extends JController
{
	/**
	 * path to code for parent model class - in joomooobase
	 * @access private
	 * @var string file path
	 */
	private $_parentModelPath;
	/**
	 * path to code for users model class
	 * @access private
	 * @var string file path
	 */
	private $_usersModelPath;
	/**
	 * model supporting access to jos_users table in DB
	 * @access private
	 * @var instance of UsersModelUsers
	 */
	private $_usersModel = null;
	/**
	 * path to code for joomooUser model class
	 * @access private
	 * @var string file path
	 */
	private $_joomooUserModelPath;
	/**
	 * model supporting access to joomoouser table in DB
	 * @access private
	 * @var instance of JoomooUsersModelJoomooUser
	 */
	private $_joomooUserModel = null;
	/**
	 * JUser object
	 * @access private
	 * @var object JUser object
	 */
	private $_user = '';

	/**
	 * Constructor: set the model paths
	 * @access public
	 */
	public function __construct( $default = array() )
	{
		//	print "Hello from JoomooUserController::__construct()<br />\n";

		parent::__construct( $default );
		$this->_user = & JFactory::getUser();

		$this->_usersModelPath = 'components'.DS.'com_joomoouser'.DS.'models'.DS.'users.php';
		$this->_parentModelPath = 'components'.DS.'com_joomoobase'.DS.'models'.DS.'joomoobaseDb.php';
		$this->_joomooUserModelPath = 'components'.DS.'com_joomoouser'.DS.'models'.DS.'joomoouser.php';
		// print '<br />this->_joomooUserModelPath = ' . $this->_joomooUserModelPath . '<br />';
	}

	/**
	 * Unused - we either post and redirect or do nothin' - kept as kind of a safety-net
	 * @access public
	 */
	public function display()
	{
		$viewName = JRequest::getVar('view');
		// print '<br />Oh haii from controllers/joomoouser.php where viewName = ' . $viewName . '<br />';

		if ( $viewName == 'joomoouseredit' )
		{
			require_once( $this->_parentModelPath );
			require_once( $this->_joomooUserModelPath );
			$this->_joomooUserModel = new JoomooUserModelJoomooUser();
			$viewObject =& $this->getView( $viewName, 'html' );
			$viewObject->setModel( &$this->_joomooUserModel, true );        // 'true' makes this the default model
		}

		parent::display();
	}
	/**
	 * if possible adds a user
	 * @access public
	 * @return TRUE if successful else FALSE
	 */
	public function add( )
	{
		//	print "Hello from JoomooUserController::add() in file controllers/joomoouser.php<br />\n";

		// $userParams = &JComponentHelper::getParams( 'com_user' );
		$joomooUserParams = &JComponentHelper::getParams( 'com_joomoouser' );
		$require_captcha = $joomooUserParams->get( 'require_captcha' );
		$message = '';

		//
		// If the string is bad we must set and display the view rather than redirect so
		//     that we can recycle the values they entered
		//
		if ( $require_captcha )
		{
			$captcha_type = $joomooUserParams->get( "captcha_type" );
			$baseConstantsFilePath = JPATH_SITE.DS.'components'.DS.'com_joomoobase'.DS.'assets'.DS.'constants.php';
			require_once( $baseConstantsFilePath );
			$captchaFilePath = JPATH_SITE.DS.'components'.DS.'com_joomoobase'.DS.'captcha'.DS.'JoomoobaseCaptcha.php';
			require_once( $captchaFilePath );
			$captchaObject = new JoomoobaseCaptcha( $captcha_type );
			if ( ! $captchaObject->checkCaptchaResponse() )
			{
				$link = 'index.php?option=com_joomoouser&view=joomoouseradd';
				$message .= $captchaObject->getError();
				$view =& $this->getView( 'joomoouseradd', 'html' );
				$messageObject = new stdClass();
				$messageObject->title = 'Wrong Captcha String!';
				$messageObject->text = $message;
				$view->message = $messageObject;
				$view->display();
				return FALSE;
			}
		}

		require_once $this->_usersModelPath;
		$this->_usersModel = new UsersModelUsers();
		$link = 'index.php';

		if ( $this->_usersModel->duplicateEmail() )
		{
			$message = $this->_usersModel->getError();
			$view =& $this->getView( 'joomoouseradd', 'html' );
			$messageObject = new stdClass();
			$messageObject->title = 'Duplicate Email Address!';
			$messageObject->text = $message;
			$view->message = $messageObject;
			$view->display();
			return FALSE;
		}

		$storedOk = $this->_usersModel->store();

		if ( $storedOk )
		{
			$usersConfig = &JComponentHelper::getParams( 'com_users' );
			$useractivation = $usersConfig->get('useractivation');
			$useractivation ? $message .= 'Check your email and click on the activation link' :
				$message .= 'Your account is now active and you can log in - Thanks!!';
			// $message .= '<br />  useractivation = "' . print_r($useractivation,true) . '"';
			//
			// Save a new row in the jos_joomoousers table
			// Check param to see if we need to send a notification email to site admin
			//
			$this->_user = & JFactory::getUser();
			$user_row_id = $this->_user->id;
			$joomooUserStoredOk = $this->_storeJoomooUser( 0, $user_row_id );    // seems to work OK but returns false anyway hmmm....
			$create_account_notifications = $joomooUserParams->get( 'create_account_notifications' );
			// $message .= '<br />  create_account_notifications = "' . $create_account_notifications . '"';
			if ( $create_account_notifications )
			{
				$emailedOk = $this->_sendCreateAccountNotification();
				// $message .= '<br />  emailedOk = "' . print_r($emailedOk,true) . '"';
			}
		}
		else
		{
			$link = 'index.php?option=com_joomoouser&task=add';
			$errorMessage = $this->_usersModel->getError();
			if ( 0 < strlen($errorMessage) )
			{
				$message .= "Error creating user: " . $errorMessage . '<br />';
				$message .= 'Please try again.';
			}
			else
			{
				$message .= 'Something went wrong and we are unable to create a user for you at this time.<br />';
				$message .= 'Please try again later.';
			}
		}

		// print "add not redirecting; link = " . $link . "; message = " . $message . "<br />\n";
		$this->setRedirect( $link, $message );

		return $storedOk;
	}
	/*
	 * activates a user who's been created
	 * Inspired by the "real" method with the same name in
	 *    joomla/components/com_user/controller.php
	 * Couldn't use that method because $user->get('id') is true for us (see below)
	 * @access public
	 * @return TRUE if successful else FALSE
	 */
	public function activate()
	{
		$app = JFactory::getApplication();

		// Initialize some variables
		$db			=& JFactory::getDBO();
		$user 		=& JFactory::getUser();
		$document   =& JFactory::getDocument();
		$pathway 	=& $app->getPathWay();

		$usersConfig = &JComponentHelper::getParams( 'com_users' );
		$useractivation			= $usersConfig->get('useractivation');
		$allowUserRegistration	= $usersConfig->get('allowUserRegistration');

		// We can't do this - for some reason it's set when we are using joomoouser!!
		//	// Check to see if they're logged in, because they don't need activating!
		//	if ($user->get('id')) {
		//		// They're already logged in, so redirect them to the home page
		//		$app->redirect( 'index.php' );
		//	}

		if ($allowUserRegistration == '0' || $useractivation == '0') {
			JError::raiseError( 403, JText::_( 'Access Forbidden' ));
			return;
		}

		// If we don't have an activation string ("should not occur") just go to the home page
		//
		$activation = JRequest::getVar('activation', '', '', 'alnum' );
		$activation = $db->getEscaped( $activation );
		if (empty( $activation ))
		{
			$link = 'index.php';
			//	$message = 'No activation string?!?!';
			$this->setRedirect( $link, $message );
			return;
		}

		// Lets activate this user
		//
		jimport('joomla.user.helper');
		$activatedOk = JUserHelper::activateUser($activation);
		if ( $activatedOk )
		{
			$link = 'index.php?option=com_users&view=login';
			$message = 'You have successfully activated your account.  Log in and have fun!';
			$this->setRedirect( $link, $message );
		}
		else
		{
			if ( $user->guest )
			{
				$link = 'index.php?option=com_users&view=login';
				$message = 'Activation failed.  You may be already activated and can log in.';
				$this->setRedirect( $link, $message );
			}
			else
			{
				$link = 'index.php';
				$message = 'You are already activated and logged in.  Enjoy your stay!';
				$this->setRedirect( $link, $message );
			}
		}

		return $activatedOk;
	}
	/**
	 * unpublishes a user
	 * @access public
	 * @return TRUE if successful else FALSE
	 */
	public function deactivate( )
	{
		//	print "Hello from JoomooUser::deactivate() in file controllers/joomoouser.php<br />\n";

		$id = $this->_user->id;

		if ( $id == 0 )      // This option should not be available to anonymous users!
		{
			$message = 'You must be logged-in to deactivate yourself.';
		}
		else
		{
			require_once $this->_usersModelPath;
			$this->_usersModel = new UsersModelUsers();
			$deactivatedOk = $this->_usersModel->deactivate();
			if ( $deactivatedOk )
			{
				$message = 'User deactivated Ok.  Please log out!';
			}
			else
			{
				$errorMessage = $this->_usersModel->getError();
				0 < strlen($errorMessage) ? $message = 'Unable to deactivate user: ' . $errorMessage . '.' :
					$message = 'Unable to deactivate user - please try again later.';
			}
		}

		$link = 'index.php';

		// print "deactivate not redirecting; link = " . $link . "; message = " . $message . "<br />\n";
		$this->setRedirect( $link, $message );
	}
	/**
	 * updates the joomoo information for a user
	 * @access public
	 * @return TRUE if successful else FALSE
	 */
	public function update( )
	{
		// print 'Oh haiii from JoomooUser::update() in file controllers/joomoouser.php<br />';

		$user_row_id = $this->_user->id;

		if ( $user_row_id == 0 )      // This option should not be available to anonymous users!
		{
			$message = 'You must be logged-in to update an account.';
		}
		else
		{
			$joomoouser_row_id = JRequest::getInt( 'joomoouser_row_id', 0 );
			$comment_posted_email = JRequest::getVar('comment_posted_email', JOOMOOUSER_COMMENT_POSTED_EMAIL_DEFAULT );
			$storedOk = $this->_storeJoomooUser( $joomoouser_row_id, $user_row_id, $comment_posted_email );
			if ( $storedOk )
			{
				$message  = 'Settings saved Ok!';
				// $message .= '  For joomoouser_row_id = ' . $joomoouser_row_id  . ' and user_row_id = ' . $user_row_id;
			}
			else
			{
				$errorMessage = $this->getError();
				0 < strlen($errorMessage) ? $message = 'Unable to store user data: ' . $errorMessage . '.' :
					$message = 'Unable to store user data - please try again later.';
			}
		}

		$link = 'index.php?option=com_joomoouser&view=joomoouseredit';

		// print "update not redirecting; link = " . $link . "; message = " . $message . "<br />\n";
		$this->setRedirect( $link, $message );
	}
	/**
	 * deletes a user
	 * @access public
	 * @return TRUE if successful else FALSE
	 */
	public function delete( )
	{
		//	print "Hello from JoomooUser::delete() in file controllers/joomoouser.php<br />\n";

		$id = $this->_user->id;

		if ( $id == 0 )      // This option should not be available to anonymous users!
		{
			$message = 'You must be logged-in to delete yourself.';
		}
		else
		{
			require_once $this->_usersModelPath;
			$this->_usersModel = new UsersModelUsers();
			$deletedOk = $this->_usersModel->delete();
			if ( $deletedOk )
			{
				$message = 'User deleted Ok.';
			}
			else
			{
				$errorMessage = $this->_usersModel->getError();
				0 < strlen($errorMessage) ? $message = 'Unable to delete user: ' . $errorMessage . '.' :
					$message = 'Unable to delete user - please try again later.';
			}
		}

		$link = 'index.php';

		// print "delete not redirecting; link = " . $link . "; message = " . $message . "<br />\n";
		$this->setRedirect( $link, $message );
	}
	/**
	 * stores a row in the jos_joomoouser table
	 * @return TRUE if successful else FALSE
	 */
	private function _storeJoomooUser( $joomoouser_row_id, $user_row_id=0, $comment_posted_email=JOOMOOUSER_COMMENT_POSTED_EMAIL_DEFAULT )
	{
		if ( is_numeric($user_row_id) && $user_row_id > 0 )
		{
			if ( isset($comment_posted_email) )
			{
				if ( $comment_posted_email != JOOMOOUSER_COMMENT_POSTED_EMAIL_FOLLOW_UP_COMMENT &&
				     $comment_posted_email != JOOMOOUSER_COMMENT_POSTED_EMAIL_FOLLOW_UP_AUTHOR &&
				     $comment_posted_email != JOOMOOUSER_COMMENT_POSTED_EMAIL_FOLLOW_UP &&
				     $comment_posted_email != JOOMOOUSER_COMMENT_POSTED_EMAIL_NEVER &&
				     $comment_posted_email != JOOMOOUSER_COMMENT_POSTED_EMAIL_ENTIRE_SITE )
				{
					$comment_posted_email = JOOMOOUSER_COMMENT_POSTED_EMAIL_DEFAULT;
				}
			}
			else
			{
			     $comment_posted_email = JOOMOOUSER_COMMENT_POSTED_EMAIL_DEFAULT;
			}
			require_once( $this->_parentModelPath );
			require_once( $this->_joomooUserModelPath );
			$this->_joomooUserModel = new JoomooUserModelJoomooUser();
			$data = new stdClass();
			$data->id = $joomoouser_row_id;
			$data->user_id = $user_row_id;
			$data->comment_posted_email = $comment_posted_email;
			$storedOk = $this->_joomooUserModel->store( $data );
			if ( ! $storedOk )
			{
				$errorMessage = $this->_joomooUserModel->getError();
				$this->setError( 'Error saving joomoouserData: "' . $errorMessage . '"' );
			}
		}
		else
		{
			$this->setError( 'Invalid user_row_id specified: "' . $user_row_id . '"' );
			$storedOk = FALSE;
		}

		return $storedOk;
	}
	/**
	 * Sends create account notification email to site admin
	 * @return TRUE if successful else FALSE
	 */
	private function _sendCreateAccountNotification()
	{
		$app = JFactory::getApplication();
		$mailfrom = $app->getCfg( 'mailfrom' );  // site admin email from configuration.php (Global Settings -> Server)
		$sitename = $app->getCfg( 'sitename' );  // site name from configuration.php (Global Settings -> Site)

		$this->_user = & JFactory::getUser();
		$id = $this->_user->id;
		$name = $this->_user->name;
		$username = $this->_user->username;
		$email = $this->_user->email;

		// print 'mailfrom = "' . $mailfrom . "\"<br />\n";
		// print 'sitename = "' . $sitename . "\"<br />\n";
		// print 'id = ' . $id . '<br />';
		// print 'name = ' . $name . '<br />';
		// print 'username = ' . $username . '<br />';

		$subject = JText::_( $name . ' (' . $username . ') created an account on ' . $sitename );
		$subject = html_entity_decode($subject, ENT_QUOTES);
		$body  = JText::_( 'Hello site admin,' . "\n\n" );
		$body .= JText::_( $name . ' (' . $username . ') created a user account on ' . $sitename . ".\n\n" );
		$body .= JText::_( 'id: "' . $id . "\"\n" );
		$body .= JText::_( 'name: "' . $name . "\"\n" );
		$body .= JText::_( 'username: "' . $username . "\"\n" );
		$body .= JText::_( 'email: "' . $email . "\"\n\n" );
		$body .= JText::_( 'You are receiving this email because the Joomoo User component is configured ' );
		$body .= JText::_( 'to notifiy you when someone creates an account.  ' . "\n\n" );
		$body .= JText::_( 'To turn off these emails access the back end of the site at http://' . $sitename . '/administrator , ' );
		$body .= JText::_( 'select the Components -> Joomoo User menu option, then click on the Parameters icon.' . "\n\n" );
		$body = html_entity_decode( $body, ENT_QUOTES );

		require_once JPATH_SITE .DS. 'components' .DS. 'com_joomoobase' .DS. 'utilities' .DS. 'JoomoobaseEmailer.php';
		$mailer = new JoomoobaseEmailer();
		// $mailer->sender = $mailfrom;
		$mailer->sender = 'do_not_reply@' . $sitename;
		$mailer->recipient = $mailfrom;
		$mailer->subject = $subject;
		$mailer->body = $body;

		//  $emailedOk = $mailer->sendEmailJMail( );
		$emailedOk = $mailer->sendEmailJUtility( );

		return $emailedOk;
	}
}
?>

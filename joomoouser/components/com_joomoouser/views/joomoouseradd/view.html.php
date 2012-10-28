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

jimport( 'joomla.application.component.view');

/**
 * HTML View class for the joomoouser component - add user
 */
class JoomoouserViewJoomoouserAdd extends JView
{
	/**
	 * JUser object
	 */
	public $user;
	/**
	 * Component-level params, defined in administrator/components/com_joomoouser/config.xml
	 */
	public $componentParams;

	/**
	 * display/driver function: what the view does
	 */
	public function display($tpl = null)
	{
		$app = JFactory::getApplication();

		//	print "view.html.php: top of JoomoouserViewJoomoouserAdd::display method<br />\n";

		// Load the form validation behavior
		JHTML::_('behavior.formvalidation');

		$this->componentParams = &JComponentHelper::getParams( 'com_joomoouser' );
		$this->user =& JFactory::getUser();

		$params = &$app->getParams();      // Get the parameters of the active menu item
		$this->assignRef('params', $params);

		//	$imagesModel =& $this->getModel( 'joomoouser' );
		//	$this->assignRef( 'imagesModel', $imagesModel );

		parent::display($tpl);
	}
	/**
	 * prints form for user to create an account
	 * This is based heavily on the form in components/com_user/views/register/tmpl/default.php
	 */
	public function printForm ( )
	{
		$require_captcha = $this->componentParams->get('require_captcha');
		//	print ' require_captcha = ' . $require_captcha . '<br />' . "\n";

		print '<script type="text/javascript">' . "\n";
		print '<!--' . "\n";
		print '	Window.onDomReady(function(){' . "\n";
		print '		document.formvalidator.setHandler("passverify", function (value) { return ($("password").value == value); }	);' . "\n";
		print '	});' . "\n";
		print '// -->' . "\n";
		print '</script>' . "\n";
	
		if(isset($this->message))
		{
			$this->display('message');
		}
	
		$inputUsername = JRequest::getVar( 'username', '', 'post' );
		$inputName     = JRequest::getVar( 'name',     '', 'post' );
		$inputEmail    = JRequest::getVar( 'email',    '', 'post' );

		0 < strlen( $inputUsername ) ? $username = $inputUsername : $username = $this->user->get( 'username' );
		0 < strlen( $inputName     ) ? $name     = $inputName     : $name     = $this->user->get( 'name' );
		0 < strlen( $inputEmail    ) ? $email    = $inputEmail    : $email    = $this->user->get( 'email' );

		//	print 'inputUsername = ' . $inputUsername . '<br />' . "\n";
		//	print 'inputName = ' . $inputName . '<br />' . "\n";
		//	print 'inputEmail = ' . $inputEmail . '<br />' . "\n";
		//	print 'username = ' . $username . '<br />' . "\n";
		//	print 'name = ' . $name . '<br />' . "\n";
		//	print 'email = ' . $email . '<br />' . "\n";

		print '<form action="index.php?option=com_joomoouser" method="post" id="josForm" name="josForm" class="form-validate">' . "\n";
		print ' <table cellpadding="0" cellspacing="0" border="0" width="100%" class="contentpane">' . "\n";
		print '  <tr>' . "\n";
		print '   <td height="40">' . "\n";
		print '    <label id="usernamemsg" for="username">' . JText::_('Username:') . '<br /><span class="small">' .
		             JText::_('(lower case only') . '<br />' . JText::_('with no spaces)') . '&nbsp;</span>' . '</label>' . "\n";
		print '   </td>' . "\n";
		print '   <td>' . "\n";
		print '    <input type="text" id="username" name="username" size="40" value="' . $this->escape($username) .'" ' .
			        'class="inputbox required validate-username" maxlength="25" /> *' . "\n";
		print '   </td>' . "\n";
		print '  </tr>' . "\n";
		print '  <tr>' . "\n";
		print '   <td width="30%" height="40">' . "\n";
		print '    <label id="namemsg" for="name">' . JText::_( 'Real Name:' ) . '</label>' . "\n";
		print '   </td>' . "\n";
		print '   <td>' . "\n";
		print '    <input type="text" name="name" id="name" size="40" value="' . $this->escape($name) . '" ' .
			        'class="inputbox required" maxlength="50" /> *' . "\n";
		print '   </td>' . "\n";
		print '  </tr>' . "\n";
		print '  <tr>' . "\n";
		print '   <td height="40">' . "\n";
		print '    <label id="emailmsg" for="email">' . JText::_( 'Email Address:' ) . '</label>' . "\n";
		print '   </td>' . "\n";
		print '   <td>' . "\n";
		print '    <input type="text" id="email" name="email" size="40" value="' . $this->escape($email) . '" ' .
			        'class="inputbox required validate-email" maxlength="100" /> *' . "\n";
		print '   </td>' . "\n";
		print '  </tr>' . "\n";
		print '  <tr>' . "\n";
		print '   <td height="40">' . "\n";
		print '    <label id="pwmsg" for="password">' . JText::_( 'Password:' ) . '</label>' . "\n";
		print '   </td>' . "\n";
		print '   <td>' . "\n";
		print '    <input class="inputbox required validate-password" type="password" id="password" name="password" ' .
		            'size="40" value="" /> *' . "\n";
		print '   </td>' . "\n";
		print '  </tr>' . "\n";
		print '  <tr>' . "\n";
		print '   <td height="40">' . "\n";
		print '    <label id="pw2msg" for="password2">' . JText::_( 'Verify Password:' ) . '</label>' . "\n";
		print '   </td>' . "\n";
		print '   <td>' . "\n";
		print '    <input class="inputbox required validate-passverify" type="password" id="password2" name="password2" ' .
		            'size="40" value="" /> *' . "\n";
		print '   </td>' . "\n";
		print '  </tr>' . "\n";
		//	print '  <tr>' . "\n";
		//	print '   <td colspan="2" height="40">' . "\n";
		//	print JText::_( 'Fields marked with an asterisk (*) are required.' );
		//	print '   </td>' . "\n";
		//	print '  </tr>' . "\n";
		print ' </table>' . "\n";

		print '  <p class="joomoouser">' . "\n";
		print JText::_( 'Fields marked with an asterisk (*) are required.' );
		print '  </p>' . "\n";
		print '  <p class="joomoouser">' . "\n";
		print JText::_( 'Please wait patiently for the next screen and click on the "Register" button ONLY ONCE.' );
		print '  </p>' . "\n";

		if ( $require_captcha )
		{
			$baseConstantsFilePath = JPATH_SITE.DS.'components'.DS.'com_joomoobase'.DS.'assets'.DS.'constants.php';
			require_once( $baseConstantsFilePath );
			$captcha_type = $this->componentParams->get('captcha_type');
			$captchaFilePath = JPATH_SITE.DS.'components'.DS.'com_joomoobase'.DS.'captcha'.DS.'JoomoobaseCaptcha.php';
			require_once( $captchaFilePath );
			$captchaObject = new JoomoobaseCaptcha( $captcha_type );
			print $captchaObject->getCaptchaString() . "\n";
		}

		print ' <center><button class="button validate" type="submit">' . JText::_('Register') . '</center></button>' . "\n";
		print ' <input type="hidden" name="task" value="add" />' . "\n";
		print ' <input type="hidden" name="id" value="0" />' . "\n";
		print ' <input type="hidden" name="gid" value="0" />' . "\n";
		print JHTML::_( 'form.token' );
		print '</form>' . "\n";
	}
}
 ?>

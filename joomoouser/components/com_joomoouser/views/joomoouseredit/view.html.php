<?php
/**
 * @author      Tom Hartung <webmaster@tomhartung.com>
 * @package     Joomla
 * @subpackage  joomoouser
 * @copyright   Copyright (C) 2011 Tom Hartung. All rights reserved.
 * @since       1.5
 * @license     GNU/GPL, see LICENSE.php
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

jimport( 'joomla.application.component.view');

/**
 * HTML View class for the joomoouser component - delete user
 */
class JoomoouserViewJoomoouserEdit extends JView
{
	/**
	 * JUser object
	 */
	public $user;
	/**
	 * @var int id of row in joomoouser for this user
	 */
	private $_joomoouser_row_id = 0;
	/**
	 * @var enum sets level of comment emails user wants to receive
	 */
	private $_comment_posted_email = null;

	public function display($tpl = null)
	{
		$app = JFactory::getApplication();
		// print "view.html.php: top of JoomoouserViewJoomoouserEdit::display method<br />\n";

		$this->user =& JFactory::getUser();
		$joomoouserModel = $this->getModel('joomoouser');
		$joomoouserRow = $joomoouserModel->getRow( $this->user->id );
		// print '<br />this->user->id: ' . $this->user->id . '<br />';
		// print '<br />joomoouserRow: ' . print_r($joomoouserRow,true) . '<br />';

		if ( $joomoouserRow )
		{
			$this->_joomoouser_row_id = $joomoouserRow->id;
			$this->_comment_posted_email = $joomoouserRow->comment_posted_email;
		}
		else
		{
			$this->_joomoouser_row_id = 0;
			$this->_comment_posted_email = JOOMOOUSER_COMMENT_POSTED_EMAIL_DEFAULT;
		}

		// print '<br />this->_comment_posted_email: ' . $this->_comment_posted_email . '<br />';
		$params = &$app->getParams();      // Get the parameters of the active menu item
		$this->assignRef('params', $params);

		parent::display($tpl);
	}
	/**
	 * prints form for user to edit their account
	 */
	public function printForm ( )
	{
		$comment_posted_email_follow_up_comment_checked = '';
		$comment_posted_email_follow_up_author_checked = '';
		$comment_posted_email_follow_up_checked = '';
		$comment_posted_email_never_checked = '';
		$comment_posted_email_entire_site_checked = '';

		switch( $this->_comment_posted_email )
		{
			case JOOMOOUSER_COMMENT_POSTED_EMAIL_FOLLOW_UP_COMMENT:
				$comment_posted_email_follow_up_comment_checked = 'checked="checked"';
				break;
			case JOOMOOUSER_COMMENT_POSTED_EMAIL_FOLLOW_UP_AUTHOR:
				$comment_posted_email_follow_up_author_checked = 'checked="checked"';
				break;
			case JOOMOOUSER_COMMENT_POSTED_EMAIL_FOLLOW_UP:
				$comment_posted_email_follow_up_checked = 'checked="checked"';
				break;
			case JOOMOOUSER_COMMENT_POSTED_EMAIL_NEVER:
				$comment_posted_email_never_checked = 'checked="checked"';
				break;
			case JOOMOOUSER_COMMENT_POSTED_EMAIL_ENTIRE_SITE:
				$comment_posted_email_entire_site_checked = 'checked="checked"';
				break;
			default:
				$comment_posted_email_never_checked = 'checked="checked"';
				break;
		}

		// print '<br />this->comment_posted_email: ' . $this->_comment_posted_email . '<br />';
		print '<form action="index.php?option=com_joomoouser" method="post" id="josForm" name="josForm" class="form-validate">' . "\n";
		print '  <ul class="joomoouser-no_arrows">' . "\n";
		print '    <li class="joomoouser-no_arrows">' . "\n";
		print '      <input type="radio" name="comment_posted_email" id="comment_posted_email_follow_up_comment" class="joomoouser" ' .
		               'value="' . JOOMOOUSER_COMMENT_POSTED_EMAIL_FOLLOW_UP_COMMENT . '" ' . $comment_posted_email_follow_up_comment_checked . '" />' . "\n";
		print '      <label for="comment_posted_email_follow_up_comment">Send email when someone comments on an article you have commented on</label>' . "\n";
		print '    </li>' . "\n";
		print '    <li class="joomoouser-no_arrows">' . "\n";
		print '      <input type="radio" name="comment_posted_email" id="comment_posted_email_follow_up_author" class="joomoouser" ' .
		               'value="' . JOOMOOUSER_COMMENT_POSTED_EMAIL_FOLLOW_UP_AUTHOR . '" ' . $comment_posted_email_follow_up_author_checked . '" />' . "\n";
		print '      <label for="comment_posted_email_follow_up_author">Send email when someone comments on an article you wrote</label>' . "\n";
		print '    </li>' . "\n";
		print '    <li class="joomoouser-no_arrows">' . "\n";
		print '      <input type="radio" name="comment_posted_email" id="comment_posted_email_follow_up" class="joomoouser" ' .
		               'value="' . JOOMOOUSER_COMMENT_POSTED_EMAIL_FOLLOW_UP . '" ' . $comment_posted_email_follow_up_checked . '" />' . "\n";
		print '      <label for="comment_posted_email_follow_up">Send email when someone comments on an article you have commented on or written</label>' . "\n";
		print '    </li>' . "\n";
		print '    <li class="joomoouser-no_arrows">' . "\n";
		print '      <input type="radio" name="comment_posted_email" id="comment_posted_email_entire_site" class="joomoouser" ' .
		               'value="' . JOOMOOUSER_COMMENT_POSTED_EMAIL_ENTIRE_SITE . '" ' . $comment_posted_email_entire_site_checked . '" />' . "\n";
		print '      <label for="comment_posted_email_entire_site">Send email when someone comments on ANY article on the entire site</label>' . "\n";
		print '    </li>' . "\n";
		print '    <li class="joomoouser-no_arrows">' . "\n";
		print '      <input type="radio" name="comment_posted_email" id="comment_posted_email_never" class="joomoouser" ' .
		               'value="' . JOOMOOUSER_COMMENT_POSTED_EMAIL_NEVER . '" ' . $comment_posted_email_never_checked . '" />' . "\n";
		print '      <label for="comment_posted_email_never">Never send email when comments are posted on this site</label>' . "\n";
		print '    </li>' . "\n";
		print '  </ul>' . "\n";
		print '  <button class="button validate joomoouser" type="submit">' . JText::_('Set Notifications') . '</button>' . "\n";
		print '  <input type="hidden" name="task" value="update" />' . "\n";
		print '  <input type="hidden" name="joomoouser_row_id" value="' . $this->_joomoouser_row_id . '" />' . "\n";
		print JHTML::_( 'form.token' );
		print '</form>' . "\n";
	}
}
?>

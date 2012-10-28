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
 * HTML View class for the joomoouser component - delete user
 */
class JoomoouserViewJoomoouserDelete extends JView
{
	/**
	 * JUser object
	 */
	public $user;

	public function display($tpl = null)
	{
		$app = JFactory::getApplication();

		//	print "view.html.php: top of JoomoouserViewJoomoouserDelete::display method<br />\n";

		$params = &$app->getParams();      // Get the parameters of the active menu item
		$this->assignRef('params', $params);

		$this->user =& JFactory::getUser();

		parent::display($tpl);
	}
	/**
	 * prints form for user to delete their account
	 */
	public function printForm ( )
	{
		print '<form action="index.php?option=com_joomoouser" method="post" id="josForm" name="josForm" class="form-validate">' . "\n";
		print ' <button class="button validate joomoouser" type="submit">' . JText::_('Delete Your Account') . '</button>' . "\n";
		print ' <input type="hidden" name="task" value="delete" />' . "\n";
		print JHTML::_( 'form.token' );
		print '</form>' . "\n";
	}
}
?>

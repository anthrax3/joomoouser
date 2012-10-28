<?php
/**
 * @version     $Id: view.html.php,v 1.15 2009/05/19 22:21:39 tomh Exp tomh $
 * @author      Tom Hartung <webmaster@tomhartung.com>
 * @package     Joomla
 * @subpackage  Joomoouser
 * @copyright   Copyright (C) 2010 Tom Hartung. All rights reserved.
 * @since       1.5
 * @license     GNU/GPL, see LICENSE.php
 */

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport('joomla.application.component.view');

/**
 * defines view class for joomoouser component back end
 */
class JoomooUserViewJoomooUser extends JView
{
	function display($tpl=null)
	{
		JToolBarHelper::title( JText::_( 'Joomoo User' ) );
		JToolBarHelper::preferences( 'com_joomoouser', '150' );

		JHTML::_('behavior.tooltip');

		//
		// Help screens are set up at a server over which I have no control
		// See http://help.joomla.org/content/view/1955/214/ and
		//    backend -> Site -> System -> System Settings -> Help Site
		// For now I am just putting instructions in tmpl/default.php
		//

		$document = & JFactory::getDocument();
		$document->setTitle(JText::_('Joomoo User'));

		parent::display($tpl);
	}
}

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

/**
 * @package     Joomla
 * @subpackage  joomoouser
 */
class TOOLBAR_joomoouser
{
	/**
	 * Setup joomoouser toolbars
	 */
	function _DEFAULT()
	{
		JToolBarHelper::preferences('com_joomoocomments', '500');
	}
}
?>

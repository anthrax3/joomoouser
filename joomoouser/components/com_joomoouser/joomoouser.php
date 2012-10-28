<?php
/**
 * @author      Tom Hartung <webmaster@tomhartung.com>
 * @package     Joomla
 * @subpackage  joomoouser
 * @copyright   Copyright (C) 2010 Tom Hartung. All rights reserved.
 * @since       1.5
 * @license     GNU/GPL, see LICENSE.php
 */

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

JTable::addIncludePath( JPATH_SITE.DS.'administrator'.DS.'components'.DS.'com_joomoouser'.DS.'tables' );

require_once (JPATH_COMPONENT.DS.'assets'.DS.'constants.php');   // Require constants
require_once (JPATH_COMPONENT.DS.'controllers/joomoouser.php');  // Load the controller code

$controller = new JoomooUserController( );            // Create the controller
$controller->execute(JRequest::getCmd('task'));       // Perform the Request task
$controller->redirect();                              // Redirect if set by the controller
?>

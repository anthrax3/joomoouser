<?php
/**
 * @author      Tom Hartung <webmaster@tomhartung.com>
 * @package     Joomla
 * @subpackage  Joomoouser
 * @copyright   Copyright (C) 2010 Tom Hartung. All rights reserved.
 * @since       1.5
 * @license     GNU/GPL, see LICENSE.php
 */
/*
 * default.php: display user delete page
 */

defined('_JEXEC') or die('Restricted access');

$app = JFactory::getApplication();
$componentName = $app->scope;
JHTML::_('stylesheet', 'joomoouser.css', 'components/' . $componentName . '/assets/');
?>

<a name="component_top" id="component_top"></a>
<?php if ( $this->params->get( 'show_page_title' ) ) : ?>
 <div class="componentheading<?php echo $this->params->get( 'pageclass_sfx' ); ?>">
  <?php echo $this->params->get( 'page_title' ); ?>
 </div>
<?php endif; ?>

<?php
print '<center>' . "\n";
print ' <div class="joomoouser_edit">' . "\n";

if ( okToUpdate($this->user) )
{
	print '   <p class="joomoouser">Use this page to set up when you want to receive comment notification emails.</p>' . "\n";
	$this->printForm( );
}

print ' </div>  <!-- close of div with class = joomoouser_edit -->' . "\n";
print '</center>' . "\n";

/**
 * Perform some basic checks to ensure it's OK for this user to delete an account
 */
function okToUpdate ( $user )
{
	if ( $user->get('guest') )
	{
		print '</p>' . "\n";
		print '  <p class="joomoouser">';
		print 'You are NOT logged in and should NOT have access to this option!?!';
		return FALSE;
	}

	return TRUE;
}
?>

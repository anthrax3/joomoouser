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
//	$userModel = $this->userModel;
//	$userRow = $userModel->getRow();

print '<center>' . "\n";
print ' <div class="joomoouser_delete">' . "\n";

if ( okToDelete($this->user) )
{
	print '   <p class="joomoouser">We have not had time to fully implement the delete user option.</p>' . "\n";
	print '   <ul><li>For example, it deletes you from the user table, but does not delete any content you have posted.</li></ul>' . "\n";
	print '   <p class="joomoouser">If you are willing to help fund our development efforts with regards to this functionality, ';
	print      'please contact us!</p>' . "\n";
	print '   <ul><li>If you really want to delete your account, click on the button.</li>' . "\n";
	print '    <li>There is no "Are you sure?" message and no "Un-do!"</li></ul>' . "\n";
	$this->printForm();
}

print '</p>' . "\n";
print ' </div>  <!-- close of div with class = joomoouser_delete -->' . "\n";
print '</center>' . "\n";

/**
 * Perform some basic checks to ensure it's OK for this user to delete an account
 */
function okToDelete ( $user )
{
	//	print '</p>' . "\n";
	//	print 'user object: <br />' . "\n";
	//	print_r( $user );
	//	print '  <p class="joomoouser">';

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

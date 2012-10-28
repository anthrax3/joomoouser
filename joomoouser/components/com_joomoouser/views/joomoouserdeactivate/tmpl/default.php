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
 * default.php: display user deactivate page
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
print ' <div class="joomoouser_deactivate">' . "\n";

if ( okToDeactivate($this->user) )
{
	print '   <p class="joomoouser">Deactivating your account means it will stay in the database but remain inaccessible.</p>' . "\n";
	print '   <ul><li>Only a site administrator will be able to re-activate your account.</li></ul>' . "\n";
	print '   <p class="joomoouser">If you really want to deactivate your account, click on the button.</p>' . "\n";
	print '   <ul><li>There is no "Are you sure?" message and no "Un-do!"</li></ul>' . "\n";
	$this->printForm();
}

print ' </div>  <!-- close of div with class = joomoouser_deactivate -->' . "\n";
print '</center>' . "\n";

/**
 * Perform some basic checks to ensure it's OK for this user to deactivate an account
 */
function okToDeactivate ( $user )
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

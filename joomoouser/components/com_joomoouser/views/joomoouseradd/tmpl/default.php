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
 * default.php: display user add page
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
print ' <div class="joomoouser_add">' . "\n";
print '  <p class="joomoouser">';

if ( okToCreate($this->user) )
{
	$this->printForm();
}

print '</p>' . "\n";
print ' </div>  <!-- close of div with class = joomoouser_add -->' . "\n";
print '</center>' . "\n";

/**
 * Perform some basic checks to ensure it's OK for this user to create an account
 */
function okToCreate ( $user )
{
	//	print '</p>' . "\n";
	//	print 'user object: <br />' . "\n";
	//	print_r( $user );
	//	print '  <p class="joomoouser">';

	if ( $user->get('guest') )
	{
		if ( 0 < $user->id )
		{
			print 'You have already created an account for ' . $user->username . '/' . $user->name . '.';
			$userConfig = &JComponentHelper::getParams( 'com_users' );
			if ( $userConfig->get('useractivation') )
			{
				print '</p>' . "\n";
				print '  <p class="joomoouser">';
				print 'Check your email and click the link to activate your new account!!';
			}
			else
			{
				print '</p>' . "\n";
				print '  <p class="joomoouser">';
				print 'You should be able to log in at this time!';
			}
			return FALSE;
		}
	}
	else
	{
		print 'You are already logged in as "' . $user->username . '" ("' . $user->name . '").';
		print '</p>' . "\n";
		print '  <p class="joomoouser">';
		print 'If you want to create a new account, please log out first.';
		return FALSE;
	}

	return TRUE;
}
?>

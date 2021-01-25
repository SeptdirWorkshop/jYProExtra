<?php
/**
 * @package    jYProExtra System Plugin
 * @version    __DEPLOY_VERSION__
 * @author     Septdir Workshop - www.septdir.com
 * @copyright  Copyright (c) 2018 - 2021 Septdir Workshop. All rights reserved.
 * @license    GNU/GPL license: https://www.gnu.org/copyleft/gpl.html
 * @link       https://www.septdir.com/
 */

defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;

extract($displayData);

/**
 * Layout variables
 * -----------------
 *
 * @var  string       $customizer YOOtheme customizer link.
 * @var  string|false $builder    YOOtheme builder link.
 * @var  string|false $admin      Item in control panel link.
 * @var  string       $position   Toolbar position.
 *
 */

$pos = '';
if (preg_match('#left#', $position))
{
	$pos = 'right';
}
elseif (preg_match('#right#', $position))
{
	$pos = 'left';
}
elseif (preg_match('#top#', $position))
{
	$pos = 'bottom';
}
elseif (preg_match('#bootom#', $position))
{
	$pos = 'top';
}

$center = (preg_match('#-center#', $position))
?>
<div id="jYProExtraToolbar"
	 class="uk-position-fixed uk-position-small uk-position-<?php echo $position; ?> <?php echo ($center) ? 'uk-flex' : ''; ?>">
	<?php if ($builder) : ?>
		<div class="uk-margin-small-<?php echo ($center) ? 'right' : 'bottom'; ?>">
			<a href="<?php echo $builder; ?>" class="uk-icon-button uk-text-danger"
			   uk-icon="icon:uikit; ratio: 1.2" uk-tooltip="<?php echo ($pos) ? 'pos:' . $pos : ''; ?>"
			   title="<?php echo Text::_('PLG_SYSTEM_JYPROEXTRA_TOOLBAR_BUILDER'); ?>">
			</a>
		</div>
	<?php endif; ?>
	<?php if ($admin) : ?>
		<div class="uk-margin-small-<?php echo ($center) ? 'right' : 'bottom'; ?>">
			<a href="<?php echo $admin; ?>" class="uk-icon-button uk-text-danger" target="_blank"
			   uk-icon="icon:joomla; ratio: 1.2" uk-tooltip="<?php echo ($pos) ? 'pos:' . $pos : ''; ?>"
			   title="<?php echo Text::_('PLG_SYSTEM_JYPROEXTRA_TOOLBAR_ADMIN'); ?>">
			</a>
		</div>
	<?php endif; ?>
	<div>
		<a href="<?php echo $customizer; ?>" class="uk-icon-button uk-text-danger"
		   uk-icon="icon:settings; ratio: 1.2" uk-tooltip="<?php echo ($pos) ? 'pos:' . $pos : ''; ?>"
		   title="<?php echo Text::_('PLG_SYSTEM_JYPROEXTRA_TOOLBAR_CUSTOMIZER'); ?>">
		</a>
	</div>
</div>
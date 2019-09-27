<?php
/**
 * @package    jYProExtra System Plugin
 * @version    __DEPLOY_VERSION__
 * @author     Septdir Workshop - www.septdir.com
 * @copyright  Copyright (c) 2018 - 2019 Septdir Workshop. All rights reserved.
 * @license    GNU/GPL license: https://www.gnu.org/copyleft/gpl.html
 * @link       https://www.septdir.com/
 */

defined('_JEXEC') or die;

extract($displayData);

/**
 * Layout variables
 * -----------------
 *
 * @var  string       $customizer YOOtheme customizer link.
 * @var  string|false $builder    YOOtheme builder link.
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
?>
<div class="uk-position-fixed uk-position-small uk-position-<?php echo $position; ?>">
	<div>
		<a href="<?php echo $customizer; ?>" class="uk-icon-button uk-text-danger"
		   uk-tooltip="<?php echo ($pos) ? 'pos:' . $pos : ''; ?>" title="Customizer" uk-icon="icon:cog; ratio: 1.2">
		</a>
	</div>
	<?php if ($builder) : ?>
		<div class="uk-margin-small-top">
			<a href="<?php echo $builder; ?>" class="uk-icon-button uk-text-danger"
			   uk-tooltip="<?php echo ($pos) ? 'pos:' . $pos : ''; ?>" title="Builder" uk-icon="icon:nut; ratio: 1.2">
			</a>
		</div>
	<?php endif; ?>
</div>
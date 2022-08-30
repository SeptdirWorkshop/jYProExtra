<?php
/**
 * @package    jYProExtra System Plugin
 * @version    1.8.1
 * @author     Septdir Workshop - www.septdir.com
 * @copyright  Copyright (c) 2018 - 2021 Septdir Workshop. All rights reserved.
 * @license    GNU/GPL license: https://www.gnu.org/copyleft/gpl.html
 * @link       https://www.septdir.com/
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Version;

if ((new Version())->isCompatible('4.0'))
{
	Factory::getDocument()->addStyleDeclaration('
		a[href="https://www.septdir.com/donate#solution=jyproextra"]:before{display:none;};
	');
}
?>
<p>
	<?php echo Text::_('PLG_SYSTEM_JYPROEXTRA_DONATE_MESSAGE'); ?>
</p>
<div>
	<a href="https://www.septdir.com/donate#solution=jyproextra" class="btn btn-primary"
	   target="_blank">
		<?php echo Text::_('PLG_SYSTEM_JYPROEXTRA_DONATE'); ?>
	</a>
</div>
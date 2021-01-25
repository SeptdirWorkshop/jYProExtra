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
use Joomla\CMS\Plugin\PluginHelper;

$src = 'index.php?option=com_plugins&task=plugin.edit&tmpl=component&layout=modal&extension_id='
	. PluginHelper::getPlugin('system', 'jyproextra')->id;
?>
<div id="jYProExtraModal" class="uk-modal-container" uk-modal>
	<div class="uk-modal-dialog">
		<div class="uk-modal-header">
			<h2 class="uk-modal-title"><?php echo Text::_('PLG_SYSTEM_JYPROEXTRA_MODAL_HEADER'); ?></h2>
		</div>
		<div uk-overflow-auto>
			<iframe data-src="<?php echo $src; ?>"
					data-import-message="<?php echo Text::_('PLG_SYSTEM_JYPROEXTRA_LIBRARY_IMPORT_SUCCESS_MODAL'); ?>"
					uk-responsive style="height: 500px; width: 100%;"></iframe>
		</div>
		<div class="uk-modal-footer uk-text-right">
			<button class="uk-button uk-button-text uk-margin-small-right uk-modal-close">
				<?php echo Text::_('JCANCEL'); ?>
			</button>
			<button type="button" class="uk-button uk-button-primary button-save">
				<?php echo Text::_('JAPPLY'); ?>
			</button>
		</div>
	</div>
</div>
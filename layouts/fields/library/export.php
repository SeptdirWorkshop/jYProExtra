<?php
/**
 * @package    jYProExtra System Plugin
 * @version    1.6.1
 * @author     Septdir Workshop - www.septdir.com
 * @copyright  Copyright (c) 2018 - 2020 Septdir Workshop. All rights reserved.
 * @license    GNU/GPL license: https://www.gnu.org/copyleft/gpl.html
 * @link       https://www.septdir.com/
 */

defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Router\Route;

HTMLHelper::script('plg_system_jyproextra/library.min.js', array('version' => 'auto', 'relative' => true));

?>
<div library-export="container"
	 data-url="<?php echo Route::_('index.php?option=com_ajax&plugin=jyproextra&group=system&action=libraryExport&format=raw&keys='); ?>">
	<p>
		<?php echo LayoutHelper::render('joomla.form.field.checkboxes', $displayData); ?>
	</p>
	<p>
		<a library-export="button" class="btn btn-primary">
			<?php echo Text::_('PLG_SYSTEM_JYPROEXTRA_LIBRARY_EXPORT_SUBMIT'); ?>
		</a>
	</p>
</div>

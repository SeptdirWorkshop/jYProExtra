<?php
/**
 * @package    jYProExtra System Plugin
 * @version    1.7.0
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
<div library-import="container"
	 data-url="<?php echo Route::_('index.php?option=com_ajax&plugin=jyproextra&group=system&action=libraryImport&format=json'); ?>">
	<p>
		<?php echo LayoutHelper::render('joomla.form.field.file', $displayData); ?>
	</p>
	<p>
		<a library-import="button" class="btn btn-success">
			<?php echo Text::_('PLG_SYSTEM_JYPROEXTRA_LIBRARY_IMPORT_SUBMIT'); ?>
		</a>
	</p>
</div>

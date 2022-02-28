<?php
/*
 * @package    jYProExtra System Plugin
 * @version    1.8.0
 * @author     Septdir Workshop - septdir.com
 * @copyright  Copyright (c) 2018 - 2022 Septdir Workshop. All rights reserved.
 * @license    GNU/GPL license: https://www.gnu.org/copyleft/gpl.html
 * @link       https://www.septdir.com/
 */

defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

extract($displayData);

/**
 * Layout variables
 * -----------------
 *
 * @var  string  $link Button link.
 * @var  string  $text Button text.
 * @var  string  $icon Button icon.
 * @var  boolean $new  Button target.
 * @var  string  $id   Button id.
 *
 */

$new = (isset($new)) ? $new : true;
?>
<a href="<?php echo Route::_($link); ?>" class="btn btn-small"<?php echo ($new) ? ' target="_blank"' : ''; ?>>
	<span aria-hidden="true" class="icon-<?php echo $icon; ?>"></span>
	<?php echo Text::_($text); ?>
</a>
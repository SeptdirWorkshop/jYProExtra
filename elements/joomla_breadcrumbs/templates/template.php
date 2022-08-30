<?php
/**
 * @package    jYProExtra System Plugin
 * @version    1.8.1
 * @author     Septdir Workshop - www.septdir.com
 * @copyright  Copyright (c) 2018 - 2021 Septdir Workshop. All rights reserved.
 * @license    GNU/GPL license: https://www.gnu.org/copyleft/gpl.html
 * @link       https://www.septdir.com/
 */

$el = $this->el('div', [
	'class' => [
		'uk-panel {@!style}',
		'uk-card uk-card-body uk-{style}',
	],

]);
?>

<?= $el($props, $attrs) ?>
{jyproextra_joomla_breadcrumbs}
</div>
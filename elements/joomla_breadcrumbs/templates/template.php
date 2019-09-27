<?php
/**
 * @package    jYProExtra System Plugin
 * @version    __DEPLOY_VERSION__
 * @author     Septdir Workshop - www.septdir.com
 * @copyright  Copyright (c) 2018 - 2019 Septdir Workshop. All rights reserved.
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
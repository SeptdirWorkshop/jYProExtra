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

use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Version;

$version = (((new Version())->isCompatible('4.0'))) ? 'joomla4' : 'joomla3';

echo LayoutHelper::render('plugins.system.jyproextra.toolbar.link.' . $version, $displayData);
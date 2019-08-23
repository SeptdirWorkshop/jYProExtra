<?php
/**
 * @package    jYProExtra System Plugin
 * @version    1.2.0
 * @author     Septdir Workshop - www.septdir.com
 * @copyright  Copyright (c) 2018 - 2019 Septdir Workshop. All rights reserved.
 * @license    GNU/GPL license: https://www.gnu.org/copyleft/gpl.html
 * @link       https://www.septdir.com/
 */

defined('_JEXEC') or die;

/**
 * Templates variables.
 * -----------------
 *
 * @var  string|array $url   Image url parameters.
 * @var  array        $attrs Image attributes.
 */

$attrs = (!empty($attrs)) ? $attrs : array();
if (!empty($url))
{
	echo $this->image($url, $attrs);
}
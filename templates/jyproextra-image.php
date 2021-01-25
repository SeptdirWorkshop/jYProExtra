<?php
/**
 * @package    jYProExtra System Plugin
 * @version    1.7.1
 * @author     Septdir Workshop - www.septdir.com
 * @copyright  Copyright (c) 2018 - 2021 Septdir Workshop. All rights reserved.
 * @license    GNU/GPL license: https://www.gnu.org/copyleft/gpl.html
 * @link       https://www.septdir.com/
 */

defined('_JEXEC') or die;

/**
 * Templates variables.
 * -----------------
 * @var  string       $src    Image source.
 * @var  int          $width  Image width.
 * @var  int          $height Image height.
 * @var  array        $attrs  Image attributes.
 * @var  string|array $url    Legacy image url parameters.
 */

// Legacy convert
if (!empty($url))
{
	if (is_array($url))
	{
		$src = $url[0];
		if (isset($url['thumbnail']))
		{
			$width  = (isset($url['thumbnail'][0])) ? $url['thumbnail'][0] : '';
			$height = (isset($url['thumbnail'][1])) ? $url['thumbnail'][1] : '';
		}
	}
	else
	{
		$src = $url;
	}
}

// Check params
if (empty($src)) return;
if (!isset($width)) $width = '';
if (!isset($height)) $height = '';
if (!isset($attrs)) $attrs = array();
$attrs['uk-img'] = true;
if ($this->isImage($src) === 'gif') $attrs['uk-gif'] = true;

// Prepare params
if ($this->isImage($src) === 'svg')
{
	$url   = $src;
	$attrs = array_merge($attrs, compact('width', 'height'));
}
else
{
	$url = array($src);
	if ($width || $height)
	{
		$url['thumbnail'] = array($width, $height);
	}
	$url['srcset'] = true;
}

// Display image
echo $this->image($url, $attrs);
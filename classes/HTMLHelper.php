<?php
/**
 * @package    jYProExtra System Plugin
 * @version    __DEPLOY_VERSION__
 * @author     Septdir Workshop - septdir.com
 * @copyright  Copyright (c) 2018 - 2021 Septdir Workshop. All rights reserved.
 * @license    GNU/GPL license: https://www.gnu.org/copyleft/gpl.html
 * @link       https://www.septdir.com/
 */

namespace Joomla\CMS\HTML;

defined('_JEXEC') or die;

use Joomla\CMS\Environment\Browser;
use Joomla\CMS\Factory;
use Joomla\CMS\Filesystem\File;
use Joomla\CMS\Uri\Uri;

abstract class HTMLHelper extends HTMLHelperCore
{
	/**
	 * Compute the files to be included.
	 *
	 * @param   string   $folder          Folder name to search in (i.e. images, css, js).
	 * @param   string   $file            Path to file.
	 * @param   boolean  $relative        Flag if the path to the file is relative to the /media folder (and searches in template).
	 * @param   boolean  $detect_browser  Flag if the browser should be detected to include specific browser files.
	 * @param   boolean  $detect_debug    Flag if debug mode is enabled to include uncompressed files if debug is on.
	 *
	 * @return  array    files to be included.
	 *
	 * @since   1.6.0
	 */
	protected static function includeRelativeFiles($folder, $file, $relative, $detectBrowser, $detectDebug)
	{
		if (!defined('YOOTHEME_CHILD'))
		{
			return parent::includeRelativeFiles($folder, $file, $relative, $detectBrowser, $detectDebug);
		}

		$app      = Factory::getApplication();
		$template = $app->getTemplate(true);
		$source   = clone $template;

		$template->template = 'yootheme_' . YOOTHEME_CHILD;
		$template->parent   = 'yootheme';
		$app->set('template', $template);

		$result = parent::includeRelativeFiles($folder, $file, $relative, $detectBrowser, $detectDebug);

		$app->set('template', $source);

		return $result;
	}

	/**
	 * Method that searches if file exists in given path and returns the relative path. If a minified version exists it will be preferred.
	 *
	 * @param   string   $path       The actual path of the file
	 * @param   string   $ext        The extension of the file
	 * @param   boolean  $debugMode  Signifies if debug is enabled
	 *
	 * @return  string  The relative path of the file
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	protected static function addFileToBuffer($path = '', $ext = '', $debugMode = false)
	{
		$result = parent::addFileToBuffer($path, $ext, $debugMode);
		if (empty($result) && strpos($path, 'media/templates/site/yootheme') !== false)
		{
			$path   = str_replace('media/templates/site/', 'templates/', $path);
			$result = parent::addFileToBuffer($path, $ext, $debugMode);
		}

		return $result;
	}
}
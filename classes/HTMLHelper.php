<?php
/**
 * @package    jYProExtra System Plugin
 * @version    1.8.1
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
	protected static function includeRelativeFiles($folder, $file, $relative, $detect_browser, $detect_debug)
	{
		// Get defaults include paths
		$default = parent::includeRelativeFiles($folder, $file, $relative, $detect_browser, $detect_debug);

		// If don't child return default
		if (!defined('YOOTHEME_CHILD')) return $default;

		// If http is present return default path
		if (strpos($file, 'http') === 0 || strpos($file, '//') === 0) return $default;

		// If don't relative return default path
		if (!$relative) return $default;

		// Prepare array of files
		$includes = array();

		// Extract extension and strip the file
		$strip = File::stripExt($file);
		$ext   = File::getExt($file);

		// Detect browser and compute potential files
		if ($detect_browser)
		{
			$navigator = Browser::getInstance();
			$browser   = $navigator->getBrowser();
			$major     = $navigator->getMajor();
			$minor     = $navigator->getMinor();

			// Try to include files named filename.ext, filename_browser.ext, filename_browser_major.ext, filename_browser_major_minor.ext
			// where major and minor are the browser version names
			$potential = array(
				$strip,
				$strip . '_' . $browser,
				$strip . '_' . $browser . '_' . $major,
				$strip . '_' . $browser . '_' . $major . '_' . $minor,
			);
		}
		else
		{
			$potential = array($strip);
		}

		// Get the template
		$template = 'yootheme_' . YOOTHEME_CHILD;

		// For each potential files
		foreach ($potential as $strip)
		{
			$files = array();

			// Detect debug mode
			if ($detect_debug && Factory::getConfig()->get('debug'))
			{
				/*
				 * Detect if we received a file in the format name.min.ext
				 * If so, strip the .min part out, otherwise append -uncompressed
				 */
				if (strlen($strip) > 4 && preg_match('#\.min$#', $strip))
				{
					$files[] = preg_replace('#\.min$#', '.', $strip) . $ext;
				}
				else
				{
					$files[] = $strip . '-uncompressed.' . $ext;
				}
			}

			$files[] = $strip . '.' . $ext;

			/*
			 * Loop on 1 or 2 files and break on first found.
			 * Add the content of the MD5SUM file located in the same folder to URL to ensure cache browser refresh
			 * This MD5SUM file must represent the signature of the folder content
			 */
			foreach ($files as $file)
			{
				// If the file is in the template folder
				$path = JPATH_THEMES . "/$template/$folder/$file";
				if (file_exists($path))
				{
					$includes[] = Uri::base(true) . "/templates/$template/$folder/$file" . static::getMd5Version($path);

					break;
				}
				else
				{
					// If the file contains any /: it can be in a media extension subfolder
					if (strpos($file, '/'))
					{
						// Divide the file extracting the extension as the first part before /
						list($extension, $file) = explode('/', $file, 2);

						// If the file yet contains any /: it can be a plugin
						if (strpos($file, '/'))
						{
							// Divide the file extracting the element as the first part before /
							list($element, $file) = explode('/', $file, 2);

							// Try to deal with system files in the template folder
							$path = JPATH_THEMES . "/$template/$folder/system/$element/$file";
							if (file_exists($path))
							{
								$includes[] = Uri::root(true) . "/templates/$template/$folder/system/$element/$file" . static::getMd5Version($path);

								break;
							}
						}
						else
						{
							// Try to deal with system files in the template folder
							$path = JPATH_THEMES . "/$template/$folder/system/$file";

							if (file_exists($path))
							{
								$includes[] = Uri::root(true) . "/templates/$template/$folder/system/$file" . static::getMd5Version($path);

								break;
							}
						}
					}
				}
			}
		}

		return (!empty($includes)) ? $includes : $default;
	}
}
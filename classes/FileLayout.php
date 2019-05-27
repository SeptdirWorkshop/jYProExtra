<?php
/**
 * @package    Joomla YooThemePro Extra System Plugin
 * @version    1.1.1
 * @author     Septdir Workshop - www.septdir.com
 * @copyright  Copyright (c) 2018 - 2019 Septdir Workshop. All rights reserved.
 * @license    GNU/GPL license: https://www.gnu.org/copyleft/gpl.html
 * @link       https://www.septdir.com/
 */

namespace Joomla\CMS\Layout;

defined('_JEXEC') or die;

class FileLayout extends FileLayoutCore
{
	/**
	 * Get the active include paths.
	 *
	 * @return  array Layout paths.
	 *
	 * @since  1.0.0
	 */
	public function getIncludePaths()
	{
		if (!defined('YOOTHEME_CHILD'))
		{
			return parent::getIncludePaths();
		}

		if ($default = parent::getIncludePaths())
		{
			$paths = array();
			foreach ($default as $path)
			{
				if (preg_match('/templates\/yootheme\//', $path))
				{
					$paths[] = str_replace('templates/yootheme/', 'templates/yootheme_' . YOOTHEME_CHILD . '/', $path);
				}
				$paths[] = $path;
			}
		}
		else
		{
			$paths = $default;
		}

		$this->setIncludePaths($paths);

		return $paths;
	}
}
<?php
/**
 * @package    jYProExtra System Plugin
 * @version    __DEPLOY_VERSION__
 * @author     Septdir Workshop - www.septdir.com
 * @copyright  Copyright (c) 2018 - 2019 Septdir Workshop. All rights reserved.
 * @license    GNU/GPL license: https://www.gnu.org/copyleft/gpl.html
 * @link       https://www.septdir.com/
 */

namespace Joomla\CMS\Layout;

defined('_JEXEC') or die;

use Joomla\CMS\Filesystem\Path;

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
				// Add child
				if (preg_match('#(.*)[\\\/]templates[\\\/]yootheme[\\\/](.*)$#', $path, $matches))
				{
					$child = Path::clean($matches[1] . '/templates/yootheme_' . YOOTHEME_CHILD . '/' . $matches[2]);
					if (!in_array($child, $paths))
					{
						$paths[] = $child;
					}
				}
				$paths[] = $path;
			}

			// Clean layout paths
			$paths = array_unique($paths);
		}
		else
		{
			$paths = $default;
		}

		$this->setIncludePaths($paths);

		return $paths;
	}
}
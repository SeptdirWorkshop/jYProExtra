<?php
/**
 * @package    jYProExtra System Plugin
 * @version    __DEPLOY_VERSION__
 * @author     Septdir Workshop - www.septdir.com
 * @copyright  Copyright (c) 2018 - 2019 Septdir Workshop. All rights reserved.
 * @license    GNU/GPL license: https://www.gnu.org/copyleft/gpl.html
 * @link       https://www.septdir.com/
 */

namespace Joomla\CMS\Helper;

defined('_JEXEC') or die;

abstract class ModuleHelper extends ModuleHelperCore
{
	/**
	 * Get the path to a layout for a module.
	 *
	 * @param   string  $module  The name of the module
	 * @param   string  $layout  The name of the module layout.
	 *
	 * @return  string  The path to the module layout.
	 *
	 * @since  1.0.0
	 */
	public static function getLayoutPath($module, $layout = 'default')
	{
		if (!defined('YOOTHEME_CHILD'))
		{
			return parent::getLayoutPath($module, $layout);
		}

		$template      = 'yootheme';
		$child         = $template . '_' . YOOTHEME_CHILD;
		$defaultLayout = $layout;

		if (strpos($layout, ':') !== false)
		{
			// Get the template and file name from the string
			$temp          = explode(':', $layout);
			$template      = $temp[0] === '_' ? $template : $temp[0];
			$layout        = $temp[1];
			$defaultLayout = $temp[1] ?: 'default';
		}

		// Build the template and base path for the layout
		$cPath = JPATH_THEMES . '/' . $child . '/html/' . $module . '/' . $layout . '.php';
		$tPath = JPATH_THEMES . '/' . $template . '/html/' . $module . '/' . $layout . '.php';
		$bPath = JPATH_BASE . '/modules/' . $module . '/tmpl/' . $defaultLayout . '.php';
		$dPath = JPATH_BASE . '/modules/' . $module . '/tmpl/default.php';

		// If the template has a layout override use it
		if (file_exists($cPath))
		{
			return $cPath;
		}

		if (file_exists($tPath))
		{
			return $tPath;
		}

		if (file_exists($bPath))
		{
			return $bPath;
		}

		return $dPath;
	}
}
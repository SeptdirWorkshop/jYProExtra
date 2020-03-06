<?php
/**
 * @package    jYProExtra System Plugin
 * @version    __DEPLOY_VERSION__
 * @author     Septdir Workshop - septdir.com
 * @copyright  Copyright (c) 2018 - 2020 Septdir Workshop. All rights reserved.
 * @license    GNU/GPL license: https://www.gnu.org/copyleft/gpl.html
 * @link       https://www.septdir.com/
 */

namespace Joomla\CMS\MVC\Controller;

defined('JPATH_PLATFORM') or die;

class BaseController extends BaseControllerCore
{
	/**
	 * Typical view method for MVC based architecture.
	 *
	 * @param   boolean  $cachable   If true, the view output will be cached.
	 * @param   array    $urlparams  An array of safe URL parameters and their variable types.
	 *
	 * @return  \JControllerLegacy  A \JControllerLegacy object to support chaining.
	 *
	 * @since  1.5.0
	 */
	public function display($cachable = false, $urlparams = array())
	{
		// Add webP support cache params
		if ($cachable)
		{
			$webP = false;

			// Check webP support
			if ($agent = $_SERVER['HTTP_USER_AGENT'])
			{
				preg_match('/(Android)(?:\'&#x20;| )([0-9.]+)/', $agent, $Android);
				preg_match('/(Version)(?:\/| )([0-9.]+)/', $agent, $Safari);
				preg_match('/(OPR)(?:\/| )([0-9.]+)/', $agent, $Opera);
				preg_match('/(Edge)(?:\/| )([0-9.]+)/', $agent, $Edge);
				preg_match('/(Trident)(?:\/| )([0-9.]+)/', $agent, $IE);
				preg_match('/(rv)(?:\:| )([0-9.]+)/', $agent, $rv);
				preg_match('/(MSIE|Opera|Firefox|Chrome|Chromium|YandexSearch|YaBrowser)(?:\/| )([0-9.]+)/', $agent, $bi);

				$isAndroid = isset($Android[1]);
				$isWin10   = strpos($agent, 'Windows NT 10.0') !== false;

				if ($Safari && !$isAndroid)
				{
					$name    = 'Safari';
					$version = (int) $Safari[2];
				}
				elseif ($Opera)
				{
					$name    = 'Opera';
					$version = (int) $Opera[2];
				}
				elseif ($Edge)
				{
					$name    = 'Edge';
					$version = (int) $Edge[2];
				}
				elseif ($IE)
				{
					$name    = 'IE';
					$version = isset($rv[2]) ? (int) $rv[2] : ($isWin10 ? 11 : (int) $IE[2]);
				}
				else
				{
					$name    = isset($bi[1]) ? $bi[1] : ($isAndroid ? 'Android' : 'Unknown');
					$version = isset($bi[2]) ? (int) $bi[2] : ($isAndroid ? (float) $Android[2] : 0);
				}
				$supported = array(
					'Chrome'       => 32,
					'Firefox'      => 65,
					'Opera'        => 19,
					'Edge'         => 18,
					'YaBrowser'    => 1,
					'YandexSearch' => 1,
					'Android'      => 4.2
				);
				$webP      = in_array($name, array_keys($supported)) && ($version >= $supported[$name]);
			}

			// Set params url params
			$this->input->set('webp_support', ($webP) ? 1 : 0);
			$urlparams['webp_support'] = 'INT';
		}

		return parent::display($cachable, $urlparams);
	}
}


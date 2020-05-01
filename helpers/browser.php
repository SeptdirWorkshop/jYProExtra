<?php
/**
 * @package    jYProExtra System Plugin
 * @version    __DEPLOY_VERSION__
 * @author     Septdir Workshop - septdir.com
 * @copyright  Copyright (c) 2018 - 2020 Septdir Workshop. All rights reserved.
 * @license    GNU/GPL license: https://www.gnu.org/copyleft/gpl.html
 * @link       https://www.septdir.com/
 */

defined('_JEXEC') or die;

class jYProExtraHelperBrowser
{
	/**
	 * Browser data.
	 *
	 * @var  array
	 *
	 * @since  __DEPLOY_VERSION__
	 */
	protected static $_browser = null;

	/**
	 * Browser function support.
	 *
	 * @var  boolean
	 *
	 * @since  __DEPLOY_VERSION__
	 */
	protected static $_supported = null;

	/**
	 * Method to get user browser data.
	 *
	 * @return  array[name,version]  Browser data.
	 *
	 * @since  __DEPLOY_VERSION__
	 */
	public static function getBrowser()
	{
		if (self::$_browser === null)
		{
			$name    = 'Unknown';
			$version = 0;
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
			}

			self::$_browser = array('name' => $name, 'version' => $version);
		}

		return self::$_browser;
	}

	/**
	 * Method to check browser function support.
	 *
	 * @param   null   $key        Function name.
	 * @param   array  $supported  Versions array[name =>version].
	 *
	 * @return  bool  True if supported, False if note.
	 *
	 * @since  __DEPLOY_VERSION__
	 */
	public static function supported($key = null, $supported = array())
	{
		if (self::$_supported === null) self::$_supported = array();
		if (empty($key)) return false;

		if (!isset(self::$_supported[$key]))
		{
			$result = false;
			if (!empty($supported))
			{
				$browser = self::getBrowser();
				$name    = $browser['name'];
				$version = $browser['version'];
				$result  = in_array($name, array_keys($supported)) && ($version >= $supported[$name]);
			}

			self::$_supported[$key] = $result;
		}

		return self::$_supported[$key];
	}
}
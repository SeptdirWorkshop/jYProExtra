<?php
/**
 * @package    jYProExtra System Plugin
 * @version    1.6.0
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
			\JLoader::register('jYProExtraHelperBrowser', JPATH_PLUGINS . '/system/jyproextra/helpers/browser.php');
			$webP = \jYProExtraHelperBrowser::supported('webp', array(
				'Chrome'       => 32,
				'Firefox'      => 65,
				'Opera'        => 19,
				'Edge'         => 18,
				'YaBrowser'    => 1,
				'YandexSearch' => 1,
				'Android'      => 4.2
			));

			// Set params url params
			$this->input->set('webp_support', ($webP) ? 1 : 0);
			$urlparams['webp_support'] = 'INT';
		}

		return parent::display($cachable, $urlparams);
	}
}


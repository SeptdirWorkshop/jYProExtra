<?php
/**
 * @package    Joomla YooThemePro Extra System Plugin
 * @version    __DEPLOY_VERSION__
 * @author     Septdir Workshop - www.septdir.com
 * @copyright  Copyright (c) 2018 - 2019 Septdir Workshop. All rights reserved.
 * @license    GNU/GPL license: https://www.gnu.org/copyleft/gpl.html
 * @link       https://www.septdir.com/
 */

namespace Joomla\CMS\MVC\View;

defined('_JEXEC') or die;

class HtmlView extends HtmlViewCore
{
	/**
	 * Constructor.
	 *
	 * @param   array  $config  A named configuration array for object construction.
	 *
	 * @since  1.0.0
	 */
	public function __construct($config = array())
	{
		parent::__construct($config);

		if (defined('YOOTHEME_CHILD'))
		{
			foreach ($this->_path['template'] as $path)
			{
				if (preg_match('/templates\/yootheme\//', $path))
				{
					$child = str_replace('templates/yootheme/', 'templates/yootheme_' . YOOTHEME_CHILD . '/', $path);
					if (!in_array($child, $this->_path['template']))
					{
						$this->_addPath('template', $child);
					}
				}
			}
		}
	}
}
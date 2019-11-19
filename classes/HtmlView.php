<?php
/**
 * @package    jYProExtra System Plugin
 * @version    __DEPLOY_VERSION__
 * @author     Septdir Workshop - www.septdir.com
 * @copyright  Copyright (c) 2018 - 2019 Septdir Workshop. All rights reserved.
 * @license    GNU/GPL license: https://www.gnu.org/copyleft/gpl.html
 * @link       https://www.septdir.com/
 */

namespace Joomla\CMS\MVC\View;

defined('_JEXEC') or die;

use Joomla\CMS\Filesystem\Path;

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
				// Add child
				if (preg_match('#(.*)[\\\/]templates[\\\/]yootheme[\\\/](.*)$#', $path, $matches))
				{
					$child = Path::clean($matches[1] . '/templates/yootheme_' . YOOTHEME_CHILD . '/' . $matches[2]);
					if (!in_array($child, $this->_path['template']))
					{
						$this->_addPath('template', $child);
					}
				}
			}
		}

		// Clean template paths
		$this->_path['template'] = array_unique($this->_path['template']);
	}

	/**
	 * Sets the layout name to use.
	 *
	 * @param   string  $layout  The layout name or a string in format <template>:<layout file>
	 *
	 * @return  string  Previous value.
	 *
	 * @since   1.2.0
	 */
	public function setLayout($layout)
	{
		// Fix yootheme child layout
		if (preg_match('#^yootheme_(.*):(.*)#', $layout, $matches))
		{
			$layout = 'yootheme:' . $matches[2];
		}

		return  parent::setLayout($layout);
	}
}
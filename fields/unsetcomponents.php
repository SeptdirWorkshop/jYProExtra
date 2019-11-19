<?php
/**
 * @package    jYProExtra System Plugin
 * @version    __DEPLOY_VERSION__
 * @author     Septdir Workshop - www.septdir.com
 * @copyright  Copyright (c) 2018 - 2019 Septdir Workshop. All rights reserved.
 * @license    GNU/GPL license: https://www.gnu.org/copyleft/gpl.html
 * @link       https://www.septdir.com/
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Filesystem\Folder;
use Joomla\CMS\Filesystem\Path;
use Joomla\CMS\Form\FormHelper;
use Joomla\CMS\Language\Text;

FormHelper::loadFieldClass('list');

class JFormFieldUnsetComponents extends JFormFieldList
{
	/**
	 * The form field type.
	 *
	 * @var  string
	 *
	 * @since  1.4.0
	 */
	protected $type = 'unsetComponents';

	/**
	 * Field options array.
	 *
	 * @var  array
	 *
	 * @since  1.4.0
	 */
	protected $_options = null;

	/**
	 * Method to get the field options.
	 *
	 * @throws  Exception
	 *
	 * @return  array  The field option objects.
	 *
	 * @since  1.4.0
	 */
	protected function getOptions()
	{
		if ($this->_options === null)
		{
			// Get components
			$db       = Factory::getDbo();
			$query    = $db->getQuery(true)
				->select(array('e.element'))
				->from($db->quoteName('#__extensions', 'e'))
				->where($db->quoteName('e.type') . '=' . $db->quote('component'))
				->where('e.enabled = 1');
			$elements = $db->setQuery($query)->loadColumn();

			$components = array();
			foreach ($elements as $component)
			{
				$folder = Path::clean(JPATH_ROOT . '/components/' . $component . '/views');
				if (Folder::exists($folder))
				{
					foreach (Folder::folders($folder) as $view)
					{
						if (!isset($views[$component]))
						{
							$views[$component] = array();
						}
						$components[$component][$view] = array();
					}
				}
			}

			// Convert options
			$options = parent::getOptions();
			foreach ($options as $key => $option)
			{
				if (!empty($option->value))
				{
					$explode   = explode('.', $option->value, 2);
					$component = (!empty($explode[0])) ? $explode[0] : false;
					$view      = (!empty($explode[1])) ? $explode[1] : false;
					if ($component && $view && in_array($component, $elements))
					{
						$explode = explode(':', $view, 2);
						$view    = $explode[0];
						$layout  = (!empty($explode[1])) ? $explode[1] : false;

						if (!isset($components[$component]))
						{
							$components[$component] = array();
						}
						if (!isset($components[$component][$view]))
						{
							$components[$component][$view] = array();
						}
						if ($layout && !in_array($layout, $components[$component][$view]))
						{
							$components[$component][$view][] = $layout;
						}

						unset($options[$key]);
					}
				}

				// Prepare options
				$pluginConstant = 'PLG_SYSTEM_JYPROEXTRA_MODULE_UNSET_COMPONENTS';
				$language       = Factory::getLanguage();
				foreach ($components as $component => $views)
				{
					$componentValue    = $component;
					$componentConstant = $pluginConstant . '_' . str_replace('com_', '', $component);
					$componentText     = ($language->hasKey($componentConstant)) ? Text::_($componentConstant) :
						ucfirst(str_replace('com_', '', $component));

					// Add views
					foreach ($views as $view => $layouts)
					{
						$viewValue    = $componentValue . '.' . $view;
						$viewConstant = $componentConstant . '_' . $view;
						$viewText = $componentText . ': ';
						$viewText .= ($language->hasKey($viewConstant)) ? Text::_($viewConstant) : ucfirst($view);

						$option        = new stdClass();
						$option->value = $viewValue;
						$option->text  = $viewText;
						$options[]     = $option;

						// Add layouts
						foreach ($layouts as $layout)
						{
							$layoutValue    = $viewValue . ':' . $layout;
							$layoutConstant = $viewConstant . '_' . $layout;
							$layoutText     = $viewText . ' (';
							$layoutText     .= ($language->hasKey($layoutConstant)) ? Text::_($layoutConstant) : ucfirst($layout);
							$layoutText     .= ')';

							$option        = new stdClass();
							$option->value = $layoutValue;
							$option->text  = $layoutText;
							$options[]     = $option;
						}
					}
				}
			}

			$this->_options = $options;
		}

		return $this->_options;
	}
}
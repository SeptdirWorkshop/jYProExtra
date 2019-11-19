<?php
/**
 * @package    jYProExtra System Plugin
 * @version    1.4.0
 * @author     Septdir Workshop - www.septdir.com
 * @copyright  Copyright (c) 2018 - 2019 Septdir Workshop. All rights reserved.
 * @license    GNU/GPL license: https://www.gnu.org/copyleft/gpl.html
 * @link       https://www.septdir.com/
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Form\FormHelper;

FormHelper::loadFieldClass('checkboxes');

class JFormFieldLibraryExport extends JFormFieldCheckboxes
{
	/**
	 * The form field type.
	 *
	 * @var  string
	 *
	 * @since   1.3.0
	 */
	protected $type = 'libraryExport';

	/**
	 * Field options array.
	 *
	 * @var  array
	 *
	 * @since  1.3.0
	 */
	protected $_options = null;

	/**
	 * Name of the layout being used to render the field.
	 *
	 * @var  string
	 *
	 * @since  1.3.0
	 */
	protected $layout = 'plugins.system.jyproextra.fields.library.export';

	/**
	 * Method to get the field options.
	 *
	 * @throws  Exception
	 *
	 * @return  array  The field option objects.
	 *
	 * @since  1.3.0
	 */
	protected function getOptions()
	{
		if ($this->_options === null)
		{
			// Get library
			$library = array();

			$db    = Factory::getDbo();
			$query = $db->getQuery(true)
				->select(array('e.custom_data'))
				->from($db->quoteName('#__extensions', 'e'))
				->where($db->quoteName('e.type') . ' = ' . $db->quote('plugin'))
				->where($db->quoteName('e.element') . ' = ' . $db->quote('yootheme'))
				->where($db->quoteName('e.folder') . ' = ' . $db->quote('system'));
			if ($custom_data = $db->setQuery($query)->loadResult())
			{
				$custom_data = json_decode($custom_data, true);
				$library     = (!empty($custom_data['library'])) ? $custom_data['library'] : array();
			}

			// Prepare options
			$options = parent::getOptions();
			foreach ($library as $key => $item)
			{
				$option          = new stdClass();
				$option->value   = $key;
				$option->text    = $item['name'];
				$option->checked = false;

				$options[] = $option;
			}

			$this->_options = $options;
		}

		return $this->_options;
	}
}
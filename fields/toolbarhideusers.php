<?php
/**
 * @package    jYProExtra System Plugin
 * @version    1.6.1
 * @author     Septdir Workshop - www.septdir.com
 * @copyright  Copyright (c) 2018 - 2020 Septdir Workshop. All rights reserved.
 * @license    GNU/GPL license: https://www.gnu.org/copyleft/gpl.html
 * @link       https://www.septdir.com/
 */

defined('_JEXEC') or die;

use Joomla\CMS\Access\Access;
use Joomla\CMS\Factory;
use Joomla\CMS\Form\FormHelper;
use Joomla\Utilities\ArrayHelper;

FormHelper::loadFieldClass('list');

class JFormFieldToolbarHideUsers extends JFormFieldList
{
	/**
	 * The form field type.
	 *
	 * @var  string
	 *
	 * @since  1.4.0
	 */
	protected $type = 'ToolbarHideUsers';

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
			// Get groups
			$db     = Factory::getDbo();
			$query  = $db->getQuery(true)
				->select(array('id', 'parent_id'))
				->from($db->quoteName('#__usergroups'));
			$rows   = $db->setQuery($query)->loadAssocList('id', 'parent_id');
			$rules  = Access::getAssetRules(1);
			$groups = array();
			foreach ($rows as $id => $parent_id)
			{
				$ids        = array($id);
				$parent_key = $parent_id;
				while (!empty($parent_key) && isset($rows[$parent_key]))
				{
					$ids[]      = $parent_key;
					$parent_key = $rows[$parent_key];
				}
				$ids = array_reverse($ids);

				if ($rules->allow('core.admin', $ids) || $rules->allow('core.login.admin', $ids))
				{
					$groups[] = $id;
				}
			}
			$groups = ArrayHelper::toInteger($groups);

			// Get users
			$query = $db->getQuery(true)
				->select(array('u.id', 'u.name'))
				->from($db->quoteName('#__users', 'u'))
				->leftJoin($db->quoteName('#__user_usergroup_map', 'm') . ' ON m.user_id = u.id')
				->where($db->quoteName('m.group_id') . ' IN (' . implode(',', $groups) . ')')
				->group(array('u.id'));
			$users = $db->setQuery($query)->loadAssocList('id', 'name');

			// Prepare options
			$options = parent::getOptions();
			foreach ($users as $id => $name)
			{
				$option        = new stdClass();
				$option->value = $id;
				$option->text  = '[' . $id . '] ' . $name;
				$options[]     = $option;
			}

			$this->_options = $options;
		}

		return $this->_options;
	}
}
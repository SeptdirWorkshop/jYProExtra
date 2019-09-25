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

use Joomla\CMS\Form\FormHelper;

FormHelper::loadFieldClass('file');

class JFormFieldLibraryImport extends JFormFieldFile
{
	/**
	 * The form field type.
	 *
	 * @var  string
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	protected $type = 'libraryImport';

	/**
	 * Name of the layout being used to render the field.
	 *
	 * @var  string
	 *
	 * @since  __DEPLOY_VERSION__
	 */
	protected $layout = 'plugins.system.jyproextra.fields.library.import';

	/**
	 * Method to get the field input markup for the file field.
	 *
	 * @return  string  The field input markup.
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	protected function getInput()
	{
		$this->__set('accept', '.json,application/json');

		return parent::getInput();
	}
}
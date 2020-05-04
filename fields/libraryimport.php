<?php
/**
 * @package    jYProExtra System Plugin
 * @version    1.6.0
 * @author     Septdir Workshop - www.septdir.com
 * @copyright  Copyright (c) 2018 - 2020 Septdir Workshop. All rights reserved.
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
	 * @since  1.3.0
	 */
	protected $type = 'libraryImport';

	/**
	 * Name of the layout being used to render the field.
	 *
	 * @var  string
	 *
	 * @since  1.3.0
	 */
	protected $layout = 'plugins.system.jyproextra.fields.library.import';

	/**
	 * Method to get the field input markup for the file field.
	 *
	 * @return  string  The field input markup.
	 *
	 * @since  1.3.0
	 */
	protected function getInput()
	{
		$this->__set('accept', '.json,application/json');

		return parent::getInput();
	}
}
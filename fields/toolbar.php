<?php
/**
 * @package    jYProExtra System Plugin
 * @version    1.7.0
 * @author     Septdir Workshop - www.septdir.com
 * @copyright  Copyright (c) 2018 - 2020 Septdir Workshop. All rights reserved.
 * @license    GNU/GPL license: https://www.gnu.org/copyleft/gpl.html
 * @link       https://www.septdir.com/
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Form\FormField;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Toolbar\Toolbar;

class JFormFieldToolbar extends FormField
{
	/**
	 * The form field type.
	 *
	 * @var string
	 *
	 * @since  1.2.0
	 */
	protected $type = 'toolbar';

	/**
	 * Method to add messages and .
	 *
	 * @throws  Exception
	 *
	 * @since  1.2.0
	 */
	protected function getInput()
	{
		$toolbar = Toolbar::getInstance('toolbar');

		// Add support button
		$toolbar->appendButton('Custom', LayoutHelper::render('plugins.system.jyproextra.toolbar.link', array(
			'link' => 'https://www.septdir.com/support#solution=jyproextra',
			'text' => 'PLG_SYSTEM_JYPROEXTRA_SUPPORT',
			'icon' => 'support'
		)), 'support');

		// Add donate message
		Factory::getApplication()->enqueueMessage(LayoutHelper::render('plugins.system.jyproextra.donate.message'), '');

		// Toolbar Style
		Factory::getDocument()->addStyleDeclaration('#toolbar-support{float: right;}');
	}
}
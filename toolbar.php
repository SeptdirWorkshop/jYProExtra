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
use Joomla\CMS\Form\FormField;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\FileLayout;
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
	 * @since   1.2.0
	 */
	protected function getInput()
	{
		$toolbar = Toolbar::getInstance('toolbar');

		// Add support button
		$link = 'https://www.septdir.com/support#solution=jyproextra';
		$toolbar->appendButton('Custom', $this->getButton($link, 'PLG_SYSTEM_JYPROEXTRA_SUPPORT', 'support'),
			'support');

		// Add donate message
		$message = new FileLayout('donate_message');
		$message->addIncludePath(__DIR__);
		Factory::getApplication()->enqueueMessage($message->render(), '');

		// Toolbar Style
		Factory::getDocument()->addStyleDeclaration('#toolbar-support{float: right;}');
	}

	/**
	 * Method to get toolbar button markup.
	 *
	 * @param   string  $link  The href attribute value.
	 * @param   string  $text  The button label.
	 * @param   string  $icon  The button label.
	 *
	 * @return  string Buttons markup string.
	 *
	 * @since  1.2.0
	 */
	protected function getButton($link = null, $text = null, $icon = null)
	{
		return '<a href="' . $link . '" class="btn btn-small" target="_blank">'
			. '<span aria-hidden="true" class="icon-' . $icon . '"></span>'
			. Text::_($text) . '</a>';
	}
}
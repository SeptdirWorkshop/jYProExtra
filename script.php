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
use Joomla\CMS\Filesystem\File;
use Joomla\CMS\Filesystem\Path;
use Joomla\CMS\Installer\InstallerAdapter;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\FileLayout;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Version;
use Joomla\Registry\Registry;

class PlgSystemJYProExtraInstallerScript
{
	/**
	 * Minimum PHP version required to install the extension.
	 *
	 * @var  string
	 *
	 * @since  1.0.0
	 */
	protected $minimumPhp = '7.0';

	/**
	 * Minimum Joomla version required to install the extension.
	 *
	 * @var  string
	 *
	 * @since  1.0.0
	 */
	protected $minimumJoomla = '3.9.0';

	/**
	 * Runs right before any installation action.
	 *
	 * @param   string  $type  Type of PostFlight action. Possible values are:
	 *
	 * @throws  Exception
	 *
	 * @return  boolean True on success, false on failure.
	 *
	 * @since  1.0.1
	 */
	function preflight($type)
	{
		// Check compatible
		if (!$this->checkCompatible()) return false;

		if ($type == 'update')
		{
			// Check update server
			$this->checkUpdateServer();

			// Check old config
			$this->checkOldConfig();
		}

		return true;
	}

	/**
	 * Method to check compatible.
	 *
	 * @throws  Exception
	 *
	 * @return  boolean True on success, false on failure.
	 *
	 * @since  1.0.0
	 */
	protected function checkCompatible()
	{
		// Check old joomla
		if (!class_exists('Joomla\CMS\Version'))
		{
			JFactory::getApplication()->enqueueMessage(JText::sprintf('PLG_SYSTEM_JYPROEXTRA_ERROR_COMPATIBLE_JOOMLA',
				$this->minimumJoomla), 'error');

			return false;
		}

		$app = Factory::getApplication();

		// Check php
		if (!(version_compare(PHP_VERSION, $this->minimumPhp) >= 0))
		{
			$app->enqueueMessage(Text::sprintf('PLG_SYSTEM_JYPROEXTRA_ERROR_COMPATIBLE_PHP', $this->minimumPhp),
				'error');

			return false;
		}

		// Check joomla version
		$jversion = new Version();
		if (!$jversion->isCompatible($this->minimumJoomla))
		{
			$app->enqueueMessage(Text::sprintf('PLG_SYSTEM_JYPROEXTRA_ERROR_COMPATIBLE_JOOMLA', $this->minimumJoomla),
				'error');

			return false;
		}

		return true;
	}

	/**
	 * Method to check update server and change if need.
	 *
	 * @since  1.1.0
	 */
	protected function checkUpdateServer()
	{

		$db       = Factory::getDbo();
		$contains = array(
			$db->quoteName('name') . ' = ' . $db->quote('Joomla YOOtheme Pro Extra'),
			$db->quoteName('location') . ' = ' . $db->quote('https://www.septdir.com/marketplace/joomla/update?element=plg_system_jyproextra'),
		);
		$query    = $db->getQuery(true)
			->select(array('update_site_id'))
			->from($db->quoteName('#__update_sites'))
			->where(implode(' OR ', $contains));
		$old      = $db->setQuery($query)->loadObject();
		if (!empty($old))
		{
			$new           = $old;
			$new->name     = 'jYProExtra';
			$new->location = 'https://www.septdir.com/solutions/joomla/update?element=plg_system_jyproextra';
			$db->updateObject('#__update_sites', $new, array('update_site_id'));
		}
	}

	/**
	 * Method to check plugin params and change if need.
	 *
	 * @since  1.2.0
	 */
	protected function checkOldConfig()
	{
		$plugin = PluginHelper::getPlugin('system', 'jyproextra');
		$params = new Registry($plugin->params);
		$update = false;

		// Check images
		if ($params->get('images_handler'))
		{
			$update = true;
			unset($params['images_handler']);
			$params->set('images', 1);
		}

		// Check child
		if ($params->get('child_layouts')
			|| $params->get('child_views')
			|| $params->get('child_modules')
			|| $params->get('child_languages'))
		{
			$update = true;
			$params->set('child', 1);
		}

		// Check scripts
		if ($params->get('scripts_remove_jquery')
			|| $params->get('scripts_remove_bootstrap')
			|| $params->get('scripts_remove_core')
			|| $params->get('scripts_remove_keepalive'))
		{
			$update = true;
			unset($params['scripts_remove_jquery']);
			unset($params['scripts_remove_bootstrap']);
			unset($params['scripts_remove_core']);
			unset($params['scripts_remove_keepalive']);
			$params->set('remove_js', 1);
		}

		// Check unset modules
		if (!$params->exists('unset_modules'))
		{
			$update = true;
			$params->set('unset_modules', 1);
		}

		// Update record
		if ($update)
		{
			$update          = new stdClass();
			$update->element = 'jyproextra';
			$update->folder  = 'system';
			$update->params  = $params->toString();

			Factory::getDbo()->updateObject('#__extensions', $update, array('element', 'folder'));
		}
	}

	/**
	 * Runs right after any installation action.
	 *
	 * @param   string            $type    Type of PostFlight action. Possible values are:
	 * @param   InstallerAdapter  $parent  Parent object calling object.
	 *
	 * @throws  Exception
	 *
	 * @return  boolean True on success, false on failure.
	 *
	 * @since  1.0.0
	 */
	function postflight($type, $parent)
	{
		$app = Factory::getApplication();
		if ($type == 'install')
		{
			// Enable plugin
			$this->enablePlugin($parent);

			// Add after install message
			$app->enqueueMessage(Text::_('PLG_SYSTEM_JYPROEXTRA_AFTER_INSTALL'), 'notice');
		}

		// Update files
		$this->updateFiles();

		// Add donate message
		$message = new FileLayout('donate_message');
		$message->addIncludePath(__DIR__);
		$app->enqueueMessage($message->render(), '');

		return true;
	}

	/**
	 * Method to update files.
	 *
	 * @since  1.0.0
	 */
	protected function updateFiles()
	{
		$files = array(
			__DIR__ . '/templates/jyproextra-image.php' => JPATH_ROOT . '/templates/yootheme/templates/jyproextra-image.php',
		);

		foreach ($files as $src => $dest)
		{
			$src  = Path::clean($src);
			$dest = Path::clean($dest);
			if (File::exists($dest))
			{
				File::delete($dest);
				File::copy($src, $dest);
			}
		}
	}

	/**
	 * Enable plugin after installation.
	 *
	 * @param   InstallerAdapter  $parent  Parent object calling object.
	 *
	 * @since   1.0.0
	 */
	protected function enablePlugin($parent)
	{
		// Prepare plugin object
		$plugin          = new stdClass();
		$plugin->type    = 'plugin';
		$plugin->element = $parent->getElement();
		$plugin->folder  = (string) $parent->getParent()->manifest->attributes()['group'];
		$plugin->enabled = 1;

		// Update record
		Factory::getDbo()->updateObject('#__extensions', $plugin, array('type', 'element', 'folder'));
	}

	/**
	 * This method is called after extension is uninstalled.
	 *
	 * @since  1.2.0
	 */
	public function uninstall()
	{
		// Remove files
		$this->removeFiles();
	}

	/**
	 * Method to remove files.
	 *
	 * @since  1.2.0
	 */
	protected function removeFiles()
	{
		$files = array(
			JPATH_ROOT . '/templates/yootheme/templates/jyproextra-image.php',
			JPATH_ROOT . '/templates/yootheme/html/pagination_all.php',
		);
		foreach ($files as $file)
		{
			$file = Path::clean($file);
			if (File::exists($file))
			{
				File::delete($file);
			}
		}
	}
}
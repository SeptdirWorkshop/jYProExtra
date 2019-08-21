<?php
/**
 * @package    Joomla YooThemePro Extra System Plugin
 * @version    __DEPLOY_VERSION__
 * @author     Septdir Workshop - www.septdir.com
 * @copyright  Copyright (c) 2018 - 2019 Septdir Workshop. All rights reserved.
 * @license    GNU/GPL license: https://www.gnu.org/copyleft/gpl.html
 * @link       https://www.septdir.com/
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Filesystem\File;
use Joomla\CMS\Filesystem\Folder;
use Joomla\CMS\Filesystem\Path;
use Joomla\CMS\Form\Form;
use Joomla\CMS\Helper\ModuleHelper;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\CMS\Uri\Uri;
use Joomla\Registry\Registry;

class PlgSystemJYProExtra extends CMSPlugin
{
	/**
	 * Affects constructor behavior.
	 *
	 * @var  boolean
	 *
	 * @since  1.0.0
	 */
	protected $autoloadLanguage = true;

	/**
	 * Set child constant and override classes.
	 *
	 * @throws  Exception
	 *
	 * @since  1.0.1
	 */
	public function onAfterInitialise()
	{
		$app = Factory::getApplication();
		if ($app->isClient('site'))
		{
			$template = $app->getTemplate();
			if ($template === 'yootheme')
			{
				$params = $app->getTemplate(true)->params->get('config');
				$params = new Registry($params);

				if ($child = $params->get('child_theme'))
				{
					// Set constant
					define('YOOTHEME_CHILD', $child);

					// Override FileLayout class
					if ($this->params->get('child_layouts', 1))
					{
						$this->overrideClass('FileLayout');
					}

					// Override HtmlView class
					if ($this->params->get('child_views', 1))
					{
						$this->overrideClass('HtmlView');
					}

					// Override ModuleHelper class
					if ($this->params->get('child_modules', 1))
					{
						$this->overrideClass('ModuleHelper');
					}
				}
			}
		}
	}

	/**
	 * Load child languages.
	 *
	 * @throws  Exception
	 *
	 * @since  1.0.0
	 */
	public function onAfterRoute()
	{
		$app = Factory::getApplication();
		if ($app->isClient('site'))
		{
			if (defined('YOOTHEME_CHILD'))
			{
				// Load child site languages
				if ($this->params->get('child_languages', 1))
				{
					$language = Factory::getLanguage();
					$language->load('tpl_yootheme_' . YOOTHEME_CHILD, JPATH_SITE, $language->getTag(), true);
				}
			}
		}

		// Load child languages in control panel
		if ($app->isClient('administrator') && $this->params->get('child_languages', 1))
		{
			if ($child = Folder::folders(JPATH_SITE . '/templates', 'yootheme_', false))
			{
				$language = Factory::getLanguage();

				foreach ($child as $template)
				{
					$language->load('tpl_' . $template . '.sys', JPATH_SITE, $language->getTag(), true);
				}
			}
		}
	}

	/**
	 * Change fields types and add params.
	 *
	 * @param   Form   $form  The form to be altered.
	 * @param   mixed  $data  The associated data for the form.
	 *
	 * @since  1.0.0
	 */
	public function onContentPrepareForm($form, $data)
	{
		$formName = $form->getName();
		if (in_array($formName, array('com_modules.module', 'com_advancedmodules.module', 'com_config.modules')))
		{
			// Child modules
			if ($this->params->get('child_modules', 1))
			{
				$this->changeFieldType($form, 'layout', 'yooModuleLayout', 'params');
			}

			// Add params
			Form::addFormPath(__DIR__ . '/forms');
			$form->loadFile('module');
		}
	}

	/**
	 * Method to handle image and rerender head.
	 *
	 * @throws  Exception
	 *
	 * @since   1.0.0
	 */
	public function onAfterRender()
	{
		$app = Factory::getApplication();
		if ($app->isClient('site') && $app->getTemplate() === 'yootheme' && $app->input->get('format', 'html') == 'html'
			&& !$app->input->get('customizer'))
		{
			$body = $app->getBody();
			if ($this->params->get('images_handler', 0))
			{
				$this->imagesHandler($body);
			}

			if ($this->params->get('scripts_remove_jquery', 0)
				|| $this->params->get('scripts_remove_bootstrap', 0)
				|| $this->params->get('scripts_remove_core', 0)
				|| $this->params->get('scripts_remove_keepalive', 0))
			{
				$this->cleanHead($body);
			}

			$app->setBody($body);
		}
	}

	/**
	 * Method to include inline files contents to head.
	 *
	 * @throws  Exception
	 *
	 * @since  __DEPLOY_VERSION__
	 */
	public function onBeforeCompileHead()
	{
		$app = Factory::getApplication();
		if ($app->isClient('site') && $app->getTemplate() === 'yootheme')
		{
			$doc = Factory::getDocument();

			// JavaScripts
			$pathsJS = array(
				Path::clean(JPATH_THEMES . '/yootheme/js/inline.min.js'),
				Path::clean(JPATH_THEMES . '/yootheme/js/inline.js'),
			);
			if (defined('YOOTHEME_CHILD'))
			{
				$pathsJS = array_merge(array(
					Path::clean(JPATH_THEMES . '/yootheme_' . YOOTHEME_CHILD . '/js/inline.min.js'),
					Path::clean(JPATH_THEMES . '/yootheme_' . YOOTHEME_CHILD . '/js/inline.js'),
				), $pathsJS);
			}
			foreach ($pathsJS as $path)
			{
				if (file_exists($path))
				{
					$doc->addScriptDeclaration(file_get_contents($path));
					break;
				}
			}

			// Stylesheets
			$pathsJS = array(
				Path::clean(JPATH_THEMES . '/yootheme/css/inline.min.css'),
				Path::clean(JPATH_THEMES . '/yootheme/css/inline.css'),
			);
			if (defined('YOOTHEME_CHILD'))
			{
				$pathsJS = array_merge(array(
					Path::clean(JPATH_THEMES . '/yootheme_' . YOOTHEME_CHILD . '/css/inline.min.css'),
					Path::clean(JPATH_THEMES . '/yootheme_' . YOOTHEME_CHILD . '/css/inline.css'),
				), $pathsJS);
			}
			foreach ($pathsJS as $path)
			{
				if (file_exists($path))
				{
					$doc->addStyleDeclaration(file_get_contents($path));
					break;
				}
			}
		}
	}

	/**
	 * Method to override code class.
	 *
	 * @param   string  $class  Class name.
	 *
	 * @since  1.0.0
	 */
	protected function overrideClass($class = null)
	{
		$classes = array(
			'FileLayout'   => JPATH_ROOT . '/libraries/src/Layout/FileLayout.php',
			'ModuleHelper' => JPATH_ROOT . '/libraries/src/Helper/ModuleHelper.php',
			'HtmlView'     => JPATH_ROOT . '/libraries/src/MVC/View/HtmlView.php',
		);

		if (!empty($classes[$class]) && !class_exists($class))
		{
			$coreClass = $class . 'Core';
			if (!class_exists($coreClass))
			{
				$path     = Path::clean($classes[$class]);
				$core     = Path::clean(__DIR__ . '/classes/' . $coreClass . '.php');
				$override = Path::clean(__DIR__ . '/classes/' . $class . '.php');
				if (!file_exists($core))
				{
					file_put_contents($core, '');
				}

				$context = file_get_contents($path);
				$context = str_replace('class ' . $class, 'class ' . $coreClass, $context);
				if (file_get_contents($core) !== $context)
				{
					file_put_contents($core, $context);
				}

				require_once $core;
				require_once $override;
			}
		}
	}

	/**
	 * Method to change field type.
	 *
	 * @param   Form    $form   Current form object.
	 * @param   string  $field  Field name.
	 * @param   string  $type   New field type.
	 * @param   string  $group  Field group.
	 *
	 * @since   1.0.0
	 */
	protected function changeFieldType(&$form = null, $field = null, $type = null, $group = null)
	{
		if (!empty($form) && !empty($field) && !empty($type))
		{
			Form::addFieldPath(__DIR__ . '/fields');
			$form->setFieldAttribute($field, 'type', $type, $group);
		}
	}

	/**
	 * Method for cleaning head.
	 *
	 * @param   string  $body  Page html.
	 *
	 * @since  1.0.0
	 */
	protected function cleanHead(&$body = '')
	{
		$unsetScripts  = array();
		$replaceScript = array();

		// Remove jQuery
		if ($this->params->get('scripts_remove_jquery', 0))
		{
			$unsetScripts[] = '/media/jui/js/jquery';
			$unsetScripts[] = '/media/jui/js/jquery-noconflict';
			$unsetScripts[] = '/media/jui/js/jquery-migrate';

			$replaceScript[] = '~jQuery\(function\(\$\){.*?(\$\((?!document\).ready).*?\}\);).*?}\);~sim';
			$replaceScript[] = '/jQuery\(function\(\$\)\{(.?)*\}\)\;/';
		}

		// Remove Bootstrap
		if ($this->params->get('scripts_remove_bootstrap', 0))
		{
			$unsetScripts[] = '/media/jui/js/bootstrap';
		}

		// Remove Core
		if ($this->params->get('scripts_remove_core', 0))
		{
			$unsetScripts[] = '/media/system/js/core';
		}

		// Remove Keepalive
		if ($this->params->get('scripts_remove_keepalive', 0))
		{
			$unsetScripts[] = '/media/system/js/keepalive';
		}

		// Rerender head
		if (!empty($unsetScripts) || !empty($replaceScript))
		{
			if (preg_match('|<head>(.*)</head>|si', $body, $matches))
			{
				$search  = $matches[1];
				$replace = $search;
				foreach ($unsetScripts as $src)
				{
					$replace = preg_replace('|<script(.?)*"' . $src . '\.(.?)*</script>|', '', $replace);
				}

				foreach ($replaceScript as $pattern)
				{
					$replace = preg_replace($pattern, '', $replace);
				}

				$replace = preg_replace('/(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+/', '', $replace);

				$body = str_replace($search, $replace, $body);
			}
		}
	}

	/**
	 * Method for image processing.
	 *
	 * @param   string  $body  Page html.
	 *
	 * @since  1.0.0
	 */
	protected function imagesHandler(&$body = '')
	{
		// Check template file exist
		if (!$this->checkFile(__DIR__ . '/templates/jyproextra-image.php',
			JPATH_THEMES . '/yootheme/templates/jyproextra-image.php')) return;

		// Replace images
		if (preg_match_all('/<img[^>]+>/i', $body, $matches))
		{
			$images = (!empty($matches[0])) ? $matches[0] : array();
			foreach ($images as $image)
			{
				$skip = false;
				foreach (array('no-lazy', 'no-handler', 'uk-img', 'data-src', 'srcset') as $value)
				{
					if (preg_match('/' . $value . '/', $image))
					{
						$skip = true;
						break;
					}
				}
				if ($skip) continue;

				if (preg_match_all('/([a-z\-]+)="([^"]*)"/i', $image, $matches2))
				{
					$attrs = array();
					foreach ($matches2[1] as $key => $name)
					{
						$attrs[$name] = $matches2[2][$key];
					}

					$src = (!empty($attrs['src'])) ? $attrs['src'] : '';
					unset($attrs['src']);

					if (!empty($src))
					{
						$src = trim(str_replace(Uri::root(), '', $src), '/');

						// Get attributes
						$width  = (!empty($attrs['width'])) ? $attrs['width'] : '';
						$height = (!empty($attrs['height'])) ? $attrs['height'] : '';

						$thumbnail = array();
						if (!empty($width) && !empty($height))
						{
							$thumb[] = $width;
							$thumb[] = $height;

							unset($attrs['width']);
							unset($attrs['height']);
						}
						$attrs['uk-img'] = true;

						foreach ($attrs as &$attr)
						{
							if (empty($attr))
							{
								$attr = true;
							}
						}

						// Render new image
						$newImage = HTMLHelper::_('render', 'jyproextra-image', array(
							'url'   => array($src, 'thumbnail' => $thumbnail, 'srcset' => true),
							'attrs' => $attrs,
						));

						// Replace image
						$body = str_replace($image, $newImage, $body);
					}
				}
			}
		}
	}

	/**
	 * Method to check and copy file in not exist.
	 *
	 * @param   string  $src   Path to the source file.
	 * @param   string  $dest  The destination path.
	 *
	 * @return  boolean True if file exist or copy. False if error or empty arguments.
	 *
	 * @since  1.0.0
	 */
	protected function checkFile($src = null, $dest = null)
	{
		if (empty($src) || empty($dest)) return false;

		if (!File::exists($dest))
		{
			return File::copy($src, $dest);
		}

		return true;
	}

	/**
	 * Method to unset module based on module params.
	 *
	 * @param   object  $module   The module object.
	 * @param   array   $attribs  The render attributes.
	 *
	 * @throws  Exception
	 *
	 * @since  1.1.0
	 */
	public function onRenderModule(&$module, &$attribs)
	{
		$app = Factory::getApplication();
		if ($app->isClient('site') && $app->getTemplate() === 'yootheme' && !empty($module->params))
		{
			$params     = new Registry($module->params);
			$customizer = (!empty($app->input->get('customizer')));
			$component  = $app->input->get('option');
			$view       = $app->input->get('view');

			// Unset in YooThemePro customizer
			if ($params->get('unset_customizer') && $customizer)
			{
				$module = null;
			}

			// Unset in com_content views
			elseif ($component == 'com_content' && $params->get('unset_content')
				&& in_array($view, $params->get('unset_content')))
			{
				$module = null;
			}

			// Unset empty content modules
			elseif ($params->get('unset_empty') && empty(trim($module->content)))
			{
				$module = null;
			}
		}
	}

	/**
	 * Method to unset modules based on module params.
	 *
	 * @param   array  $modules  The modules array.
	 *
	 * @throws  Exception
	 *
	 * @since  1.1.0
	 */
	public function onAfterCleanModuleList(&$modules)
	{
		$app = Factory::getApplication();
		if ($app->isClient('site') && $app->getTemplate() === 'yootheme' && !empty($modules))
		{
			$resetKeys  = false;
			$customizer = (!empty($app->input->get('customizer')));
			$component  = $app->input->get('option');
			$view       = $app->input->get('view');

			foreach ($modules as $key => $module)
			{
				$params = new Registry($module->params);

				// Unset in YooThemePro customizer
				if ($params->get('unset_customizer') && $customizer)
				{
					$resetKeys = true;
					unset($modules[$key]);
				}

				// Unset in com_content views
				elseif ($component == 'com_content' && $params->get('unset_content')
					&& in_array($view, $params->get('unset_content')))
				{
					$resetKeys = true;
					unset($modules[$key]);
				}

				// Unset empty content modules
				elseif ($params->get('unset_empty') && empty(trim(ModuleHelper::renderModule($module))))
				{
					$resetKeys = true;
					unset($modules[$key]);
				}
			}

			// Reset modules array keys
			if ($resetKeys)
			{
				$modules = array_values($modules);
			}
		}
	}
}
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

use Joomla\CMS\Application\CMSApplication;
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
	 * Loads the application object.
	 *
	 * @var  CMSApplication
	 *
	 * @since  1.2.0
	 */
	protected $app = null;

	/**
	 * Affects constructor behavior.
	 *
	 * @var  boolean
	 *
	 * @since  1.0.0
	 */
	protected $autoloadLanguage = true;

	/**
	 * Image function enable.
	 *
	 * @var  boolean
	 *
	 * @since  1.2.0
	 */
	protected $images = false;

	/**
	 * Inline files function enable.
	 *
	 * @var  boolean
	 *
	 * @since  1.2.0
	 */
	protected $inline = false;

	/**
	 * Exclude Modules function enable.
	 *
	 * @var  boolean
	 *
	 * @since  1.2.0
	 */
	protected $unset_modules = false;

	/**
	 * Child theme function enable.
	 *
	 * @var  boolean
	 *
	 * @since  1.2.0
	 */
	protected $child = false;

	/**
	 * Removing JavaScripts function enable.
	 *
	 * @var  boolean
	 *
	 * @since  1.2.0
	 */
	protected $remove_js = false;

	/**
	 * Pagination function enable.
	 *
	 * @var  boolean
	 *
	 * @since  1.2.0
	 */
	protected $pagination = false;

	/**
	 * Constructor.
	 *
	 * @param   object  &$subject  The object to observe
	 * @param   array    $config   An optional associative array of configuration settings.
	 *
	 * @since   1.2.0
	 */
	public function __construct(&$subject, $config = array())
	{
		parent::__construct($subject, $config);

		// Set functions status
		$this->images        = ($this->params->get('images')) ? true : false;
		$this->inline        = ($this->params->get('inline')) ? true : false;
		$this->unset_modules = ($this->params->get('unset_modules')) ? true : false;
		$this->child         = ($this->params->get('child')) ? true : false;
		$this->remove_js     = ($this->params->get('remove_js')) ? true : false;
		$this->pagination    = ($this->params->get('pagination')) ? true : false;
	}

	/**
	 * Set child constant and override classes.
	 *
	 * @since  1.0.1
	 */
	public function onAfterInitialise()
	{
		if ($this->child && $this->app->isClient('site'))
		{
			$template = $this->app->getTemplate();
			if ($template === 'yootheme')
			{
				$params = $this->app->getTemplate(true)->params->get('config');
				$params = new Registry($params);

				if ($child = $params->get('child_theme'))
				{
					// Set constant
					define('YOOTHEME_CHILD', $child);

					// Override FileLayout class
					$this->overrideClass('FileLayout');

					// Override HtmlView class
					$this->overrideClass('HtmlView');

					// Override ModuleHelper class
					$this->overrideClass('ModuleHelper');
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
			'HtmlView'     => JPATH_ROOT . '/libraries/src/MVC/View/HtmlView.php',
			'ModuleHelper' => JPATH_ROOT . '/libraries/src/Helper/ModuleHelper.php',
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
	 * Load child languages and enable pagination for all components.
	 *
	 * @since  1.0.0
	 */
	public function onAfterRoute()
	{
		if ($this->app->isClient('site'))
		{
			// Load child site languages
			if ($this->child && defined('YOOTHEME_CHILD'))
			{
				$language = Factory::getLanguage();
				$language->load('tpl_yootheme_' . YOOTHEME_CHILD, JPATH_SITE, $language->getTag(), true);
			}

			// Enable pagination for all components
			if ($this->pagination
				&& !in_array($this->app->input->get('option'), array('com_content', 'com_finder', 'com_search', 'com_tags')))
			{
				$this->overridePagination();
			}
		}

		// Load child languages in control panel
		if ($this->child && $this->app->isClient('administrator'))
		{
			if ($child = Folder::folders(Path::clean(JPATH_SITE . '/templates'), '^yootheme_', false))
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
	 * Method to override pagination for enabled on all components.
	 *
	 * @since  1.2.0
	 */
	protected function overridePagination()
	{
		// Create pagination_all file
		$src     = Path::clean(JPATH_THEMES . '/yootheme/html/pagination.php');
		$dest    = Path::clean(JPATH_THEMES . '/yootheme/html/jyproextra-pagination.php');
		$context = file_get_contents($src);
		$context = preg_replace('#if(.?)*#', '', $context, 1);
		$context = trim($context);
		$context = rtrim($context, '}');
		if (File::exists($dest))
		{
			File::delete($dest);
		}
		file_put_contents($dest, $context);

		// Override Pagination Class
		$src     = Path::clean(JPATH_ROOT . '/libraries/src/Pagination/Pagination.php');
		$dest    = Path::clean(__DIR__ . '/classes/Pagination.php');
		$context = str_replace('pagination.php', 'jyproextra-pagination.php', file_get_contents($src));
		if (File::exists($dest))
		{
			File::delete($dest);
		}
		file_put_contents($dest, $context);
		require_once $dest;
	}

	/**
	 * Change fields types and add fields.
	 *
	 * @param   Form  $form  The form to be altered.
	 *
	 * @since  1.0.0
	 */
	public function onContentPrepareForm($form)
	{
		// Change fields type for child theme
		if ($this->child)
		{
			$types = array(
				'ModuleLayout'    => 'YooModuleLayout',
				'ComponentLayout' => 'YooComponentLayout'
			);
			Form::addFieldPath(__DIR__ . '/fields');
			foreach ($form->getFieldsets() as $fieldset)
			{
				foreach ($form->getFieldset($fieldset->name) as $field)
				{
					$type = $field->__get('type');
					if (isset($types[$type]))
					{
						$name  = $field->__get('fieldname');
						$group = $field->__get('group');
						$form->setFieldAttribute($name, 'type', $types[$type], $group);
					}
				}
			}
		}

		// Change modules form
		if ($this->unset_modules
			&& in_array($form->getName(), array('com_modules.module', 'com_advancedmodules.module', 'com_config.modules')))
		{
			// Add params
			Form::addFormPath(__DIR__ . '/forms');
			$form->loadFile('module');
		}
	}

	/**
	 * Method to unset modules based on module params.
	 *
	 * @param   array  $modules  The modules array.
	 *
	 * @since  1.1.0
	 */
	public function onAfterCleanModuleList(&$modules)
	{
		if ($this->unset_modules && !empty($modules) && $this->app->isClient('site')
			&& $this->app->getTemplate() === 'yootheme')
		{
			$resetKeys  = false;
			$customizer = (!empty($this->app->input->get('customizer')));
			$component  = $this->app->input->get('option');
			$view       = $this->app->input->get('view');

			foreach ($modules as $key => $module)
			{
				$params = new Registry($module->params);

				// Unset in YOOtheme Pro customizer
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

	/**
	 * Method to unset module based on module params.
	 *
	 * @param   object  $module  The module object.
	 *
	 * @since  1.1.0
	 */
	public function onRenderModule(&$module)
	{
		if ($this->unset_modules && !empty($module->params) && $this->app->isClient('site')
			&& $this->app->getTemplate() === 'yootheme')
		{
			$params     = new Registry($module->params);
			$customizer = (!empty($this->app->input->get('customizer')));
			$component  = $this->app->input->get('option');
			$view       = $this->app->input->get('view');

			// Unset in YOOtheme Pro customizer
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
	 * Method to include inline files contents to head.
	 *
	 * @since  1.2.0
	 */
	public function onBeforeCompileHead()
	{
		if ($this->inline && $this->app->isClient('site') && $this->app->getTemplate() === 'yootheme')
		{
			$doc = Factory::getDocument();

			// JavaScripts
			$pathsJS = array(
				Path::clean(JPATH_THEMES . '/yootheme/js/inline.min.js'),
				Path::clean(JPATH_THEMES . '/yootheme/js/inline.js'),
			);
			if ($this->child && defined('YOOTHEME_CHILD'))
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
	 * Method to handle image and rerender head.
	 *
	 * @since   1.0.0
	 */
	public function onAfterRender()
	{
		if (($this->images || $this->remove_js) && $this->app->isClient('site')
			&& $this->app->getTemplate() === 'yootheme' && $this->app->input->get('format', 'html') == 'html'
			&& !$this->app->input->get('customizer'))
		{
			$body = $this->app->getBody();

			// Convert images
			if ($this->images)
			{
				$this->convertImages($body);
			}

			if ($this->remove_js)
			{
				$this->removeJS($body);
			}

			$this->app->setBody($body);
		}
	}

	/**
	 * Method to convert site images.
	 *
	 * @param   string  $body  Current page html.
	 *
	 * @since  1.2.0
	 */
	protected function convertImages(&$body = '')
	{
		// Check template file exist
		$src   = Path::clean(__DIR__ . '/templates/jyproextra-image.php');
		$dest  = Path::clean(JPATH_THEMES . '/yootheme/templates/jyproextra-image.php');
		$exist = (!File::exists($dest)) ? File::copy($src, $dest) : true;
		if (!$exist) return;

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
	 * Method for remove old js scripts from head.
	 *
	 * @param   string  $body  Current page html.
	 *
	 * @since       1.2.0
	 *
	 * @deprecated  Deprecated on Joomla 4.
	 */
	protected function removeJS(&$body = '')
	{
		if (preg_match('|<head>(.*)</head>|si', $body, $matches))
		{
			$search  = $matches[1];
			$replace = $search;

			// Remove js files
			$files = array(
				'/media/jui/js/jquery',
				'/media/jui/js/jquery-noconflict',
				'/media/jui/js/jquery-migrate',
				'/media/jui/js/bootstrap',
			);
			foreach ($files as $src)
			{
				$replace = preg_replace('|<script(.?)*"' . $src . '\.(.?)*</script>|', '', $replace);
			}

			// Remove inline java scripts
			$patterns = array(
				'~jQuery\(function\(\$\){.*?(\$\((?!document\).ready).*?\}\);).*?}\);~sim',
				'/jQuery\(function\(\$\)\{(.?)*\}\)\;/',
			);
			foreach ($patterns as $pattern)
			{
				$replace = preg_replace($pattern, '', $replace);
			}

			// Remove empty lines
			$replace = preg_replace('/(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+/', '', $replace);

			// Replace body
			$body = str_replace($search, $replace, $body);
		}
	}
}
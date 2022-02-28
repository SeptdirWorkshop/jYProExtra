<?php
/**
 * @package    jYProExtra System Plugin
 * @version    1.8.0
 * @author     Septdir Workshop - www.septdir.com
 * @copyright  Copyright (c) 2018 - 2021 Septdir Workshop. All rights reserved.
 * @license    GNU/GPL license: https://www.gnu.org/copyleft/gpl.html
 * @link       https://www.septdir.com/
 */

defined('_JEXEC') or die;

use Joomla\CMS\Application\CMSApplication;
use Joomla\CMS\Date\Date;
use Joomla\CMS\Factory;
use Joomla\CMS\Filesystem\File;
use Joomla\CMS\Filesystem\Folder;
use Joomla\CMS\Filesystem\Path;
use Joomla\CMS\Form\Form;
use Joomla\CMS\Helper\ModuleHelper;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Installer\Installer;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Version;
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
	 * Loads the database object.
	 *
	 * @var  JDatabaseDriver
	 *
	 * @since  1.3.0
	 */
	protected $db = null;

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
	 * UIkit icons function enable.
	 *
	 * @var  boolean
	 *
	 * @since  1.7.0
	 */
	protected $uikit_icons = false;

	/**
	 * Pagination function enable.
	 *
	 * @var  boolean
	 *
	 * @since  1.2.0
	 */
	protected $pagination = false;

	/**
	 * Toolbar function enable.
	 *
	 * @var  boolean
	 *
	 * @since  1.3.0
	 */
	protected $toolbar = false;

	/**
	 * WebP cache function enable.
	 *
	 * @var  boolean
	 *
	 * @since  1.5.0
	 */
	protected $webp_cache = false;

	/**
	 * Remove YOOtheme Pro update stylesheet function enable.
	 *
	 * @var  boolean
	 *
	 * @since  1.5.0
	 */
	protected $remove_update_css = false;

	/**
	 * Preview function enable.
	 *
	 * @var  boolean
	 *
	 * @since  1.6.0
	 */
	protected $preview = false;

	/**
	 * Is Joomla 4.
	 *
	 * @var  boolean
	 *
	 * @since  1.6.0
	 */
	protected $joomla4 = false;

	/**
	 * Is visitor authorized in control panel.
	 *
	 * @var  boolean
	 *
	 * @since  1.5.0
	 */
	protected $_isAuthorizedAdmin = null;

	/**
	 * Constructor.
	 *
	 * @param   object  &$subject  The object to observe
	 * @param   array    $config   An optional associative array of configuration settings.
	 *
	 * @since  1.2.0
	 */
	public function __construct(&$subject, $config = array())
	{
		parent::__construct($subject, $config);

		// Set functions status
		$this->images            = ($this->params->get('images')) ? true : false;
		$this->inline            = ($this->params->get('inline')) ? true : false;
		$this->unset_modules     = ($this->params->get('unset_modules')) ? true : false;
		$this->child             = ($this->params->get('child')) ? true : false;
		$this->remove_js         = ($this->params->get('remove_js')) ? true : false;
		$this->uikit_icons       = ($this->params->get('uikit_icons')) ? true : false;
		$this->pagination        = ($this->params->get('pagination')) ? true : false;
		$this->toolbar           = ($this->params->get('toolbar')) ? true : false;
		$this->webp_cache        = ($this->params->get('webp_cache')) ? true : false;
		$this->remove_update_css = ($this->params->get('remove_update_css')) ? true : false;
		$this->preview           = ($this->params->get('preview')) ? true : false;
		$this->joomla4           = (new Version())->isCompatible('4.0');
	}

	/**
	 * Override classes and set admin cookie.
	 *
	 * @since  1.0.1
	 */
	public function onAfterInitialise()
	{
		if ($this->child && $this->app->isClient('site'))
		{
			// Override FileLayout class
			$this->overrideClass('FileLayout');

			// Override ModuleHelper class
			$this->overrideClass('HTMLHelper');

			// Override HtmlView class
			$this->overrideClass('HtmlView');

			// Override ModuleHelper class
			$this->overrideClass('ModuleHelper');
		}

		// Override BaseController class
		if ($this->app->isClient('site') && $this->webp_cache)
		{
			$this->overrideClass('BaseController');
		}

		// Set admin user_id cookie
		if ($this->app->isClient('administrator') && $this->toolbar)
		{
			$this->app->input->cookie->set('jyproextra_admin',
				Factory::getUser()->id,
				(new Date('now + 3 days'))->toUnix(),
				$this->app->get('cookie_path', '/'),
				$this->app->get('cookie_domain'),
				$this->app->isSSLConnection());
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
			'FileLayout'     => JPATH_ROOT . '/libraries/src/Layout/FileLayout.php',
			'HTMLHelper'     => JPATH_ROOT . '/libraries/src/HTML/HTMLHelper.php',
			'HtmlView'       => JPATH_ROOT . '/libraries/src/MVC/View/HtmlView.php',
			'ModuleHelper'   => JPATH_ROOT . '/libraries/src/Helper/ModuleHelper.php',
			'BaseController' => JPATH_ROOT . '/libraries/src/MVC/Controller/BaseController.php',
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
	 * Set child constant, load child languages and enable pagination for all components.
	 *
	 * @since  1.0.0
	 */
	public function onAfterRoute()
	{
		if ($this->app->isClient('site'))
		{
			if ($this->child)
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

						// Load child site languages
						$language = Factory::getLanguage();
						$language->load('tpl_yootheme_' . $child, JPATH_SITE, $language->getTag(), true);
					}
				}
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
	 * @depreacted YOOtheme 2.2+
	 *
	 * @since      1.2.0
	 */
	protected function overridePagination()
	{
		// Create pagination_all file
		$src     = Path::clean(JPATH_THEMES . '/yootheme/html/pagination.php');
		$dest    = Path::clean(JPATH_THEMES . '/yootheme/html/jyproextra-pagination.php');
		$context = file_get_contents($src);
		if (preg_match('#com_content#', $context))
		{
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
	}

	/**
	 * Change fields types, add fields and add preview toolbar.
	 *
	 * @param   Form   $form  The form to be altered.
	 * @param   mixed  $data  The associated data for the form.
	 *
	 * @since  1.0.0
	 */
	public function onContentPrepareForm($form, $data)
	{
		// Change fields type for child theme
		if ($this->child)
		{
			$types = array(
				'ComponentLayout' => 'YooComponentLayout',
				'FieldLayout'     => 'YooFieldLayout',
				'ModuleLayout'    => 'YooModuleLayout',
			);
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
						$form->setFieldAttribute($name, 'addfieldpath', '/plugins/system/jyproextra/fields', $group);
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

			// Remove unset customizer & unset empty in builder module
			if ((new Registry($data))->get('module') == 'mod_yootheme_builder')
			{
				$form->removeField('unset_customizer', 'params');
				$form->removeField('unset_empty', 'params');
			}
		}

		// Add preview buttons
		if ($this->preview && $this->app->isClient('administrator') && is_object($data))
		{
			$formName = $form->getName();
			$preview  = false;
			if ($formName === 'com_content.article' && !empty($data->id))
			{
				$preview = 'index.php?option=com_content&view=article&id=' . $data->id . ':' . $data->alias . '&catid=' . $data->catid;
				if (!empty($data->language) && $data->language !== '*')
				{
					$preview .= '&lang=' . $data->language;
				}
			}

			if ($formName === 'com_categories.categorycom_content' && !empty($data->id))
			{
				$preview = 'index.php?option=com_content&view=category&id=' . $data->id . ':' . $data->alias;
				if (!empty($data->language) && $data->language !== '*')
				{
					$preview .= '&lang=' . $data->language;
				}
			}

			if ($formName === 'com_menus.item' && !empty($data->id))
			{
				$preview = 'index.php?Itemid=' . $data->id;
				if (!empty($data->language) && $data->language !== '*')
				{
					$preview .= '&lang=' . $data->language;
				}
			}

			if ($preview)
			{
				$toolbar = Toolbar::getInstance();
				if (!$this->joomla4){

				}
				else {
					Factory::getDocument()->addStyleDeclaration('#toolbar-jyproextra_preview{float:right}');
				}
				$link = Uri::root() . 'index.php?option=com_ajax&plugin=jyproextra&group=system&action=sitePreview&preview='
					. base64_encode($preview) . '&format=raw';
				$toolbar->appendButton('Custom', LayoutHelper::render('plugins.system.jyproextra.toolbar.link', array(
					'link' => $link,
					'text' => 'PLG_SYSTEM_JYPROEXTRA_PREVIEW_BUTTON',
					'icon' => 'enter',
					'id'=> 'jyproextra_preview',
					'order' => 99,
				)), 'jyproextra_preview');
			}
		}

		// Set success message
		if ($successMessage = $this->app->getUserState('jyproextra_success_message'))
		{
			$this->app->enqueueMessage($successMessage);
			$this->app->setUserState('jyproextra_success_message', false);
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
			$resetKeys       = false;
			$customizer      = (!empty($this->app->input->get('customizer')));
			$component       = $this->app->input->get('option');
			$view            = $this->app->input->get('view');
			$layout          = $this->app->input->get('layout');
			$controller      = $this->app->input->get('controller', $this->app->input->get('ctrl', $this->app->input->get('task')));
			$unsetView       = ($view) ? $component . '.' . $view : false;
			$unsetLayout     = ($unsetView && $layout) ? $unsetView . ':' . $layout : false;
			$unsetController = (!$view && $controller) ? $component . '.' . $controller : false;

			foreach ($modules as $key => $module)
			{
				$params          = new Registry($module->params);
				$unsetComponents = $params->get('unset_components');

				// Unset in YOOtheme Pro customizer
				if ($params->get('unset_customizer') && $customizer)
				{
					$resetKeys = true;
					unset($modules[$key]);
				}

				// Unset in components views
				elseif ($unsetComponents && (($unsetView && in_array($unsetView, $unsetComponents))
						|| ($unsetLayout && in_array($unsetLayout, $unsetComponents))
						|| ($unsetController && in_array($unsetController, $unsetComponents))))
				{
					$resetKeys = true;
					unset($modules[$key]);
				}

				// Unset administrator
				elseif ($params->get('unset_administrator') && $this->isAuthorizedAdmin())
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
			$params          = new Registry($module->params);
			$customizer      = (!empty($this->app->input->get('customizer')));
			$component       = $this->app->input->get('option');
			$view            = $this->app->input->get('view');
			$layout          = $this->app->input->get('layout');
			$controller      = $this->app->input->get('controller', $this->app->input->get('ctrl', $this->app->input->get('task')));
			$unsetView       = ($view) ? $component . '.' . $view : false;
			$unsetLayout     = ($unsetView && $layout) ? $unsetView . ':' . $layout : false;
			$unsetController = (!$view && $controller) ? $component . '.' . $controller : false;
			$unsetComponents = $params->get('unset_components');

			// Unset in YOOtheme Pro customizer
			if ($params->get('unset_customizer') && $customizer)
			{
				$module = null;
			}

			// Unset in components views
			elseif ($unsetComponents && (($unsetView && in_array($unsetView, $unsetComponents))
					|| ($unsetLayout && in_array($unsetLayout, $unsetComponents))
					|| ($unsetController && in_array($unsetController, $unsetComponents))
				))
			{
				$module = null;
			}

			// Unset administrator
			elseif ($params->get('unset_administrator') && $this->isAuthorizedAdmin())
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
	 * Method to include inline files contents to head and add scripts to customizer.
	 *
	 * @since  1.2.0
	 */
	public function onBeforeCompileHead()
	{
		// Include inline files contents
		if ($this->inline && $this->app->isClient('site') && $this->app->getTemplate() === 'yootheme')
		{
			$this->includeInlineFiles();
		}

		// Add scripts to customizer
		if (!$this->joomla4 && $this->app->isClient('administrator') && $this->app->input->get('option') === 'com_ajax'
			&& $this->app->input->get('p') === 'customizer')
		{
			$this->addCustomizerScripts();
		}
	}

	/**
	 * Method to include inline files contents to head.
	 *
	 * @since  1.4.1
	 */
	protected function includeInlineFiles()
	{
		$doc = Factory::getDocument();

		// JavaScripts
		$pathsJS = array(
			Path::clean(JPATH_THEMES . '/yootheme/js/inline.min.js'),
			Path::clean(JPATH_THEMES . '/yootheme/js/inline.js'),
		);
		if ($child = (new Registry($this->app->getTemplate(true)->params->get('config')))->get('child_theme'))
		{
			$pathsJS = array_merge(array(
				Path::clean(JPATH_THEMES . '/yootheme_' . $child . '/js/inline.min.js'),
				Path::clean(JPATH_THEMES . '/yootheme_' . $child . '/js/inline.js'),
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
		$pathsCss = array(
			Path::clean(JPATH_THEMES . '/yootheme/css/inline.min.css'),
			Path::clean(JPATH_THEMES . '/yootheme/css/inline.css'),
		);
		if ($child)
		{
			$pathsCss = array_merge(array(
				Path::clean(JPATH_THEMES . '/yootheme_' . YOOTHEME_CHILD . '/css/inline.min.css'),
				Path::clean(JPATH_THEMES . '/yootheme_' . YOOTHEME_CHILD . '/css/inline.css'),
			), $pathsCss);
		}
		foreach ($pathsCss as $path)
		{
			if (file_exists($path))
			{
				$doc->addStyleDeclaration(file_get_contents($path));
				break;
			}
		}
	}

	/**
	 * Method to add and run scripts to customizer.
	 *
	 * @since  1.4.1
	 *
	 * @depreacted 2.0
	 */
	protected function addCustomizerScripts()
	{
		// Add modal
		$link = 'index.php?option=com_ajax&plugin=jyproextra&group=system&action=jYProExtraModal&format=json';
		HTMLHelper::script('plg_system_jyproextra/customizer.min.js', array('version' => 'auto', 'relative' => true));
		Factory::getDocument()->addScriptDeclaration("jYProExtraModal('" . $link . "');");

		// Include jquery
		HTMLHelper::_('jquery.framework');

		// Remove toolbar from preview
		Factory::getDocument()->addScriptDeclaration("jYProExtraRemoveToolbar();");
	}

	/**
	 * Method to handle image and rerender head.
	 *
	 * @since  1.0.0
	 */
	public function onAfterRender()
	{
		$body = false;
		if (($this->images || $this->remove_js || $this->toolbar || $this->remove_update_css) && $this->app->isClient('site')
			&& $this->app->getTemplate() === 'yootheme' && $this->app->input->get('format', 'html') == 'html'
			&& !$this->app->input->get('customizer'))
		{
			$body = $this->app->getBody();

			// Convert images
			if ($this->images)
			{
				$this->convertImages($body);
			}

			// Remove old javascripts
			if ($this->remove_js)
			{
				$this->removeJS($body);
			}

			// Remove or move uikit-icons
			if ($this->uikit_icons)
			{
				$this->UIkitIcons($body);
			}

			// Add YOOtheme toolbar
			if ($this->toolbar)
			{
				$this->addYOOthemeToolbar($body);
			}

			// Remove YOOtheme Pro update stylesheet
			if ($this->remove_update_css)
			{
				$this->removeUpdateCss($body);
			}

			$this->app->setBody($body);
		}

		// Replace breadcrumbs shortcode
		if ($this->app->isClient('site'))
		{
			$body = (!$body) ? $this->app->getBody() : $body;
			$this->replaceBreadcrumbsShortcode($body);
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
		if (!File::exists(Path::clean(JPATH_THEMES . '/yootheme/templates/jyproextra-image.php'))) return;

		// Replace images
		if (preg_match_all('/<img[^>]+>/i', $body, $matches))
		{
			$images = (!empty($matches[0])) ? $matches[0] : array();
			$view   = (function_exists('YOOtheme\app')) ? YOOtheme\app(YOOtheme\View::class) : false;

			foreach ($images as $image)
			{
				$skip = false;
				foreach (array('no-lazy', 'no-handler', 'uk-img', 'uk-svg', 'data-src', 'srcset') as $value)
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
						if (isset($attrs['width'])) unset($attrs['width']);
						if (isset($attrs['height'])) unset($attrs['height']);
						foreach ($attrs as &$attr)
						{
							if (empty($attr))
							{
								$attr = true;
							}
						}

						// Render new image
						$data     = array(
							'src'    => $src,
							'width'  => $width,
							'height' => $height,
							'attrs'  => $attrs,
						);
						$newImage = ($view) ? $view('~theme/templates/jyproextra-image', $data)
							: HTMLHelper::_('render', 'jyproextra-image', $data);

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
	 * @since  1.2.0
	 */
	protected function removeJS(&$body = '')
	{
		if (preg_match('|<head>(.*)</head>|si', $body, $matches))
		{
			$search        = $matches[1];
			$replace       = $search;
			$scripts       = array();
			$links         = array();
			$jQueryExtends = array();

			// Check browser remove jQuery support
			JLoader::register('jYProExtraHelperBrowser', __DIR__ . '/helpers/browser.php');
			$supported = jYProExtraHelperBrowser::supported('proxy', array(
				'Chrome'       => 49,
				'Firefox'      => 18,
				'Opera'        => 36,
				'Edge'         => 12,
				'YaBrowser'    => 1,
				'YandexSearch' => 1,
				'Android'      => 81,
				'Safari'       => 10
			));
			if (!$supported && $this->params->set('remove_js_jquery', 1))
			{
				$this->params->set('remove_js_jquery', 0);
				$this->params->set('remove_js_bootstrap', 1);
				$this->params->set('remove_js_chosen', 1);
			}

			// Remove jQuery
			if ($this->params->get('remove_js_jquery', 1))
			{
				$scripts[] = '/media/jui/js/jquery';
				$scripts[] = '/media/jui/js/jquery-noconflict';
				$scripts[] = '/media/jui/js/jquery-migrate';
				$scripts[] = '/media/jui/js/bootstrap';
				$scripts[] = '/media/jui/js/chosen';
				$scripts[] = '/media/jui/js/ajax-chosen';

				$links[] = '/media/jui/css/chosen';

				$handler = '<script>'
					. 'var jYProExtraNojQueryHandler = {apply: function (target, thisArg, args) {'
					. 'return new Proxy(function() {}, jYProExtraNojQueryHandler);},'
					. 'get: function (target, propKey, receiver) {'
					. 'return new Proxy(function() {}, jYProExtraNojQueryHandler);}};'
					. 'const $ = jQuery = new Proxy(function() {}, jYProExtraNojQueryHandler);'
					. '</script>';

				$replace = preg_replace('#<script#', $handler . PHP_EOL . '<script', $replace, 1);
			}
			else
			{
				// Remove Bootstrap
				if ($this->params->get('remove_js_bootstrap', 1))
				{
					$scripts[] = '/media/jui/js/bootstrap';

					$jQueryExtends[] = 'alert';
					$jQueryExtends[] = 'button';
					$jQueryExtends[] = 'carousel';
					$jQueryExtends[] = 'collapse';
					$jQueryExtends[] = 'dropdown';
					$jQueryExtends[] = 'modal';
					$jQueryExtends[] = 'tooltip';
					$jQueryExtends[] = 'popover';
					$jQueryExtends[] = 'scrollspy';
					$jQueryExtends[] = 'tab';
					$jQueryExtends[] = 'typeahead';
					$jQueryExtends[] = 'affix';
				}

				// Remove chosen
				if ($this->params->get('remove_js_chosen', 1))
				{
					$scripts[] = '/media/jui/js/chosen';
					$scripts[] = '/media/jui/js/ajax-chosen';

					$links[] = '/media/jui/css/chosen';

					$jQueryExtends[] = 'chosen';
					$jQueryExtends[] = 'ajaxChosen ';
				}
			}

			// Remove scripts
			foreach ($scripts as $src)
			{
				$replace = preg_replace('#<script.*src="' . $src . '\..*".*</script>#i', '', $replace);
			}

			// Remove links
			foreach ($links as $src)
			{
				$replace = preg_replace('#<link.*href="' . $src . '\..*".*/>#i', '', $replace);
			}

			// Add jQuery extends
			if ($jQueryExtends)
			{
				$jQueryFN = '';
				foreach ($jQueryExtends as $i => $name)
				{
					$jQueryFN .= 'jQuery.fn.' . $name . ' = function (){console.log("jQuery ' . $name . ' not available")};';
				}
				$script = '	<script>if (typeof jQuery !== "undefined") {' . $jQueryFN . '}</script>';

				$replace .= PHP_EOL . $script . PHP_EOL;
			}

			// Remove empty lines
			$replace = preg_replace('#(<\/.*?>|\/>)(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+#', '${1}' . PHP_EOL, $replace);

			// Replace body
			$body = str_replace($search, $replace, $body);
		}
	}

	/**
	 * Method for move or remove uikit-icons script from head.
	 *
	 * @param   string  $body  Current page html.
	 *
	 * @since  1.7.0
	 */
	protected function UIkitIcons(&$body = '')
	{
		if (preg_match('|<head>(.*)</head>|si', $body, $matches))
		{
			$search = $matches[1];
			if (preg_match_all('#<script.*src="(.+?)".*</script>#i', $search, $scripts))
			{
				$replace = $search;
				$head    = false;
				$footer  = false;
				foreach ($scripts[0] as $s => $script)
				{
					if (preg_match('#uikit-icons#', $script))
					{
						$replace = str_replace($script, '', $replace);
						$head    = true;
						if ($this->params->get('uikit_icons_mode', 'move') === 'move')
						{
							$footer = '<script defer src="' . $scripts[1][$s] . '"></script>' . PHP_EOL;
						}
						break;
					}
				}

				if ($head === true)
				{
					$replace = preg_replace('#(<\/.*?>|\/>)(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+#',
						'${1}' . PHP_EOL, $replace);
					$body    = str_replace($search, $replace, $body);
				}

				if ($footer) $body = str_replace('</body>', $footer . '</body>', $body);
			}
		}
	}

	/**
	 * Method to add YOOtheme toolbar.
	 *
	 * @param   string  $body  Current page html.
	 *
	 * @since  1.3.0
	 */
	protected function addYOOthemeToolbar(&$body = '')
	{
		if ($userID = (int) $this->app->input->cookie->get('jyproextra_admin'))
		{
			if (Factory::getUser($userID)->authorise('core.login.admin')
				&& !in_array($userID, $this->params->get('toolbar_hide_users', array())))
			{
				$uri         = Uri::getInstance();
				$current     = urlencode($uri->toString());
				$admin       = Uri::root() . 'administrator/';
				$yootheme    = $admin . 'index.php?p=customizer&option=com_ajax&format=html';
				$displayData = array(
					'customizer' => $yootheme . '&site=' . $current . '&return=' . $current,
					'builder'    => false,
					'admin'      => false,
					'position'   => $this->params->get('toolbar', 'center-right'),
				);
				if ($this->app->input->get('option') === 'com_content'
					&& $this->app->input->get('view') === 'article')
				{
					$displayData['builder'] = $yootheme . '&section=builder&site=' . $current . '&return=' . $current;
					$displayData['admin']   = $admin . 'index.php?option=com_content&task=article.edit&id='
						. $this->app->input->get('id');
				}
				if ($this->app->input->get('option') === 'com_content'
					&& $this->app->input->get('view') === 'category')
				{
					$displayData['admin'] = $admin . 'index.php?option=com_categories&task=category.edit&extension=com_content&id='
						. $this->app->input->get('id');
				}

				$toolbar = LayoutHelper::render('plugins.system.jyproextra.toolbar.yootheme', $displayData);
				$body    = str_replace('</body>', $toolbar . PHP_EOL . '</body>', $body);
			}
		}
	}

	/**
	 * Method for remove YOOtheme Pro update stylesheet from head.
	 *
	 * @param   string  $body  Current page html.
	 *
	 * @since  1.5.0
	 */
	protected function removeUpdateCss(&$body = '')
	{
		if (preg_match('|<head>(.*)</head>|si', $body, $matches))
		{
			// Remove file
			$search  = $matches[1];
			$replace = preg_replace('#<link(.?)*theme\.update\.css(.?)*/>#', '', $search);

			// Replace body
			$body = str_replace($search, $replace, $body);
		}
	}

	/**
	 * Method to add breadcrumbs.
	 *
	 * @param   string  $body  Current page html.
	 *
	 * @since  1.3.0
	 */
	protected function replaceBreadcrumbsShortcode($body = '')
	{
		if (preg_match('/{jyproextra_joomla_breadcrumbs}/i', $body))
		{
			$module            = new stdClass();
			$module->id        = 'tm-jyproextra-breadcrumbs';
			$module->name      = 'yoo_breadcrumbs';
			$module->title     = '';
			$module->showtitle = 0;
			$module->position  = '';
			$module->params    = '{}';
			$module->module    = 'mod_breadcrumbs';

			// Replace in head
			if (preg_match('|<head>(.*)</head>|si', $body, $matches))
			{
				$search  = $matches[1];
				$replace = str_replace('{jyproextra_joomla_breadcrumbs}', '', $search);

				$body = str_replace($search, $replace, $body);
			}

			// Replace in body
			$body = str_replace('{jyproextra_joomla_breadcrumbs}', ModuleHelper::renderModule($module), $body);
			$this->app->setBody($body);
		}
	}

	/**
	 * Method to ajax functions.
	 *
	 * @throws  Exception
	 *
	 * @return mixed Function result.
	 *
	 * @since  1.3.0
	 */
	public function onAjaxJyproextra()
	{
		$action = $this->app->input->get('action');
		if (empty($action) || !method_exists($this, $action))
		{
			throw new Exception(Text::_('PLG_SYSTEM_JYPROEXTRA_ERROR_AJAX_METHOD_NOT_FOUND'), 500);
		}

		return $this->$action();
	}

	/**
	 * Method to export YOOtheme Pro library items.
	 *
	 * @throws  Exception
	 *
	 * @return boolean True on success, False on failure.
	 *
	 * @since  1.3.0
	 */
	protected function libraryExport()
	{
		$keys = explode(',', $this->app->input->get('keys', '', 'string'));
		$keys = array_filter(array_map('trim', $keys), function ($element) {
			return !empty($element);
		});

		// Get items
		$items = array();
		$db    = $this->db;
		$query = $db->getQuery(true)
			->select(array('e.custom_data'))
			->from($db->quoteName('#__extensions', 'e'))
			->where($db->quoteName('e.type') . ' = ' . $db->quote('plugin'))
			->where($db->quoteName('e.element') . ' = ' . $db->quote('yootheme'))
			->where($db->quoteName('e.folder') . ' = ' . $db->quote('system'));
		if ($custom_data = $db->setQuery($query)->loadResult())
		{
			$custom_data = json_decode($custom_data, true);

			if (!empty($custom_data['library']))
			{
				foreach ($custom_data['library'] as $key => $item)
				{
					if (empty($keys) || in_array($key, $keys))
					{
						$items[$key] = $item;
					}
				}
			}
		}
		if (empty($items))
		{
			throw new Exception(Text::_('PLG_SYSTEM_JYPROEXTRA_ERROR_LIBRARY_ITEMS_NOT_FOUND'), 404);
		}

		// Prepare result
		$check    = 'jyproextra_library_export';
		$host     = Uri::getInstance()->toString(array('host'));
		$date     = Factory::getDate()->toSql();
		$filename = $check . '_' . $host . '_' . Factory::getDate()->toUnix() . '.json';
		$result   = array(
			'check' => $check,
			'host'  => $host,
			'date'  => $date,
			'items' => $items
		);

		// Set headers
		$app = $this->app;
		ob_end_clean();
		$app->clearHeaders();
		$app->setHeader('Content-Type', 'application/json', true);
		$app->setHeader('Content-Disposition', 'attachment; filename=' . $filename . ';', true);
		$app->sendHeaders();

		// Read result
		echo json_encode($result);

		// Close application
		$app->close();

		return true;
	}

	/**
	 * Method to import YOOtheme Pro library items.
	 *
	 * @throws  Exception
	 *
	 * @return boolean True on success, False on failure.
	 *
	 * @since  1.3.0
	 */
	protected function libraryImport()
	{
		// Get file
		$files = $this->app->input->files->get('files', array());
		$file  = (!empty($files[0])) ? $files[0] : false;
		if (!$file || $file['type'] !== 'application/json')
		{
			throw new Exception(Text::_('PLG_SYSTEM_JYPROEXTRA_ERROR_FILE_NOT_FOUND'), 404);
		}
		if (!$context = file_get_contents($file['tmp_name']))
		{
			throw new Exception(Text::_('PLG_SYSTEM_JYPROEXTRA_ERROR_FILE_NOT_FOUND'), 404);
		}
		if (!$json = @json_decode($context, true))
		{
			throw new Exception(Text::_('PLG_SYSTEM_JYPROEXTRA_ERROR_FILE_NOT_FOUND'), 404);
		}
		if (empty($json['check']) || $json['check'] !== 'jyproextra_library_export' || empty($json['items']))
		{
			throw new Exception(Text::_('PLG_SYSTEM_JYPROEXTRA_ERROR_FILE_NOT_FOUND'), 404);
		}

		// Get current
		$keys  = array();
		$names = array();
		$items = array();
		$db    = $this->db;
		$query = $db->getQuery(true)
			->select(array('e.extension_id', 'e.custom_data'))
			->from($db->quoteName('#__extensions', 'e'))
			->where($db->quoteName('e.type') . ' = ' . $db->quote('plugin'))
			->where($db->quoteName('e.element') . ' = ' . $db->quote('yootheme'))
			->where($db->quoteName('e.folder') . ' = ' . $db->quote('system'));
		if (!$plugin = $db->setQuery($query)->loadObject())
		{
			throw new Exception(Text::_('PLG_SYSTEM_JYPROEXTRA_ERROR_YOOTHEME_NOT_FOUND'), 404);
		}
		if ($custom_data = $plugin->custom_data)
		{
			$custom_data = json_decode($custom_data, true);

			if (!empty($custom_data['library']))
			{
				foreach ($custom_data['library'] as $key => $item)
				{
					$keys[]      = $key;
					$names[]     = $item['name'];
					$items[$key] = $item;
				}
			}
		}

		// Add new items
		foreach ($json['items'] as $key => $item)
		{
			// Check key
			while (in_array($key, $keys))
			{
				$key = $this->generateLibraryKey();
			}

			// Check name
			$name = $item['name'];
			$i    = 1;
			while (in_array($name, $names))
			{
				$i++;
				$name = $item['name'] . ' (' . $i . ')';
			}
			$item['name'] = $name;

			// Add to items
			$items[$key] = $item;
		}

		// Update plugin
		$plugin->custom_data            = ($custom_data) ? $custom_data : array();
		$plugin->custom_data['library'] = $items;
		$plugin->custom_data            = json_encode($plugin->custom_data);
		if (!$db->updateObject('#__extensions', $plugin, array('extension_id')))
		{
			throw new Exception(Text::_('PLG_SYSTEM_JYPROEXTRA_LIBRARY_IMPORT_FAILURE'), 500);
		}

		// Set message
		$this->app->setUserState('jyproextra_success_message', Text::_('PLG_SYSTEM_JYPROEXTRA_LIBRARY_IMPORT_SUCCESS'));

		return true;
	}

	/**
	 * Method to get plugin modal markup.
	 *
	 * @throws  Exception
	 *
	 * @return  object Modal markup on success, Exception on failure.
	 *
	 * @since  1.4.1
	 */
	protected function jYProExtraModal()
	{
		if (!Factory::getUser()->authorise('core.edit', 'com_plugins'))
		{
			throw new Exception(Text::_('JLIB_APPLICATION_ERROR_EDIT_NOT_PERMITTED'), 403);
		}

		$return          = new stdClass();
		$return->button  = LayoutHelper::render('plugins.system.jyproextra.customizer.modal.button');
		$return->content = LayoutHelper::render('plugins.system.jyproextra.customizer.modal.content');
		$return->style   = LayoutHelper::render('plugins.system.jyproextra.customizer.modal.style');

		return $return;
	}

	/**
	 * Method to redirect to site preview.
	 *
	 * @throws  Exception
	 *
	 * @since  1.6.0
	 */
	protected function sitePreview()
	{
		if ($preview = $this->app->input->getBase64('preview'))
		{
			$this->app->redirect(Route::_(base64_decode($preview), false), 301);
		}
		else
		{
			throw new Exception(Text::_('PLG_SYSTEM_JYPROEXTRA_ERROR_PREVIEW_NOT_FOUND'), 404);
		}
	}

	/**
	 * Method to generate random library key.
	 *
	 * @param   int  $length  Key length.
	 *
	 * @return  string  Library key.
	 *
	 * @since  1.3.0
	 */
	protected function generateLibraryKey($length = 8)
	{
		$secret = '';
		$chars  = array('a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'r', 's',
			't', 'u', 'v', 'x', 'y', 'z', 'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O',
			'P', 'R', 'S', 'T', 'U', 'V', 'X', 'Y', 'Z', 0, 1, 2, 3, 4, 5, 6, 7, 8, 9);
		for ($i = 0; $i < $length; $i++)
		{
			$key    = rand(0, count($chars) - 1);
			$secret .= $chars[$key];
		}

		return $secret;
	}

	/**
	 * Method to copy YOOtheme external files after install extension.
	 *
	 * @param   Installer  $installer  Installer object.
	 * @param   integer    $eid        Extension Identifier.
	 *
	 * @since  1.3.1
	 */
	public function onExtensionAfterInstall($installer, $eid)
	{
		if ($eid) $this->copyYOOthemeFiles($installer);
	}

	/**
	 * Method to copy YOOtheme external files after update extension.
	 *
	 * @param   Installer  $installer  Installer object.
	 * @param   integer    $eid        Extension Identifier.
	 *
	 * @since  1.3.1
	 */
	public function onExtensionAfterUpdate($installer, $eid)
	{
		if ($eid) $this->copyYOOthemeFiles($installer);
	}

	/**
	 * Method to copy YOOtheme external files.
	 *
	 * @param   Installer  $installer  Installer object.
	 *
	 * @since  1.3.1
	 */
	protected function copyYOOthemeFiles($installer)
	{
		$manifest = $installer->getManifest();
		if ((string) $manifest->attributes()['type'] === 'package' && (string) $manifest->packagename === 'yootheme')
		{
			JLoader::register('PlgSystemJYProExtraInstallerScript', Path::clean(__DIR__ . '/script.php'));
			(new PlgSystemJYProExtraInstallerScript())->copyYOOthemeFiles(new Installer());
		}
	}

	/**
	 * Method to check  is visitor authorized in control panel.
	 *
	 * @return  bool True if authorized administrator, False if is not.
	 *
	 * @since  1.5.0
	 */
	protected function isAuthorizedAdmin()
	{
		if ($this->_isAuthorizedAdmin === null)
		{
			$db    = $this->db;
			$admin = false;

			// Check on site
			if ($this->app->isClient('site'))
			{
				// Get sessions
				$sessions = array();
				foreach ($this->app->input->cookie->getArray() as $key => $value)
				{
					if (strlen($key) === 32 && strlen($value) === 32)
					{
						$sessions[] = $db->quote(trim($value));
					}
				}

				// Find administrator session
				if (!empty($sessions))
				{
					$query = $db->getQuery(true)
						->select('userid')
						->from($db->quoteName('#__session'))
						->where($db->quoteName('session_id') . ' IN (' . implode(',', $sessions) . ')')
						->where('time > '
							. Factory::getDate('- ' . Factory::getConfig()->get('lifetime', 15) . 'minute')->toUnix())
						->where('client_id = 1')
						->where('guest = 0');
					$admin = (!empty($db->setQuery($query)->loadResult()));
				}
			}

			// Check on control panel
			elseif ($this->app->isClient('administrator') && !Factory::getUser()->guest)
			{
				$admin = true;
			}

			$this->_isAuthorizedAdmin = $admin;
		}

		return $this->_isAuthorizedAdmin;
	}
}

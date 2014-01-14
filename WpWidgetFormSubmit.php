<?php
/*
Plugin Name: Widget Form Submit
Plugin URI: 
Description: A plugin that allows the easy collection of info in a widget form.
Author: Austin Matzko
Author URI: https://austinmatzko.com
Version: 1.0
Text Domain: wp-widget-form-submit
*/

if (! class_exists('WpWidgetFormSubmit')) {
	class WpWidgetFormSubmit
	{
		/**
		 * The form submit widgets that are currently registered.
		 */
		static protected $_widgets = array();

		/**
		 * Plugin class constructor.
		 */
		public function __construct()
		{
			add_action('init', array($this, 'set_up_widgets'));
		}

		/**
		 * Set up the widgets 
		 */
		public function set_up_widgets()
		{

			foreach(self::$_widgets as $widget) {
				$form = new Zend_Form();
				$view = new Zend_View();

				$instance = $widget->getInstance();

				$template_directories = apply_filters(
					'wp_widget_form_submit_template_directories',
					array(
						get_template_directory(),
						dirname(__FILE__) . DIRECTORY_SEPARATOR . 'templates',
					),
					$instance
				);

				$formIdElement = new Zend_Form_Element_Hidden('wp_widget_form_id');
				$formIdElement->setValue($widget->id);
				$formIdElement->id = 'form_identifier-' . $widget->id;

				$form
					->setView($view)
					->setAction('/')
					->setMethod('post')
					->setAttrib('id', $widget->id)
					->addElement($formIdElement);

				$form = apply_filters(
					'wp_widget_form_submit_form',
					$form, 
					$widget,
					$instance, 
					$widget->id_base
				);

				$view = apply_filters(
					'wp_widget_form_submit_view',
					$view, 
					$widget,
					$instance, 
					$widget->id_base
				);

				$widget->setForm($form);
				$widget->setView($view);
				$widget->buildForm();
				if (!empty($_POST['wp_widget_form_id']) && $widget->id == $_POST['wp_widget_form_id']) {
					$isValid = $form->isValid($_POST);
					if ($isValid) {
						$widget->whenFormPassesValidation();
					} else {
						$widget->whenFormFailsValidation();
					}
				} else {
					$isValid = null;
				}
			}
		}

		/**
		 * Add a widget to those that we know about.
		 *
		 * @param WP_Widget $widget The widget to register.
		 *
		 * @return void
		 */
		public static function registerWidget(WP_Widget $widget)
		{
			self::$_widgets[] = $widget;
		}
	}

	/**
	 * Initialize the plugin into a global.
	 */
	function initialize_wp_widget_form_submit_plugin()
	{
		global $wp_widget_form_submit_plugin;
		$wp_widget_form_submit_plugin = new WpWidgetFormSubmit();
	}

	/**
	 * Autoload classes used in this plugin.
	 *
	 * @param string $class The unknown class that PHP is looking for.
	 */
	function wp_widget_form_submit_autoloader($class = '')
	{
		if (
			0 === stripos($class,'Zend_Form') 
			|| 0 === stripos($class,'Zend_Validate') 
			|| 0 === stripos($class,'Zend_View') 
			|| 0 === stripos($class, 'WpWidgetFormSubmit'
		)) {
			if (preg_match('/^(WpWidgetFormSubmit|Zend)_(.*)/i',$class, $matches) && $matches[2]) {
				$subdirs = explode('_', $matches[2]);
				$class_file = dirname(__FILE__) . DIRECTORY_SEPARATOR . $matches[1];
				foreach($subdirs as $sub) {
					$class_file .= DIRECTORY_SEPARATOR . $sub;
				}
				$class_file .= '.php';
				if (file_exists($class_file)) {
					include_once $class_file;
				}
			}
		}
	}

	/**
	 * Attach the callback for initializing this plugin.
	 */
	add_action('plugins_loaded', 'initialize_wp_widget_form_submit_plugin');
	set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__));
	spl_autoload_register('wp_widget_form_submit_autoloader');
}

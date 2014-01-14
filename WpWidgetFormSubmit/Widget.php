<?php

/**
 * The Widget proper
 *
 */
abstract class WpWidgetFormSubmit_Widget 
extends WP_Widget 
implements WpWidgetFormSubmit_FormInterface
{
	/**
	 * The form associated with this widget.
	 *
	 * @var Zend_Form
	 */
	protected $_form;

	/**
	 * The view associated with this widget.
	 *
	 * @var Zend_View
	 */
	protected $_view;

	public function __construct() {
		$widget_ops = array(
			'classname' => $this->getWidgetClassName(),
			'description' => $this->getWidgetDescription(), 
		);
		parent::__construct($this->getWidgetClassName(), $this->getWidgetName(), $widget_ops);
		WpWidgetFormSubmit::registerWidget($this);
	}

	public function widget($args, $instance) 
	{
		extract($args);
		$title = apply_filters('widget_title', empty( $instance['title'] ) ? '' : $instance['title'], $instance, $this->id_base );

		echo $before_widget;
		if ($title) {
			echo $before_title . $title . $after_title;
		}

		echo $this->render();

		echo $after_widget;
	}

	/**
	 * Get the markup for the form as rendered.
	 *
	 * @return string The markup
	 */
	public function render()
	{
		$form = $this->getForm();
		$view = $this->getView();
		if (!empty($form) && !empty($view)) {
			$markup = $form->render($view);
			$markup = apply_filters(
				'wp_widget_form_submit_form_render',
				$markup,
				$form,
				$view,
				$this
			);
			return $markup;
		}
	}

	/**
	 * Set the form object to be associated with this widget.
	 *
	 * @param Zend_Form $form The form associated with this widget.
	 *
	 * @return WpWidgetFormSubmit_Widget 
	 */
	public function setForm(Zend_Form $form)
	{
		$this->_form = $form;
		return $this;
	}

	/**
	 * Get the form associated with this widget.
	 *
	 * @return Zend_Form
	 */
	public function getForm()
	{
		return $this->_form;
	}

	/**
	 * Set the view object to be associated with this widget.
	 *
	 * @param Zend_View $view The view associated with this widget.
	 *
	 * @return WpWidgetFormSubmit_Widget 
	 */
	public function setView(Zend_View $view)
	{
		$this->_view = $view;
		return $this;
	}

	/**
	 * Get the view associated with this widget.
	 *
	 * @return Zend_View
	 */
	public function getView()
	{
		return $this->_view;
	}

	/**
	 * Get the widget instance values.
	 *
	 * @return array The WP_Widget instance values.
	 */
	public function getInstance()
	{
		$settings = $this->get_settings();
		$instance = !empty($this->number) && isset($settings[$this->number]) ? $settings[$this->number] : null;
		return $instance;
	}
}

<?php

/**
 * The interface for a WP Widget form submit object.
 */
interface WpWidgetFormSubmit_FormInterface
{
	/**
	 * Get the widget's name.
	 *
	 * @return string
	 */
	public function getWidgetName();

	/**
	 * Get the widget class name.
	 *
	 * @return string 
	 */
	public function getWidgetClassName();

	/**
	 * Get the widget's description.
	 *
	 * @return string The description of the widget.
	 */
	public function getWidgetDescription();

	/**
	 * Callback invoked when constructing the form.  Should be used to add elements, validators, etc.
	 *
	 * @return WpWidgetFormSubmit_FormInterface
	 */
	public function buildForm();

	/**
	 * Callback invoked when the submitted form has been successfully validated.
	 *
	 * @return WpWidgetFormSubmit_FormInterface
	 */
	public function whenFormPassesValidation();

	/**
	 * Callback invoked when the submitted form has failed validation.
	 *
	 * @return WpWidgetFormSubmit_FormInterface
	 */
	public function whenFormFailsValidation();
}

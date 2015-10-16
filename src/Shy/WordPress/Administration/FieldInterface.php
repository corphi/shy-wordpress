<?php

namespace Shy\WordPress\Administration;



/**
 * Contract interface for backend form fields.
 * 
 * @license GPL-2.0+
 */
interface FieldInterface
{
	/**
	 * Label to use for display.
	 * 
	 * @return string
	 */
	public function getLabel();

	/**
	 * Render the field.
	 * 
	 * @param array $args {
	 *    @var string $name
	 *    @var string $input_name
	 *    @var string $label_for
	 *    @var mixed  $value
	 *    @var array  $attr
	 * }
	 * @return void
	 */
	public function renderField( array $args );

	/**
	 * Sanitize user input before saving it.
	 * 
	 * @param mixed    $value
	 * @param callable $reporter
	 * @return mixed
	 */
	public function sanitizeValue( $value, $reporter = '' );
}

<?php

namespace Shy\WordPress\Administration;



/**
 * WordPress’ default text field with an optional description.
 * 
 * @license GPL-2.0+
 */
class TextField extends AbstractSettingsField
{
	/**
	 * Render a setting as text field.
	 * 
	 * @param array $args {
	 *    @var string $name
	 *    @var string $input_name
	 *    @var string $label_for
	 *    @var string $value
	 *    @var array  $attr
	 *    @var string $description
	 * }
	 */
	public function renderField( array $args )
	{
		$this->renderInputTag( [
			'id'    => $args['label_for'],
			'class' => 'regular-text',
			'name'  => $args['input_name'],
			'value' => $args['value'],
		] + $args['attr'] + [
			'type'  => 'text',
		] );

		if ( isset( $args['description'] ) ) {
			$this->renderDescription( $args['description'] );
		}
	}
}

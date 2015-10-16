<?php

namespace Shy\WordPress\Administration;



/**
 * A checkbox.
 * 
 * @license GPL-2.0+
 */
class CheckboxField extends AbstractSettingsField
{
	/**
	 * @var string
	 */
	protected $caption;


	/**
	 * @param string $label
	 * @param string $caption
	 */
	public function __construct( $label, $caption = '' )
	{
		parent::__construct( $label );

		$this->caption = (string) $caption ?: $this->label;
	}


	/**
	 * Render a setting as checkbox.
	 * 
	 * @param array $args {
	 *    @var string $name
	 *    @var string $input_name
	 *    @var string $label_for
	 *    @var bool   $value
	 *    @var array  $attr
	 *    @var string $description
	 * }
	 */
	public function renderField( array $args )
	{
		echo '<label>';
		$this->renderInputTag( [
			'type'    => 'checkbox',
			'id'      => isset( $args['label_for'] ) ? $args['label_for'] : null,
			'name'    => $args['input_name'],
			'value'   => '1',
			'checked' => $args['value'] ? 'checked' : null,
		] + $args['attr'] );
		echo ' ' . esc_html( $this->caption ) . '</label>';

		if ( isset( $args['description'] ) ) {
			$this->renderDescription( $args['description'] );
		}
	}

	public function sanitizeValue( $value, $reporter = '' )
	{
		return (bool) $value;
	}
}

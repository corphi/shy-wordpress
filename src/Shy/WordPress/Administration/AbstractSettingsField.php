<?php

namespace Shy\WordPress\Administration;



/**
 * Base implementation for setting fields.
 * 
 * @license GPL-2.0+
 */
abstract class AbstractSettingsField implements FieldInterface
{
	/**
	 * Label to use for display.
	 * 
	 * @var string
	 */
	protected $label;


	/**
	 * @param string $label
	 */
	public function __construct( $label )
	{
		$this->label = (string) $label;
	}


	public function getLabel()
	{
		return $this->label;
	}

	/**
	 * Helper function to output an input tag with given HTML attributes.
	 * 
	 * Attribute values are properly escaped.
	 * 
	 * @param array $attr
	 * @return void
	 */
	protected function renderInputTag( array $attr )
	{
		echo '<input';
		foreach ( $attr as $key => $value ) {
			if ( null !== $value ) {
				printf( ' %s="%s"', $key, esc_attr( $value ) );
			}
		}
		echo ' />';
	}

	/**
	 * Render a description for a field, usually underneath it.
	 * 
	 * @param string $description
	 * @return void
	 */
	protected function renderDescription( $description )
	{
		printf( '<p class="description">%s</p>', esc_html( $description ) );
	}

	public function sanitizeValue( $value, $reporter = '' )
	{
		return $value;
	}
}

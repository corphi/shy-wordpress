<?php

namespace Shy\WordPress\Administration;

use Shy\WordPress\CompositeOption;



/**
 * A page displaying settings via the WordPress Settings API.
 * 
 * @license GPL-2.0+
 */
abstract class AbstractSettingsPage extends AbstractPage
{
	/**
	 * @var CompositeOption
	 */
	protected $settings;

	/**
	 * Fields on this page.
	 * 
	 * @var array<string, FieldInterface>
	 */
	protected $fields;


	/**
	 * @param CompositeOption $settings
	 * @param string          $slug     optional, use settings’ slug if omitted
	 */
	public function __construct( CompositeOption $settings, $slug = '' )
	{
		$this->settings = $settings;

		if ( ! strlen( $slug ) ) {
			$slug = $settings->getSlug();
		}
		parent::__construct( $slug, 'manage_options' );

		$this->addHookMethod( 'admin_init', 'registerSettings' );
	}


	/**
	 * Make all fields known to WordPress Settings API.
	 * 
	 * @return void
	 */
	public function registerSettings()
	{
		$this->buildFields();

		register_setting(
			$this->slug,
			$this->settings->getSlug(),
			[ $this, 'sanitizeSettings' ]
		);
	}

	/**
	 * Add fields for the settings.
	 * 
	 * @return void
	 */
	abstract public function buildFields();


	/**
	 * Sanitize setting values after form submission.
	 * 
	 * @param array $options
	 * @return array
	 */
	public function sanitizeSettings( $options )
	{
		foreach ( $this->fields as $name => $field ) {
			$value = isset( $options[ $name ] ) ? $options[ $name ] : null;
			$value = $field->sanitizeValue(
				$value,
				function ( $message ) use ( $name ) {
					$this->addError( $name, $message );
				}
			);
			$options[ $name ] = $value;
		}

		return $options;
	}


	/**
	 * Section to add fields to.
	 * 
	 * Parameter default from add_settings_field().
	 * 
	 * @var string
	 */
	protected $currentSection = 'default';

	/**
	 * @return string
	 */
	public function getCurrentSection()
	{
		return $this->currentSection;
	}

	/**
	 * Add a new section and return its generated name.
	 *
	 * @param string $title optional, can be empty
	 * @param string $name  optional, will be generated if empty
	 * @return string
	 */
	protected function addSection( $title = '', $name = '' )
	{
		$name = (string) $name;
		if ( ! strlen( $name ) ) {
			$name = $this->slug . '-section' . ( count( $this->getSections() ) + 1 );
		}

		add_settings_section(
			$name,
			esc_html( $title ),
			[ $this, 'renderSectionTeaser' ],
			$this->slug
		);

		return $this->currentSection = $name;
	}

	/**
	 * Callback before output of section fields.
	 * 
	 * Teasers must escape their output themselves.
	 * 
	 * @param array $section {
	 *    @var string   $id
	 *    @var string   $title
	 *    @var callable $callback
	 * }
	 */
	public function renderSectionTeaser( array $section )
	{
	}

	/**
	 * Get all known section names on this page.
	 * 
	 * @global $wp_settings_fields
	 * @return string[]
	 */
	public function getSections()
	{
		global $wp_settings_fields;

		return isset( $wp_settings_fields[ $this->slug ] )
			? array_keys( $wp_settings_fields[ $this->slug ] )
			: [];
	}

	/**
	 * @global $wp_settings_fields
	 * @param string $section
	 * @return array<string, array {
	 *    @var string   $id
	 *    @var string   $title
	 *    @var callable $callback
	 *    @var array    $args
	 * }>
	 */
	public function getFieldsForSection( $section )
	{
		global $wp_settings_fields;

		return $wp_settings_fields[ $this->slug ][ $section ];
	}

	/**
	 * Add a field to this setting page.
	 * 
	 * FieldInterfaces are registered with $this->fields.
	 * 
	 * @param string                $name
	 * @param FieldInterface|string $field
	 * @param array                 $args
	 * @param callable              $callback ignored if passed a FieldInterface
	 */
	protected function addField( $name, $field, array $args = [], $callback = '' )
	{
		if ( $field instanceof FieldInterface ) {
			$this->fields[ $name ] = $field;

			$args = [
				'name'       => $name,
				'input_name' => $this->slug . '[' . $name . ']',
				'value'      => $this->settings[ $name ],
			] + $args + [
				'label_for'  => $this->slug . '-' . $name,
				'attr'       => [],
			];
			$callback = [ $field, 'renderField' ];
			$field    = $field->getLabel();
		} elseif ( ! is_callable( $callback ) ) {
			throw new \InvalidArgumentException( 'Parameter $callback must be callable.' );
		}

		add_settings_field(
			$name,
			esc_html( $field ),
			$callback,
			$this->slug,
			$this->currentSection,
			$args
		);
	}

	/**
	 * Add an error.
	 * 
	 * @param string $code
	 * @param string $message
	 */
	public function addError( $code, $message )
	{
		add_settings_error( $this->settings->getSlug(), $code, $message );
	}

	/**
	 * Errors for this setting.
	 * 
	 * @return array {
	 *    @var string $setting
	 *    @var string $code
	 *    @var string $message
	 *    @var string $type    'error'
	 * }
	 */
	public function getErrors()
	{
		return get_settings_errors( $this->settings->getSlug() );
	}


	/**
	 * Output settings page.
	 */
	public function renderPage()
	{
		if ( ! current_user_can( $this->capability ) ) {
			wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
		}

		?>
		<div class="wrap">
			<h2><?php echo esc_html( $this->getTitle() ); ?></h2>
			<form action="options.php" method="post">
				<?php settings_errors( 'general' ); // “Settings saved.” message ?>
				<?php settings_fields( $this->slug ); // Settings group ?>
				<?php do_settings_sections( $this->slug ); // Page ?>
				<?php submit_button(); ?>
			</form>
		</div>
		<?php
	}
}

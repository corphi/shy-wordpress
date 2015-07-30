<?php

namespace Shy\WordPress;



/**
 * Abstracts common functionality and escaping for the Settings API.
 * 
 * TODO: Check slug and field names for illegal characters.
 * TODO: Refactor to not extend but use CompositeOption
 */
abstract class SettingsPage extends CompositeOption
{
	use HookableTrait;


	/**
	 * @var string
	 */
	protected $capability;


	/**
	 * Slug (file name) of the parent menu entry.
	 * 
	 * @see add_submenu_page() for suggestions.
	 * 
	 * @return string|null
	 */
	protected function getParentSlug()
	{
		return 'options-general.php';
	}

	/**
	 * Title for this setting page.
	 * 
	 * @return string
	 */
	abstract protected function getPageTitle();

	/**
	 * String to show in the menu entry.
	 * 
	 * @return string
	 */
	protected function getMenuTitle()
	{
		return $this->getPageTitle();
	}


	/**
	 * @param string $slug       Page slug
	 * @param string $capability Required capability to view
	 */
	protected function __construct( $slug, $capability = 'manage_options' )
	{
		parent::__construct( $slug );

		$this->capability = (string) $capability;

		$this->addHookMethod( 'admin_menu', 'registerPage' );
		$this->addHookMethod( 'admin_init', 'registerSettings' );
	}

	/**
	 * Register our options page.
	 * 
	 * @return void
	 */
	public function registerPage()
	{
		add_submenu_page(
			$this->getParentSlug(),
			$this->getPageTitle(),
			$this->getMenuTitle(),
			$this->capability,
			$this->slug,
			array( $this, 'renderPage' )
		);
	}

	/**
	 * Register the actual settings.
	 * Override and use addSection() and add*Field() methods.
	 * 
	 * @return void
	 */
	public function registerSettings()
	{
		register_setting(
			$this->slug,
			$this->slug,
			array( $this, 'sanitizeOptions' )
		);
	}

	/**
	 * Sanitize option values after form submission.
	 * 
	 * @param array $options
	 * @return array
	 */
	abstract public function sanitizeOptions( array $options );


	/**
	 * Section to add fields to.
	 * 
	 * Parameter default from add_settings_field().
	 * 
	 * @var string
	 */
	protected $currentSection = 'default';

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
			array( $this, 'renderSectionTeaser' ),
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
	 *    @type string   $id
	 *    @type string   $title
	 *    @type callable $callback
	 * }
	 */
	public function renderSectionTeaser( array $section )
	{
	}

	/**
	 * Get all known section names on this page.
	 * 
	 * @global $wp_settings_fields
	 * @return array<string>
	 */
	public function getSections()
	{
		global $wp_settings_fields;

		if ( ! isset( $wp_settings_fields[ $this->slug ] ) ) {
			return array();
		}

		return array_keys( $wp_settings_fields[ $this->slug ] );
	}

	/**
	 * @global $wp_settings_fields
	 * @param string $section
	 * @return array<string, array {
	 *    @type string   $id
	 *    @type string   $title
	 *    @type callable $callback
	 *    @type array    $args
	 * }>
	 */
	public function getFieldsForSection( $section )
	{
		global $wp_settings_fields;

		return $wp_settings_fields[ $this->slug ][ $section ];
	}

	/**
	 * Add a custom field to this setting page.
	 * 
	 * @param string   $name
	 * @param string   $label
	 * @param callable $callback
	 * @param array    $args
	 */
	protected function addField( $name, $label, $callback, $args = array() )
	{
		if ( ! is_callable( $callback ) ) {
			throw new \InvalidArgumentException( 'Parameter $callback must be callable.' );
		}

		add_settings_field(
			$name,
			esc_html( $label ),
			$callback,
			$this->slug,
			$this->currentSection,
			$args
		);
	}

	/**
	 * Add a text field to this settings page.
	 * 
	 * @param string $name
	 * @param string $label
	 * @param array  $args
	 * @param string $callback
	 */
	protected function addTextField( $name, $label, $args = array(), $callback = '' )
	{
		if ( ! $callback || ! is_callable( $callback ) ) {
			$callback = array( $this, 'renderTextField' );
		}

		$this->addField(
			$name,
			$label,
			$callback,
			$args + array(
				'label_for' => $this->slug . '-' . $name,
				'name'      => $name,
				'attr'      => array(),
			)
		);
	}

	/**
	 * @param string   $name
	 * @param string   $label
	 * @param string   $caption
	 * @param array    $args
	 * @param callable $callback
	 */
	protected function addCheckboxField( $name, $label, $caption, $args = array(), $callback = '' )
	{
		if ( ! $callback || ! is_callable( $callback ) ) {
			$callback = array( $this, 'renderCheckboxField' );
		}

		$this->addField(
			$name,
			$label,
			$callback,
			$args + array(
				'label_for' => $this->slug . '-' . $name,
				'name'      => $name,
				'caption'   => $caption,
				'attr'      => array(),
			)
		);
	}

	/**
	 * Add an error.
	 * 
	 * @param string $code
	 * @param string $message
	 */
	protected function addError( $code, $message )
	{
		add_settings_error( $this->slug, $code, $message );
	}

	/**
	 * Errors for this setting.
	 * 
	 * @return array {
	 *    @type string $setting
	 *    @type string $code
	 *    @type string $message
	 *    @type string $type 'error'
	 * }
	 */
	public function getErrors()
	{
		return get_settings_errors( $this->slug );
	}


	/**
	 * Render a setting as text field.
	 * 
	 * @param array $args {
	 *    @type string $name
	 *    @type string $label_for
	 *    @type array  $attr
	 * }
	 */
	public function renderTextField( array $args )
	{
		$name = $args['name'];

		$this->renderInputTag( array(
			'type'  => 'text',
			'id'    => $args['label_for'],
			'class' => 'regular-text',
			'name'  => $this->slug . '[' . $name . ']',
			'value' => $this[ $name ],
		) + $args['attr'] );
	}

	/**
	 * Render a setting as checkbox.
	 * 
	 * @param array $args {
	 *    @type string $caption
	 *    @type string $name
	 *    @type string $label_for
	 *    @type array  $attr
	 * }
	 */
	public function renderCheckboxField( array $args )
	{
		$name = $args['name'];

		echo '<label>';
		$this->renderInputTag( array(
			'type'    => 'checkbox',
			'id'      => isset( $args['label_for'] ) ? $args['label_for'] : null,
			'name'    => $this->slug . '[' . $name . ']',
			'value'   => '1',
			'checked' => $this[ $name ] ? 'checked' : null,
		) + $args['attr'] );
		echo ' ' . esc_html( $args['caption'] ) . '</label>';
	}

	/**
	 * Output an input tag with given HTML attributes.
	 * 
	 * @param array $attr
	 */
	protected function renderInputTag( array $attr )
	{
		echo '<input';
		foreach ( $attr as $k => $v ) {
			if ( null !== $v ) {
				printf( ' %s="%s"', $k, esc_attr( $v ) );
			}
		}
		echo ' />';
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
			<h2><?php echo esc_html( $this->getPageTitle() ); ?></h2>
			<form action="options.php" method="post">
				<?php settings_errors( 'general' ); // “Settings saved.” message ?>
				<?php settings_fields( $this->slug ); ?>
				<?php do_settings_sections( $this->slug ); ?>
				<?php submit_button(); ?>
			</form>
		</div>
		<?php
	}
}

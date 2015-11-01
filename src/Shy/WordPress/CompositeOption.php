<?php

namespace Shy\WordPress;



/**
 * An array option with default values for its suboptions.
 * 
 * @license GPL-2.0+
 */
abstract class CompositeOption implements \ArrayAccess, \Countable, \IteratorAggregate
{
	use HookableTrait;


	/**
	 * @var string
	 */
	protected $slug;

	/**
	 * @return string
	 */
	public function getSlug()
	{
		return $this->slug;
	}


	/**
	 * @param string $slug
	 */
	protected function __construct( $slug )
	{
		$this->slug = (string) $slug;

		$this->addHookMethod( 'default_option_' . $this->slug, 'getDefaults' );
		$this->addHookMethod( 'option_' . $this->slug, 'filterOptionMergeDefaults' );
	}


	/**
	 * Return default values for all suboptions.
	 * Hooked into get_option() defaults.
	 * 
	 * @return array
	 */
	abstract public function getDefaults();


	/**
	 * Merge default values if missing in database.
	 * 
	 * @param array $option
	 * @return array
	 */
	public function filterOptionMergeDefaults( $option )
	{
		return (array) $option + $this->getDefaults();
	}

	/**
	 * @param string $offset
	 * @return mixed
	 */
	public function getDefault( $offset )
	{
		return $this->getDefaults()[ $offset ];
	}


	public function offsetExists( $offset )
	{
		$settings = get_option( $this->slug );

		return isset( $settings[ $offset ] );
	}

	public function offsetGet( $offset )
	{
		$settings = get_option( $this->slug );
		if ( ! isset( $settings[ $offset ] ) && ! array_key_exists( $offset, $settings ) ) {
			throw new \OutOfBoundsException( "There is no setting '$offset'." );
		}

		return $settings[ $offset ];
	}

	public function offsetSet( $offset, $value )
	{
		$settings = get_option( $this->slug );
		$settings[ $offset ] = $value;
		update_option( $this->slug, $settings );
	}

	public function offsetUnset( $offset )
	{
		$settings = get_option( $this->slug );
		unset( $settings[ $offset ] );

		update_option( $this->slug, $settings );
	}


	public function count()
	{
		return count( get_option( $this->slug ) );
	}


	public function getIterator()
	{
		return new \ArrayIterator( get_option( $this->slug ) );
	}


	/**
	 * Merge another set of options into this one.
	 * 
	 * @param array $settings
	 * @param bool  $overwrite
	 */
	public function merge( array $settings, $overwrite = false )
	{
		if ( ! empty( $settings ) ) {
			if ( $overwrite ) {
				$settings = $settings + get_option( $this->slug, [] );
			} else {
				$settings = get_option( $this->slug, [] ) + $settings;
			}

			update_option( $this->slug, $settings );
		}
	}

	/**
	 * Remove the entire option from the database.
	 * 
	 * @return void
	 */
	public function clear()
	{
		delete_option( $this->slug );
	}

	/**
	 * Remove all suboptions that have no default values.
	 * 
	 * @return void
	 */
	public function clearNonDefault()
	{
		$defaults = $this->getDefaults();

		$settings = get_option( $this->slug );
		$settings = array_intersect_key( $settings, $defaults );
		$settings = array_diff_assoc( $settings, $defaults );

		update_option( $this->slug, $settings );
	}
}

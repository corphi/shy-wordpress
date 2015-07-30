<?php

namespace Shy\WordPress;



/**
 * A composite option with a fixed number of suboptions and their default values.
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


	protected function __construct( $slug )
	{
		$this->slug = (string) $slug;

		$this->addHookMethod( 'default_option_' . $this->slug, 'getDefaults' );
	}


	/**
	 * Return default values for all suboptions.
	 * Hooked into get_option() defaults.
	 *
	 * @return array
	 */
	abstract public function getDefaults();

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
		if ( ! isset( $settings[ $offset ] ) ) {
			throw new \OutOfBoundsException( "There is no setting '$offset'." );
		}

		return $settings[ $offset ];
	}

	public function offsetSet( $offset, $value )
	{
		$settings = get_option( $this->slug );
		if ( ! isset( $settings[ $offset ] ) ) {
			throw new \OutOfBoundsException( "There is no setting '$offset'." );
		}

		$settings[ $offset ] = $value;
		update_option( $this->slug, $settings );
	}

	public function offsetUnset( $offset )
	{
		throw new \BadMethodCallException( 'You cannot unset settings.' );
	}


	public function count()
	{
		return count( $this->getDefaults() );
	}


	public function getIterator()
	{
		return new \ArrayIterator( get_option( $this->slug ) );
	}
}

<?php

namespace Shy\WordPress;



/**
 * A composite option for use with a settings page.
 * 
 * @license GPL-2.0+
 */
abstract class Settings extends CompositeOption
{
	/**
	 * @param string $slug
	 */
	public function __construct( $slug )
	{
		parent::__construct( $slug );

		$this->addHookMethod( 'pre_update_option_' . $this->slug, 'mergeOldSettings' );
	}


	/**
	 * Merge old values, if the settings page doesn’t show fields for all settings.
	 * 
	 * @param array $new Value to be assigned
	 * @param array $old Current saved value
	 * @return array
	 */
	public function mergeOldSettings( array $new, array $old )
	{
		return $new + $old;
	}
}

<?php

namespace Shy\WordPress;



/**
 * Settings using the native WordPress theme settings option.
 * 
 * @license GPL-2.0+
 */
abstract class ThemeSettings extends Settings
{
	/**
	 * @param string $theme Theme slug
	 */
	public function __construct( $theme )
	{
		parent::__construct( 'theme_mods_' . $theme );
	}
}

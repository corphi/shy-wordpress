<?php

/**
 * Try to load a Shy WordPress class.
 * 
 * @param string $name
 * @return bool
 */
function shy_wordpress_autoloader( $name )
{
	if ( substr( $name, 0, 14 ) !== 'Shy\\WordPress\\' ) {
		return false;
	}

	$name = __DIR__ . DIRECTORY_SEPARATOR . str_replace( '\\', DIRECTORY_SEPARATOR, $name ) . '.php';

	return is_file( $name ) && include( $name );
}

spl_autoload_register( 'shy_wordpress_autoloader' );

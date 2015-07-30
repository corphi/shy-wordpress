<?php

/**
 * Try to load a Shy WordPress test class.
 * 
 * @param string $name
 * @return boolean
 */
function shy_wordpress_tests_autoloader( $name )
{
	if ( substr( $name, 0, 20 ) !== 'Shy\\WordPress\\Tests\\' ) {
		return false;
	}

	$name = __DIR__ . '/' . str_replace( '\\', DIRECTORY_SEPARATOR, $name ) . '.php';

	return is_file( $name ) && include( $name );
}

spl_autoload_register( 'shy_wordpress_tests_autoloader' );

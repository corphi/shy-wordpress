<?php
/**
 * PHPUnit bootstrap file
 * 
 * Variant of the one from github.com/tierra/wordpress-plugins-tests
 */

require_once '../src/autoloader.php';
require_once 'autoloader.php';



require_once ( getenv( 'WP_DEVELOP_DIR' ) ?: '../../../..' )
	. '/tests/phpunit/includes/bootstrap.php';

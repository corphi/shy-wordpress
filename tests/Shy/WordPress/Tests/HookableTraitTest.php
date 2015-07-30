<?php

namespace Shy\WordPress\Tests;

use Shy\WordPress\HookableTrait;



/**
 * Check that HookableTrait actually works.
 * 
 * @author Philipp Cordes <pc@irgendware.net>
 */
class HookableTraitTest extends \WP_UnitTestCase
{
	use HookableTrait;


	public function actionMethod()
	{
	}

	public function testWorksAsAction()
	{
		$this->addHookMethod( 'shywp_test_action', 'actionMethod' );
		$this->assertTrue( has_action( 'shywp_test_action' ), 'Registering an action via addHookMethod() worked.' );
	}


	public function filterMethod( $value )
	{
		return $value;
	}

	public function testWorksAsFilter()
	{
		$this->addHookMethod( 'shywp_test_filter', 'filterMethod' );
		$this->assertTrue( has_filter( 'shywp_test_filter' ), 'Registering a filter via addHookMethod() worked.' );
	}
}

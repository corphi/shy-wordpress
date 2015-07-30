<?php

namespace Shy\WordPress;



/**
 * Making actions and filters how they should be.
 * 
 * Default to pass all arguments.
 */
trait HookableTrait
{
	/**
	 * @param string $action_or_filter
	 * @param string $method
	 * @param int    $priority
	 * @param int    $acceptedArgs
	 */
	protected function addHookMethod( $action_or_filter, $method, $priority = 10, $acceptedArgs = 99 )
	{
		add_filter( $action_or_filter, array( $this, $method ), $priority, $acceptedArgs );
	}

	/**
	 * @param string $action_or_filter
	 * @param string $method
	 * @param int    $priority
	 * @param int    $acceptedArgs
	 */
	protected function removeHookMethod( $action_or_filter, $method, $priority = 10, $acceptedArgs = 99 )
	{
		remove_filter( $action_or_filter, array( $this, $method ), $priority, $acceptedArgs );
	}
}

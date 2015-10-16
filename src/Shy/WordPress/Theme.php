<?php

namespace Shy\WordPress;



/**
 * Boilerplate for themes.
 * 
 * @license GPL-2.0+
 */
abstract class Theme extends Plugin
{
	public function __construct()
	{
		$GLOBALS['content_width'] = $this->getContentWidth();
	}


	/**
	 * @return int
	 */
	abstract public function getContentWidth();
}

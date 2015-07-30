<?php

namespace Shy\WordPress;



abstract class Theme extends Plugin
{
	public function __construct()
	{
		$GLOBALS['content_width'] = $this->getContentWidth();
	}

	/**
	 * @return integer
	 */
	abstract public function getContentWidth();
}

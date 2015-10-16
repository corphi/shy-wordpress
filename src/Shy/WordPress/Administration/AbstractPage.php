<?php

namespace Shy\WordPress\Administration;

use Shy\WordPress\HookableTrait;



/**
 * A backend page.
 * 
 * @license GPL-2.0+
 */
abstract class AbstractPage
{
	use HookableTrait;


	/**
	 * Slug to identify this page.
	 * 
	 * @var string
	 */
	protected $slug;

	/**
	 * Required capability to view.
	 * 
	 * @var string
	 */
	protected $capability;


	/**
	 * @param string $slug
	 * @param string $capability
	 */
	public function __construct( $slug, $capability )
	{
		$this
			->setSlug( $slug )
			->setCapability( $capability )
			->addHookMethod( 'admin_menu', 'registerPage' )
		;
	}


	/**
	 * @param string $slug
	 * @return $this
	 */
	public function setSlug( $slug )
	{
		$this->slug = (string) $slug;

		return $this;
	}

	/**
	 * Return slug for this menu entry.
	 * 
	 * @return string
	 */
	public function getSlug()
	{
		return $this->slug;
	}

	/**
	 * Slug (file name) of the parent menu entry.
	 * 
	 * @see add_submenu_page() for suggestions.
	 * 
	 * @return string|null
	 */
	public function getParentSlug()
	{
		return 'options-general.php';
	}


	/**
	 * @param string $capability
	 * @return $this
	 */
	public function setCapability( $capability )
	{
		$this->capability = (string) $capability;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getCapability()
	{
		return $this->capability;
	}


	/**
	 * Title for this page.
	 * 
	 * @return string
	 */
	abstract protected function getTitle();

	/**
	 * String to show in the menu entry.
	 * 
	 * @return string
	 */
	protected function getMenuTitle()
	{
		return $this->getTitle();
	}


	/**
	 * Register our options page.
	 *
	 * @return void
	 */
	public function registerPage()
	{
		add_submenu_page(
			$this->getParentSlug(),
			$this->getTitle(),
			$this->getMenuTitle(),
			$this->capability,
			$this->slug,
			[ $this, 'renderPage' ]
		);
	}

	/**
	 * Output the page.
	 * 
	 * @return void
	 */
	abstract public function renderPage();
}

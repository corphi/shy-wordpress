<?php

namespace Shy\WordPress\Tests;

use Shy\WordPress\SettingsPage;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit_Framework_MockObject_Builder_InvocationMocker as BuilderInvocationMocker;



class SettingsPageTest extends \WP_UnitTestCase
{
	/**
	 * Mock a SettingsPage.
	 * 
	 * @param string|null $slug
	 * @param string      $capability
	 * @return SettingsPage|MockObject {
	 *    @method BuilderInvocationMocker method(string)
	 * }
	 */
	protected function mockSettingsPage( $slug = null, $capability = 'manage_options' )
	{
		$builder = $this->getMockBuilder( 'Shy\WordPress\SettingsPage' )
			->enableProxyingToOriginalMethods();

		if ( null === $slug ) {
			$builder->disableOriginalConstructor();
		} else {
			$builder->setConstructorArgs( array( $slug, $capability ) );
		}

		return $builder->getMock();
	}


	/**
	 * Test reading defaults from the settings page.
	 * 
	 * @covers SettingsPage::__construct()
	 * @covers SettingsPage::getDefaults()
	 * @covers SettingsPage::offsetExists()
	 * @covers SettingsPage::offsetGet()
	 * @expectedException OutOfBoundsException
	 */
	public function testReading()
	{
		$slug     = 'shywp_settingspage_test_slug_reading';
		$defaults = array( 'foo' => 'bar' );

		$page = $this->mockSettingsPage( $slug );
		$page->method( 'getDefaults' )->willReturn( $defaults );

		$this->assertEquals( $defaults, get_option( $slug ) );

		$this->assertArrayHasKey( 'foo', $page );
		$this->assertEquals( $defaults['foo'], $page['foo'] );

		$this->assertArrayNotHasKey( 'baz', $page );
		$page['baz'];
	}

	/**
	 * Test writing to the settings page.
	 * 
	 * @covers SettingsPage::offsetSet()
	 * @expectedException OutOfBoundsException
	 */
	public function testWriting()
	{
		$slug     = 'shywp_settingspage_test_slug_writing';
		$defaults = array( 'foo' => 'bar' );

		$page = $this->mockSettingsPage( $slug );
		$page->method( 'getDefaults' )->willReturn( $defaults );

		$page['foo'] = 'foo';
		$this->assertEquals( 'foo', $page['foo'] );
		$page['baz'] = '123';
	}

	/**
	 * Fail to remove a setting.
	 * 
	 * @covers SettingPage::offsetUnset()
	 * @expectedException BadMethodCallException
	 */
	public function testRemoving()
	{
		$slug = 'shywp_settingspage_test_slug_removing';
		$defaults = array();

		$page = $this->mockSettingsPage( $slug );
		$page->method( 'getDefaults' )->willReturn( $defaults );

		unset( $page['baz'] );
	}


	/**
	 * Test whether the settings page can be showed.
	 * 
	 * @covers SettingsPage::__construct()
	 * @covers SettingsPage::getParentSlug()
	 * @covers SettingsPage::getPageTitle()
	 * @covers SettingsPage::getMenuTitle()
	 */
	public function testRegisterPage()
	{
		$this->expectOutputRegex( '/&lt;page&amp;title&gt;/' );

		$slug = 'shywp_settingspage_test_slug_registerpage';

		$page = $this->mockSettingsPage( $slug );
		$page->method( 'getParentSlug' )->willReturn( 'index.php' );
		$page->method( 'getPageTitle' )->willReturn( '<page&title>' );
		$page->method( 'getMenuTitle' )->willReturn( '<menu&title>' );

		$page->expects( $this->once() )->method( 'registerPage' )->with();
		$page->expects( $this->once() )->method( 'registerSettings' )->with();

		// FIXME: Simulate display of backend.
	}


	/**
	 * @covers SettingsPage::sanitizeOptions()
	 */
	public function testSanitize()
	{
		$slug = 'shywp_settingspage_test_slug_sanitize';

		$page = $this->mockSettingsPage( $slug );
		$page->method( 'sanitizeOptions' )->will( $this->returnArgument( 0 ) );
		$page->expects( $this->atLeastOnce() )->method( 'sanitizeOptions' );

		$this->markTestIncomplete();
		// FIXME: Simulate form submission
	}

	public function testRenderTextField()
	{
		$this->expectOutputRegex( '/^<input type="text"/' );

		$page = $this->mockSettingsPage();
		$page->renderTextField( array(
			'label_for' => 'foo',
			'name'      => 'bar',
		) );
	}

	public function testRenderCheckboxField()
	{
		$this->expectOutputRegex( '/^<label><input type="checkbox"/' );

		$page = $this->mockSettingsPage();
		$page->renderCheckboxField( array(
			'label_for' => 'foo',
			'name'      => 'bar',
			'caption'   => 'baz',
		) );
	}

	public function testRenderPage()
	{
		$this->markTestIncomplete();
		$this->expectOutputRegex( '/<form action="options.php" method="post">.*&lt;3&amp;&gt;.*cryptic_teaser.*</form>/' );

		$slug   = 'shywp_settingspage_test_slug_renderpage';

		$page = $this->mockSettingsPage( $slug, 'read' );
		$page->method( 'getPageTitle' )->willReturn( '<3&>' );
		$page->method( 'renderSectionTeaser' )->will( $this->returnCallback( function () use ( $teaser ) {
			echo 'cryptic_teaser';
		} ) );

		// FIXME: Simulate view of the settings page
		$page->renderPage();
	}
}

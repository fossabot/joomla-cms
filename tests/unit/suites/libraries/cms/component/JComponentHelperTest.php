<?php
/**
 * @package     Joomla.UnitTest
 * @subpackage  Component
 *
 * @copyright   Copyright (C) 2005 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

/**
 * Test class for JComponentHelper.
 *
 * @package     Joomla.UnitTest
 * @subpackage  Component
 * @since       3.2
 */
class JComponentHelperTest extends TestCaseDatabase
{
	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 *
	 * @return  void
	 *
	 * @since   4.0
	 */
	protected function tearDown()
	{
		TestReflection::setValue('JComponentHelper', 'components', array());

		parent::tearDown();
	}

	/**
	 * Gets the data set to be loaded into the database during setup
	 *
	 * @return  \PHPUnit\DbUnit\DataSet\CsvDataSet
	 *
	 * @since   3.2
	 */
	protected function getDataSet()
	{
		$dataSet = new \PHPUnit\DbUnit\DataSet\CsvDataSet(',', "'", '\\');

		$dataSet->addTable('jos_extensions', JPATH_TEST_DATABASE . '/jos_extensions.csv');

		return $dataSet;
	}

	/**
	 * Test JComponentHelper::getComponent
	 *
	 * @return  void
	 *
	 * @since   3.2
	 * @covers  JComponentHelper::getComponent
	 */
	public function testGetComponent()
	{
		$component = JComponentHelper::getComponent('com_content');

		$this->assertEquals(22, $component->id,	'com_content is extension ID 22');
		$this->assertInstanceOf('Joomla\\Registry\\Registry', $component->params, 'Parameters need to be a Registry instance');
		$this->assertEquals('1', $component->params->get('show_title'), 'The show_title parameter of com_content should be set to 1');
		$this->assertSame($component, JComponentHelper::getComponent('com_content'), 'The object returned must always be the same');
	}

	/**
	 * Test JComponentHelper::getComponent
	 *
	 * @return  void
	 *
	 * @since   3.4
	 * @covers  JComponentHelper::getComponent
	 */
	public function testGetComponent_falseComponent()
	{
		$component = JComponentHelper::getComponent('com_false');
		$this->assertEmpty($component->id,	'Anonymous component has no extension ID');
		$this->assertInstanceOf('Joomla\\Registry\\Registry', $component->params, 'Parameters need to be a Registry instance');
		$this->assertEquals(0, $component->params->count(), 'Anonymous component does not have any set parameters');
		$this->assertTrue($component->enabled, 'The anonymous component has to be enabled by default if not strict');
	}

	/**
	 * Test JComponentHelper::getComponent
	 *
	 * @return  void
	 *
	 * @since   3.4
	 * @covers  JComponentHelper::getComponent
	 */
	public function testGetComponent_falseComponent_strict()
	{
		$component = JComponentHelper::getComponent('com_false', true);
		$this->assertFalse($component->enabled, 'The anonymous component has to be disabled by default if strict');
	}

	/**
	 * Test JComponentHelper::isEnabled
	 *
	 * @return  void
	 *
	 * @since   3.2
	 * @covers  JComponentHelper::isEnabled
	 */
	public function testIsEnabled()
	{
		$this->assertTrue(
			(bool) JComponentHelper::isEnabled('com_content'),
			'com_content should be enabled'
		);
	}

	/**
	 * Test JComponentHelper::isInstalled
	 *
	 * @return  void
	 *
	 * @since   3.4
	 * @covers  JComponentHelper::isInstalled
	 */
	public function testIsInstalled()
	{
		$this->assertTrue(
			(bool) JComponentHelper::isInstalled('com_content'),
			'com_content should be installed'
		);

		$this->assertFalse(
			(bool) JComponentHelper::isInstalled('com_willneverhappen'),
			'com_willneverhappen should not be enabled'
		);
	}

}

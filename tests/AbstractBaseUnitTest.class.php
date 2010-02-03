<?php
abstract class customer_tests_AbstractBaseUnitTest extends customer_tests_AbstractBaseTest
{
	/**
	 * @return void
	 */
	public function prepareTestCase()
	{
		$this->resetDatabase();
	}
}
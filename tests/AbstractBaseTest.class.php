<?php
abstract class customer_tests_AbstractBaseTest extends f_tests_AbstractBaseTest
{
	/**
	 * @return String
	 */
	protected final function getPackageName()
	{
		return 'modules_customer';
	}

	/**
	 * @return void
	 */
	protected function clearServicesCache()
	{
		parent::clearServicesCache();
		RequestContext::clearInstance();
		RequestContext::getInstance()->setLang('fr');
		self::clearModuleServiceCache();
	}

	/**
	 * @return void
	 */
	public static function clearModuleServiceCache()
	{
	}	
}
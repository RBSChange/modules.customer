<?php
/**
 * customer_patch_0311
 * @package modules.customer
 */
class customer_patch_0311 extends patch_BasePatch
{ 
	/**
	 * Entry point of the patch execution.
	 */
	public function execute()
	{
		$this->executeModuleScript('list.xml', 'customer');
		$this->execChangeCommand('update-autoload', array('modules/customer/persistentdocument/filters'));
		$this->execChangeCommand('compile-document-filters');
		$this->execChangeCommand('compile-locales', array('customer'));
	}

	/**
	 * @return String
	 */
	protected final function getModuleName()
	{
		return 'customer';
	}

	/**
	 * @return String
	 */
	protected final function getNumber()
	{
		return '0311';
	}
}
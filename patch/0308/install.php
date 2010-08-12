<?php
/**
 * customer_patch_0308
 * @package modules.customer
 */
class customer_patch_0308 extends patch_BasePatch
{
	/**
	 * Entry point of the patch execution.
	 */
	public function execute()
	{
		$newPath = f_util_FileUtils::buildWebeditPath('modules/customer/persistentdocument/customer.xml');
		$newModel = generator_PersistentModel::loadModelFromString(f_util_FileUtils::read($newPath), 'customer', 'customer');
		$newProp = $newModel->getPropertyByName('lastAbandonedOrderDate');
		f_persistentdocument_PersistentProvider::getInstance()->addProperty('customer', 'customer', $newProp);
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
		return '0308';
	}
}
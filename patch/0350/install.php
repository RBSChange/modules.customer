<?php
/**
 * customer_patch_0350
 * @package modules.customer
 */
class customer_patch_0350 extends patch_BasePatch
{
	/**
	 * Entry point of the patch execution.
	 */
	public function execute()
	{
		$newPath = f_util_FileUtils::buildWebeditPath('modules/customer/persistentdocument/address.xml');
		$newModel = generator_PersistentModel::loadModelFromString(f_util_FileUtils::read($newPath), 'customer', 'address');
		$newProp = $newModel->getPropertyByName('mobilephone');
		f_persistentdocument_PersistentProvider::getInstance()->addProperty('customer', 'address', $newProp);
		
		$newPath = f_util_FileUtils::buildWebeditPath('modules/customer/persistentdocument/customer.xml');
		$newModel = generator_PersistentModel::loadModelFromString(f_util_FileUtils::read($newPath), 'customer', 'customer');
		$newProp = $newModel->getPropertyByName('birthday');
		f_persistentdocument_PersistentProvider::getInstance()->addProperty('customer', 'customer', $newProp);
		
		$newPath = f_util_FileUtils::buildWebeditPath('modules/customer/persistentdocument/customer.xml');
		$newModel = generator_PersistentModel::loadModelFromString(f_util_FileUtils::read($newPath), 'customer', 'customer');
		$newProp = $newModel->getPropertyByName('birthdayDayNumber');
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
		return '0350';
	}
}
<?php
/**
 * customer_patch_0306
 * @package modules.customer
 */
class customer_patch_0306 extends patch_BasePatch
{
	//  by default, isCodePatch() returns false.
	//  decomment the following if your patch modify code instead of the database structure or content.
	/**
	 * Returns true if the patch modify code that is versionned.
	 * If your patch modify code that is versionned AND database structure or content,
	 * you must split it into two different patches.
	 * @return Boolean true if the patch modify code that is versionned.
	 */
	//	public function isCodePatch()
	//	{
	//		return true;
	//	}
	

	/**
	 * Entry point of the patch execution.
	 */
	public function execute()
	{
		parent::execute();
		$newPath = f_util_FileUtils::buildWebeditPath('modules/customer/persistentdocument/preferences.xml');
		$newModel = generator_PersistentModel::loadModelFromString(f_util_FileUtils::read($newPath), 'customer', 'preferences');
		$newProp = $newModel->getPropertyByName('securitylevel');
		f_persistentdocument_PersistentProvider::getInstance()->addProperty('customer', 'preferences', $newProp);
		// Implement your patch here.
	}
	
	/**
	 * Returns the name of the module the patch belongs to.
	 *
	 * @return String
	 */
	protected final function getModuleName()
	{
		return 'customer';
	}
	
	/**
	 * Returns the number of the current patch.
	 * @return String
	 */
	protected final function getNumber()
	{
		return '0306';
	}
}
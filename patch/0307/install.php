<?php
/**
 * customer_patch_0307
 * @package modules.customer
 */
class customer_patch_0307 extends patch_BasePatch
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
		// Implement your patch here.
		$newPath = f_util_FileUtils::buildWebeditPath('modules/customer/persistentdocument/customer.xml');
		$newModel = generator_PersistentModel::loadModelFromString(f_util_FileUtils::read($newPath), 'customer', 'customer');
		$newProp = $newModel->getPropertyByName('lastCartUpdate');
		f_persistentdocument_PersistentProvider::getInstance()->addProperty('customer', 'customer', $newProp);
		
		$customerIds = customer_CustomerService::getInstance()->createQuery()->setProjection(Projections::property('id', 'id'))->findColumn('id');
		foreach (array_chunk($customerIds, 100) as $chunk)
		{
			$pHandle = popen("php " . WEBEDIT_HOME . '/modules/customer/patch/0307/patch.php ' . implode(" ", $chunk) , "r");
			while ($read = fread($pHandle, 1024))
			{
				echo $read;
			}
			pclose($pHandle);
		}
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
		return '0307';
	}
}
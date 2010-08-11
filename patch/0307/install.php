<?php
/**
 * customer_patch_0307
 * @package modules.customer
 */
class customer_patch_0307 extends patch_BasePatch
{
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
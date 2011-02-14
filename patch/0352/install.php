<?php
/**
 * customer_patch_0352
 * @package modules.customer
 */
class customer_patch_0352 extends patch_BasePatch
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
		$newPath = f_util_FileUtils::buildWebeditPath('modules/customer/persistentdocument/customergroup.xml');
		$newModel = generator_PersistentModel::loadModelFromString(f_util_FileUtils::read($newPath), 'customer', 'customergroup');
		$newProp = $newModel->getPropertyByName('dynamicgroup');
		f_persistentdocument_PersistentProvider::getInstance()->addProperty('customer', 'customergroup', $newProp);
		$this->execChangeCommand('compile-db-schema');
		
		foreach (users_DynamicfrontendgroupService::getInstance()->createQuery()->find() as $group) 
		{
			if ($group instanceof users_persistentdocument_dynamicfrontendgroup)
			{
				$customerGroupId = $group->getParameter('referenceId');
				$customerGroup = customer_CustomergroupService::getInstance()->createQuery()->add(Restrictions::eq('id', $customerGroupId))->findUnique();
				if ($customerGroup !== null)
				{
					$customerGroup->setDynamicgroup($group);
					$customerGroup->save();
				}
			}
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
		return '0352';
	}
}
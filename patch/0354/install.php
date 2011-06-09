<?php
/**
 * customer_patch_0354
 * @package modules.customer
 */
class customer_patch_0354 extends patch_BasePatch
{
 
	/**
	 * Entry point of the patch execution.
	 */
	public function execute()
	{
		$this->log('Add codeReference property...');
		$newPath = f_util_FileUtils::buildWebeditPath('modules/customer/persistentdocument/customer.xml');
		$newModel = generator_PersistentModel::loadModelFromString(f_util_FileUtils::read($newPath), 'customer', 'customer');
		$newProp = $newModel->getPropertyByName('codeReference');
		f_persistentdocument_PersistentProvider::getInstance()->addProperty('customer', 'customer', $newProp);
		
		$this->log('Copy synchroId in codeReference...');
		$sql = "UPDATE `m_customer_doc_customer` SET `codereference` = `synchroid` WHERE `codereference` IS NULL AND `synchroid` IS NOT NULL";
		$this->executeSQLQuery($sql);

		$this->log('compile-locales customer...');
		$this->execChangeCommand('compile-locales', array('customer'));
		
		$this->log('update-autoload customer...');
		$this->execChangeCommand('update-autoload', array('modules/customer'));
		
		$this->log('compile filters...');
		$this->execChangeCommand('compile-document-filters');
	}
}
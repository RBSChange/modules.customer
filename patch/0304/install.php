<?php
/**
 * customer_patch_0304
 * @package modules.customer
 */
class customer_patch_0304 extends patch_BasePatch
{
	/**
	 * Entry point of the patch execution.
	 */
	public function execute()
	{
		parent::execute();
		
		// Update customer model.
		$this->executeSQLQuery("ALTER TABLE m_customer_doc_customer ADD `tarifgroup` int(11) default NULL;");
		
		// Add the tarif customer group folder.
		$rootId = ModuleService::getInstance()->getRootFolderId('customer');
		customer_TarifcustomergroupfolderService::getInstance()->getNewDocumentInstance()->save($rootId);
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
		return '0304';
	}
}
<?php
/**
 * customer_TarifcustomergroupfolderScriptDocumentElement
 * @package modules.customer.persistentdocument.import
 */
class customer_TarifcustomergroupfolderScriptDocumentElement extends import_ScriptDocumentElement
{
	/**
	 * @return customer_persistentdocument_tarifcustomergroupfolder
	 */
	protected function initPersistentDocument()
	{
		return customer_TarifcustomergroupfolderService::getInstance()->getNewDocumentInstance();
	}
	
	/**
	 * @return f_persistentdocument_PersistentDocumentModel
	 */
	protected function getDocumentModel()
	{
		return f_persistentdocument_PersistentDocumentModel::getInstanceFromDocumentModelName('modules_customer/tarifcustomergroupfolder');
	}
}
<?php
/**
 * customer_EditablecustomergroupScriptDocumentElement
 * @package modules.customer.persistentdocument.import
 */
class customer_EditablecustomergroupScriptDocumentElement extends import_ScriptDocumentElement
{
	/**
	 * @return customer_persistentdocument_editablecustomergroup
	 */
	protected function initPersistentDocument()
	{
		return customer_EditablecustomergroupService::getInstance()->getNewDocumentInstance();
	}
	
	/**
	 * @return f_persistentdocument_PersistentDocumentModel
	 */
	protected function getDocumentModel()
	{
		return f_persistentdocument_PersistentDocumentModel::getInstanceFromDocumentModelName('modules_customer/editablecustomergroup');
	}
}
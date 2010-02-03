<?php
/**
 * customer_CustomergroupScriptDocumentElement
 * @package modules.customer.persistentdocument.import
 */
class customer_CustomergroupScriptDocumentElement extends import_ScriptDocumentElement
{
    /**
     * @return customer_persistentdocument_customergroup
     */
    protected function initPersistentDocument()
    {
    	return customer_CustomergroupService::getInstance()->getNewDocumentInstance();
    }
    
    /**
	 * @return f_persistentdocument_PersistentDocumentModel
	 */
	protected function getDocumentModel()
	{
		return f_persistentdocument_PersistentDocumentModel::getInstanceFromDocumentModelName('modules_customer/customergroup');
	}
}
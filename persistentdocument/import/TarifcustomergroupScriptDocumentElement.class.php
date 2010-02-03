<?php
/**
 * customer_TarifcustomergroupScriptDocumentElement
 * @package modules.customer.persistentdocument.import
 */
class customer_TarifcustomergroupScriptDocumentElement extends import_ScriptDocumentElement
{
    /**
     * @return customer_persistentdocument_tarifcustomergroup
     */
    protected function initPersistentDocument()
    {
    	return customer_TarifcustomergroupService::getInstance()->getNewDocumentInstance();
    }
    
    /**
	 * @return f_persistentdocument_PersistentDocumentModel
	 */
	protected function getDocumentModel()
	{
		return f_persistentdocument_PersistentDocumentModel::getInstanceFromDocumentModelName('modules_customer/tarifcustomergroup');
	}
}
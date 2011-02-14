<?php
/**
 * customer_VoucherhandlerScriptDocumentElement
 * @package modules.customer.persistentdocument.import
 */
class customer_VoucherhandlerScriptDocumentElement extends import_ScriptDocumentElement
{
    /**
     * @return customer_persistentdocument_voucherhandler
     */
    protected function initPersistentDocument()
    {
    	return customer_VoucherhandlerService::getInstance()->getNewDocumentInstance();
    }
    
    /**
	 * @return f_persistentdocument_PersistentDocumentModel
	 */
	protected function getDocumentModel()
	{
		return f_persistentdocument_PersistentDocumentModel::getInstanceFromDocumentModelName('modules_customer/voucherhandler');
	}
}
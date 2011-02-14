<?php
/**
 * customer_VoucherfolderScriptDocumentElement
 * @package modules.customer.persistentdocument.import
 */
class customer_VoucherfolderScriptDocumentElement extends import_ScriptDocumentElement
{
    /**
     * @return customer_persistentdocument_voucherfolder
     */
    protected function initPersistentDocument()
    {
    	return customer_VoucherfolderService::getInstance()->getNewDocumentInstance();
    }
    
    /**
	 * @return f_persistentdocument_PersistentDocumentModel
	 */
	protected function getDocumentModel()
	{
		return f_persistentdocument_PersistentDocumentModel::getInstanceFromDocumentModelName('modules_customer/voucherfolder');
	}
}
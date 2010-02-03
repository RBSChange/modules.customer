<?php
class customer_AddressScriptDocumentElement extends import_ScriptDocumentElement
{
    /**
     * @return customer_persistentdocument_address
     */
    protected function initPersistentDocument()
    {
    	return customer_AddressService::getInstance()->getNewDocumentInstance();
    }
}
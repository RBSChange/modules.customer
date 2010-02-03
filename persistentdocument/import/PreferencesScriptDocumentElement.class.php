<?php
class customer_PreferencesScriptDocumentElement extends import_ScriptDocumentElement
{
    /**
     * @return customer_persistentdocument_preferences
     */
    protected function initPersistentDocument()
    {
    	$document = ModuleService::getInstance()->getPreferencesDocument('customer');
    	return ($document !== null) ? $document : customer_PreferencesService::getInstance()->getNewDocumentInstance();
    }
}
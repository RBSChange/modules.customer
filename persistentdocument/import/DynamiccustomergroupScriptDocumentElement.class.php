<?php
/**
 * customer_DynamiccustomergroupScriptDocumentElement
 * @package modules.customer.persistentdocument.import
 */
class customer_DynamiccustomergroupScriptDocumentElement extends import_ScriptDocumentElement
{
    /**
     * @return customer_persistentdocument_dynamiccustomergroup
     */
    protected function initPersistentDocument()
    {
    	return customer_DynamiccustomergroupService::getInstance()->getNewDocumentInstance();
    }
    
    /**
	 * @return f_persistentdocument_PersistentDocumentModel
	 */
	protected function getDocumentModel()
	{
		return f_persistentdocument_PersistentDocumentModel::getInstanceFromDocumentModelName('modules_customer/dynamiccustomergroup');
	}
	
	/**
	 * @return array
	 */
	protected function getDocumentProperties()
	{
		$properties = parent::getDocumentProperties();
		if (isset($properties['query']))
		{
			$query = $this->replaceRefIdInString($properties['query']) ;
			$properties['query'] = $query;
		}
		return $properties;
	}
}
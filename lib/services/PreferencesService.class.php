<?php
/**
 * @date Mon, 28 Jan 2008 16:50:04 +0100
 * @author intportg
 */
class customer_PreferencesService extends f_persistentdocument_DocumentService
{
	/**
	 * @var customer_PreferencesService
	 */
	private static $instance;

	/**
	 * @return customer_PreferencesService
	 */
	public static function getInstance()
	{
		if (self::$instance === null)
		{
			self::$instance = self::getServiceClassInstance(get_class());
		}
		return self::$instance;
	}

	/**
	 * @return customer_persistentdocument_preferences
	 */
	public function getNewDocumentInstance()
	{
		return $this->getNewDocumentInstanceByModelName('modules_customer/preferences');
	}

	/**
	 * Create a query based on 'modules_customer/preferences' model
	 * @return f_persistentdocument_criteria_Query
	 */
	public function createQuery()
	{
		return $this->pp->createQuery('modules_customer/preferences');
	}
	
	/**
	 * @param customer_persistentdocument_preferences $document
	 * @param Integer $parentNodeId Parent node ID where to save the document (optionnal => can be null !).
	 * @return void
	 */
	protected function preSave($document, $parentNodeId = null)
	{
		$document->setLabel('&modules.customer.bo.general.Module-name;');
	}
}
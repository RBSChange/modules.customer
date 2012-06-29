<?php
/**
 * @package mdoules.customer
 * @method customer_PreferencesService getInstance()
 */
class customer_PreferencesService extends f_persistentdocument_DocumentService
{
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
		return $this->getPersistentProvider()->createQuery('modules_customer/preferences');
	}
}
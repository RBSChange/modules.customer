<?php
/**
 * customer_persistentdocument_preferences
 * @package customer
 */
class customer_persistentdocument_preferences extends customer_persistentdocument_preferencesbase 
{

	/**
	 * @see f_persistentdocument_PersistentDocumentImpl::getLabel()
	 *
	 * @return String
	 */
	public function getLabel()
	{
		return f_Locale::translateUI(parent::getLabel());
	}
}
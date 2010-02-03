<?php
/**
 * customer_persistentdocument_address
 * @package modules.customer
 */
class customer_persistentdocument_address extends customer_persistentdocument_addressbase
{
	/**
	 * @return customer_persistentdocument_customer
	 */
	public function getCustomer()
	{
		return f_util_ArrayUtils::firstElement($this->getCustomerArrayInverse(0, 1));
	}
	
	/**
	 * @param customer_persistentdocument_address $address
	 * @param Boolean $includeTitle
	 * @return String
	 */
	public function getFullName($includeTitle = true)
	{
		return $this->getDocumentService()->getFullName($this, $includeTitle);
	}
}
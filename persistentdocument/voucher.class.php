<?php
/**
 * Class where to put your custom methods for document customer_persistentdocument_voucher
 * @package modules.customer.persistentdocument
 */
class customer_persistentdocument_voucher extends customer_persistentdocument_voucherbase 
{
	/**
	 * @return customer_persistentdocument_customer
	 */
	public function getCustomer()
	{
		return DocumentHelper::getDocumentInstanceIfExists($this->getCustomerId());
	}
	
	
	/**
	 * @return catalog_persistentdocument_billingarea
	 */
	public function getBillingArea()
	{
		return DocumentHelper::getDocumentInstanceIfExists($this->getBillingAreaId());
	}
}


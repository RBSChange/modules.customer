<?php
/**
 * customer_CouponScriptDocumentElement
 * @package modules.customer.persistentdocument.import
 */
class customer_CouponScriptDocumentElement extends import_ScriptDocumentElement
{
	/**
	 * @return customer_persistentdocument_coupon
	 */
	protected function initPersistentDocument()
	{
		return customer_CouponService::getInstance()->getNewDocumentInstance();
	}
	
	/**
	 * @return f_persistentdocument_PersistentDocumentModel
	 */
	protected function getDocumentModel()
	{
		return f_persistentdocument_PersistentDocumentModel::getInstanceFromDocumentModelName('modules_customer/coupon');
	}
}
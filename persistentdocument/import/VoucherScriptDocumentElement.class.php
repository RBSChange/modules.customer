<?php
/**
 * customer_VoucherScriptDocumentElement
 * @package modules.customer.persistentdocument.import
 */
class customer_VoucherScriptDocumentElement extends import_ScriptDocumentElement
{
	/**
	 * @return customer_persistentdocument_voucher
	 */
	protected function initPersistentDocument()
	{
		return customer_VoucherService::getInstance()->getNewDocumentInstance();
	}
	
	/**
	 * @return f_persistentdocument_PersistentDocumentModel
	 */
	protected function getDocumentModel()
	{
		return f_persistentdocument_PersistentDocumentModel::getInstanceFromDocumentModelName('modules_customer/voucher');
	}
}
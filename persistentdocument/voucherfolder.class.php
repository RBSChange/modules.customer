<?php
/**
 * Class where to put your custom methods for document customer_persistentdocument_voucherfolder
 * @package modules.customer.persistentdocument
 */
class customer_persistentdocument_voucherfolder extends customer_persistentdocument_voucherfolderbase 
{
	/**
	 * Return the localized value for a rootfolder
	 * @return string
	 */
	public function getLabel()
	{
		return LocaleService::getInstance()->transBO('m.customer.document.voucherfolder.document-name', array('ucf'));
	}
}
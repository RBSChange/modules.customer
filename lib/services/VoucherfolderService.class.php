<?php
/**
 * @package mdoules.customer
 * @method customer_VoucherfolderService getInstance()
 */
class customer_VoucherfolderService extends generic_FolderService
{
	/**
	 * @return customer_persistentdocument_voucherfolder
	 */
	public function getNewDocumentInstance()
	{
		return $this->getNewDocumentInstanceByModelName('modules_customer/voucherfolder');
	}

	/**
	 * Create a query based on 'modules_customer/voucherfolder' model.
	 * Return document that are instance of modules_customer/voucherfolder,
	 * including potential children.
	 * @return f_persistentdocument_criteria_Query
	 */
	public function createQuery()
	{
		return $this->getPersistentProvider()->createQuery('modules_customer/voucherfolder');
	}
	
	/**
	 * Create a query based on 'modules_customer/voucherfolder' model.
	 * Only documents that are strictly instance of modules_customer/voucherfolder
	 * (not children) will be retrieved
	 * @return f_persistentdocument_criteria_Query
	 */
	public function createStrictQuery()
	{
		return $this->getPersistentProvider()->createQuery('modules_customer/voucherfolder', false);
	}
}
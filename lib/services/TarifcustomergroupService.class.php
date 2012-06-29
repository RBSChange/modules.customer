<?php
/**
 * @package mdoules.customer
 * @method customer_TarifcustomergroupService getInstance()
 */
class customer_TarifcustomergroupService extends customer_CustomergroupService
{
	/**
	 * @return customer_persistentdocument_tarifcustomergroup
	 */
	public function getNewDocumentInstance()
	{
		return $this->getNewDocumentInstanceByModelName('modules_customer/tarifcustomergroup');
	}

	/**
	 * Create a query based on 'modules_customer/tarifcustomergroup' model.
	 * Return document that are instance of modules_customer/tarifcustomergroup,
	 * including potential children.
	 * @return f_persistentdocument_criteria_Query
	 */
	public function createQuery()
	{
		return $this->getPersistentProvider()->createQuery('modules_customer/tarifcustomergroup');
	}
	
	/**
	 * Create a query based on 'modules_customer/tarifcustomergroup' model.
	 * Only documents that are strictly instance of modules_customer/tarifcustomergroup
	 * (not children) will be retrieved
	 * @return f_persistentdocument_criteria_Query
	 */
	public function createStrictQuery()
	{
		return $this->getPersistentProvider()->createQuery('modules_customer/tarifcustomergroup', false);
	}
	
	/**
	 * @param customer_persistentdocument_tarifcustomergroup $group
	 * @return customer_persistentdocument_customer[]
	 */
	protected function doGetMembers($group)
	{
		return $group->getCustomerArrayInverse();
	}
		
	/**
	 * @param customer_persistentdocument_tarifcustomergroup $group
	 * @return integer[]
	 */
	protected function doGetMemberIds($group)
	{
		return customer_CustomerService::getInstance()->createQuery()
			->add(Restrictions::eq('tarifGroup.id', $group->getId()))
			->addOrder(Order::asc('document_label'))
			->setProjection(Projections::property('id'))
			->findColumn('id');
	}
	
	/**
	 * @param customer_persistentdocument_tarifcustomergroup $group
	 * @param customer_persistentdocument_customer $customer
	 * @return boolean
	 */
	protected function doIsMember($group, $customer)
	{
		return in_array($customer->getId(), $this->doGetMemberIds($group));
	}
	
	/**
	 * @param customer_persistentdocument_tarifcustomergroup $document
	 * @return void
	 */
	protected function preDelete($document)
	{
		catalog_PriceService::getInstance()->deleteForProductId($document->getId());
	}
}
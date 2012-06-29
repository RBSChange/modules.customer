<?php
/**
 * @package mdoules.customer
 * @method customer_EditablecustomergroupService getInstance()
 */
class customer_EditablecustomergroupService extends customer_CustomergroupService
{
	/**
	 * @return customer_persistentdocument_editablecustomergroup
	 */
	public function getNewDocumentInstance()
	{
		return $this->getNewDocumentInstanceByModelName('modules_customer/editablecustomergroup');
	}

	/**
	 * Create a query based on 'modules_customer/editablecustomergroup' model.
	 * Return document that are instance of modules_customer/editablecustomergroup,
	 * including potential children.
	 * @return f_persistentdocument_criteria_Query
	 */
	public function createQuery()
	{
		return $this->getPersistentProvider()->createQuery('modules_customer/editablecustomergroup');
	}
	
	/**
	 * Create a query based on 'modules_customer/editablecustomergroup' model.
	 * Only documents that are strictly instance of modules_customer/editablecustomergroup
	 * (not children) will be retrieved
	 * @return f_persistentdocument_criteria_Query
	 */
	public function createStrictQuery()
	{
		return $this->getPersistentProvider()->createQuery('modules_customer/editablecustomergroup', false);
	}
	
	/**
	 * @param customer_persistentdocument_editablecustomergroup $group
	 * @return customer_persistentdocument_customer[]
	 */
	protected function doGetMembers($group)
	{
		return $group->getMembersArray();
	}
		
	/**
	 * @param customer_persistentdocument_editablecustomergroup $group
	 * @return integer[]
	 */
	protected function doGetMemberIds($group)
	{
		$members = $group->getMembersArray();
		$ids = array();
		foreach ($members as $member)
		{
			$ids[] = $member->getId();
		}
		return $ids;
	}
	
	/**
	 * @param customer_persistentdocument_editablecustomergroup $group
	 * @param customer_persistentdocument_customer $customer
	 * @return boolean
	 */
	protected function doIsMember($group, $customer)
	{
		return ($group->getIndexofMembers($customer) !== -1);
	}
}
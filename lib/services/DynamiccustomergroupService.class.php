<?php
/**
 * @package mdoules.customer
 * @method customer_DynamiccustomergroupService getInstance()
 */
class customer_DynamiccustomergroupService extends customer_CustomergroupService
{
	/**
	 * @return customer_persistentdocument_dynamiccustomergroup
	 */
	public function getNewDocumentInstance()
	{
		return $this->getNewDocumentInstanceByModelName('modules_customer/dynamiccustomergroup');
	}

	/**
	 * Create a query based on 'modules_customer/dynamiccustomergroup' model.
	 * Return document that are instance of modules_customer/dynamiccustomergroup,
	 * including potential children.
	 * @return f_persistentdocument_criteria_Query
	 */
	public function createQuery()
	{
		return $this->getPersistentProvider()->createQuery('modules_customer/dynamiccustomergroup');
	}
	
	/**
	 * Create a query based on 'modules_customer/dynamiccustomergroup' model.
	 * Only documents that are strictly instance of modules_customer/dynamiccustomergroup
	 * (not children) will be retrieved
	 * @return f_persistentdocument_criteria_Query
	 */
	public function createStrictQuery()
	{
		return $this->getPersistentProvider()->createQuery('modules_customer/dynamiccustomergroup', false);
	}
	
	/**
	 * @param customer_persistentdocument_dynamiccustomergroup $group
	 * @return customer_persistentdocument_customer[]
	 */
	protected function doGetMembers($group)
	{
		$queryIntersection = f_persistentdocument_DocumentFilterService::getInstance()->getQueryIntersectionFromJson($group->getQuery());
		return $queryIntersection->find();
	}
		
	/**
	 * @param customer_persistentdocument_dynamiccustomergroup $group
	 * @return integer[]
	 */
	protected function doGetMemberIds($group)
	{
		$queryIntersection = f_persistentdocument_DocumentFilterService::getInstance()->getQueryIntersectionFromJson($group->getQuery());
		return $queryIntersection->findIds();
	}
	
	/**
	 * @param customer_persistentdocument_dynamiccustomergroup $group
	 * @param customer_persistentdocument_customer $customer
	 * @return boolean
	 */
	protected function doIsMember($group, $customer)
	{
		return f_persistentdocument_DocumentFilterService::getInstance()->checkValueFromJson($group->getQuery(), $customer);
	}
	
	/**
	 * @param customer_persistentdocument_dynamiccustomergroup $document
	 * @param string[] $subModelNames
	 * @param integer $locateDocumentId null if use startindex
	 * @param integer $pageSize
	 * @param integer $startIndex
	 * @param integer $totalCount
	 * @return f_persistentdocument_PersistentDocument[]
	 */
	public function getVirtualChildrenAt($document, $subModelNames, $locateDocumentId, $pageSize, &$startIndex, &$totalCount)
	{
		$queryIntersection = f_persistentdocument_DocumentFilterService::getInstance()->getQueryIntersectionFromJson($document->getQuery());
		$result = $queryIntersection->findAtOffset($startIndex, $pageSize, $totalCount);
		return $result;
	}
}
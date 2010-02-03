<?php
/**
 * customer_DynamiccustomergroupService
 * @package customer
 */
class customer_DynamiccustomergroupService extends customer_CustomergroupService
{
	/**
	 * @var customer_DynamiccustomergroupService
	 */
	private static $instance;

	/**
	 * @return customer_DynamiccustomergroupService
	 */
	public static function getInstance()
	{
		if (self::$instance === null)
		{
			self::$instance = self::getServiceClassInstance(get_class());
		}
		return self::$instance;
	}

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
		return $this->pp->createQuery('modules_customer/dynamiccustomergroup');
	}
	
	/**
	 * Create a query based on 'modules_customer/dynamiccustomergroup' model.
	 * Only documents that are strictly instance of modules_customer/dynamiccustomergroup
	 * (not children) will be retrieved
	 * @return f_persistentdocument_criteria_Query
	 */
	public function createStrictQuery()
	{
		return $this->pp->createQuery('modules_customer/dynamiccustomergroup', false);
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
	 * @return Integer[]
	 */
	protected function doGetMemberIds($group)
	{
		$queryIntersection = f_persistentdocument_DocumentFilterService::getInstance()->getQueryIntersectionFromJson($group->getQuery());
		return $queryIntersection->findIds();
	}
	
	/**
	 * @param customer_persistentdocument_dynamiccustomergroup $group
	 * @param customer_persistentdocument_customer $customer
	 * @return Boolean
	 */
	protected function doIsMember($group, $customer)
	{
		return f_persistentdocument_DocumentFilterService::getInstance()->checkValueFromJson($group->getQuery(), $customer);
	}
}
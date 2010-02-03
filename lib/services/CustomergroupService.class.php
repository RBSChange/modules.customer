<?php
/**
 * customer_CustomergroupService
 * @package customer
 */
class customer_CustomergroupService extends f_persistentdocument_DocumentService
{
	/**
	 * @var customer_CustomergroupService
	 */
	private static $instance;

	/**
	 * @return customer_CustomergroupService
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
	 * @return customer_persistentdocument_customergroup
	 */
	public function getNewDocumentInstance()
	{
		return $this->getNewDocumentInstanceByModelName('modules_customer/customergroup');
	}

	/**
	 * Create a query based on 'modules_customer/customergroup' model.
	 * Return document that are instance of modules_customer/customergroup,
	 * including potential children.
	 * @return f_persistentdocument_criteria_Query
	 */
	public function createQuery()
	{
		return $this->pp->createQuery('modules_customer/customergroup');
	}
	
	/**
	 * Create a query based on 'modules_customer/customergroup' model.
	 * Only documents that are strictly instance of modules_customer/customergroup
	 * (not children) will be retrieved
	 * @return f_persistentdocument_criteria_Query
	 */
	public function createStrictQuery()
	{
		return $this->pp->createQuery('modules_customer/customergroup', false);
	}
	
	/**
	 * @param customer_persistentdocument_customergroup $group
	 * @return customer_persistentdocument_customer[]
	 */
	public function getMembers($group)
	{
		return $group->getDocumentService()->doGetMembers($group);
	}
	
	/**
	 * @param customer_persistentdocument_customergroup $group
	 * @return Integer[]
	 */
	public function getMemberIds($group)
	{
		return $group->getDocumentService()->doGetMemberIds($group);
	}
	
	/**
	 * @param customer_persistentdocument_customergroup $group
	 * @param customer_persistentdocument_customer $customer
	 * @return Booelan
	 */
	public function isMember($group, $customer)
	{
		return $group->getDocumentService()->doIsMember($group, $customer);
	}
	
	/**
	 * @param customer_persistentdocument_customergroup $group
	 * @return customer_persistentdocument_customer[]
	 */
	protected function doGetMembers($group)
	{
		throw new Exception('This method must be redefined in descendant classes!');
	}
		
	/**
	 * @param customer_persistentdocument_customergroup $group
	 * @return Integer[]
	 */
	protected function doGetMemberIds($group)
	{
		throw new Exception('This method must be redefined in descendant classes!');
	}
		
	/**
	 * @param customer_persistentdocument_customergroup $group
	 * @param customer_persistentdocument_customer $customer
	 * @return Boolean
	 */
	protected function doIsMember($group, $customer)
	{
		throw new Exception('This method must be redefined in descendant classes!');
	}
}
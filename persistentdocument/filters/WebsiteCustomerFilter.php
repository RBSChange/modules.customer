<?php
class customer_WebsiteCustomerFilter extends f_persistentdocument_DocumentFilterImpl
{
	public function __construct()
	{
		$parameters = array();
		$info = new BeanPropertyInfoImpl('website', 'modules_website/website');
		$shopParameter = new f_persistentdocument_DocumentFilterValueParameter($info);
		$parameters['shop'] = $shopParameter;
		$this->setParameters($parameters);
	}
	
	/**
	 * @return string
	 */
	public static function getDocumentModelName()
	{
		return 'modules_customer/customer';
	}

	/**
	 * @return f_persistentdocument_criteria_Query
	 */
	public function getQuery()
	{
		$groupIds = $this->getGroupIds();
		if (count($groupIds) === 0) {$groupIds[] = 0;}
		return customer_CustomerService::getInstance()->createQuery()->add(Restrictions::in('user.groups.id', $groupIds));
	}
	
	/**
	 * @param customer_persistentdocument_customer $value
	 */
	public function checkValue($value)
	{
		if ($value instanceof customer_persistentdocument_customer)
		{
			$groupIds = $this->getGroupIds();
			if (count($groupIds) === 0) {return false;}
			$usersGroupIds = DocumentHelper::getIdArrayFromDocumentArray($value->getUser()->getGroupsArray());
			return count(array_intersect($groupIds, $usersGroupIds)) > 0;
		}
		return false;
	}
	
	private $groupIds;
	
	private function getGroupIds()
	{
		if ($this->groupIds === null)
		{
			$websiteIds =  DocumentHelper::getIdArrayFromDocumentArray($this->getParameter('shop')->getValueForQuery());
			$this->groupIds = users_GroupService::getInstance()->createQuery()
				->add(Restrictions::in('website', $websiteIds))
				->setProjection(Projections::groupProperty('id', 'id'))
				->findColumn('id');
		}
		return $this->groupIds ;
	}
}
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
	 * @return String
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
		$websiteIds =  DocumentHelper::getIdArrayFromDocumentArray($this->getParameter('website')->getValueForQuery());
		return customer_CustomerService::getInstance()->createQuery()->add(Restrictions::in('user.websiteid', $websiteIds));
	}
	
	/**
	 * @param customer_persistentdocument_customer $value
	 */
	public function checkValue($value)
	{
		if ($value instanceof customer_persistentdocument_customer)
		{
			$websiteIds =  DocumentHelper::getIdArrayFromDocumentArray($this->getParameter('website')->getValueForQuery());
			return in_array($value->getUser()->getWebsiteid(), $websiteIds);
		}
		return false;
	}
}
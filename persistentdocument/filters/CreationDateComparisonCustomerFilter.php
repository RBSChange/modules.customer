<?php
class customer_CreationDateComparisonCustomerFilter extends f_persistentdocument_DocumentFilterImpl
{
	public function __construct()
	{
		$info = f_persistentdocument_PersistentDocumentModel::getInstance('customer', 'customer')->getBeanPropertyInfo('creationdate');
		$parameter = f_persistentdocument_DocumentFilterRestrictionParameter::getNewInstance($info);
		$parameter->setAllowedRestrictions($info->getName(), array('ge', 'le'));
		$this->setParameter('date', $parameter);
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
		return customer_CustomerService::getInstance()->createQuery()->add($this->getParameter('date')->getValueForQuery());
	}
	
	/**
	 * @param customer_persistentdocument_customer $value
	 */
	public function checkValue($value)
	{
		if ($value instanceof customer_persistentdocument_customer)
		{
			$date = $this->getParameter('date')->getParameter()->getValueForQyuery();
			return $date < $value->getCreationdate();
		}
		return false;
	}
}
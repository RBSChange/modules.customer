<?php
class customer_CustomerFilter extends f_persistentdocument_DocumentFilterImpl
{
	public function __construct()
	{
		$parameter = f_persistentdocument_DocumentFilterRestrictionParameter::getNewInstance();
		$parameter->setAllowedPropertyNames(array(
			'modules_customer/customer.creationdate',
			'modules_customer/customer.birthday'
		));
		$this->setParameter('field', $parameter);
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
		$query = customer_CustomerService::getInstance()->createQuery();
		$query->add($this->getParameter('field')->getValueForQuery());
		return $query;
	}
	
	/**
	 * @param customer_persistentdocument_customer $value
	 */
	public function checkValue($value)
	{
		if ($value instanceof customer_persistentdocument_customer)
		{
			$query = $this->getQuery()->add(Restrictions::eq('id', $value->getId()));
			if ($query->findUnique() !== null)
			{
				return true;
			}
		}
		return false;
	}
}
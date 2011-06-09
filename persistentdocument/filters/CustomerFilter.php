<?php
class customer_CustomerFilter extends f_persistentdocument_DocumentFilterImpl
{
	
	protected function getExcludedProperty()
	{
		return array('id', 'model', 'author', 'authorid', 'modificationdate', 
				'publicationstatus', 'lang', 'metastring', 'modelversion', 'documentversion',
				'label', 'startpublicationdate', 'endpublicationdate',
				'user', 'address', 'notActivatedReason', 'usedCoupon', 'cartSerialized', 'synchroId',
				'lastOrderId', 'birthdayDayNumber');
	}
	public function __construct()
	{
		
		$parameter = f_persistentdocument_DocumentFilterRestrictionParameter::getNewInstance();
		$model = f_persistentdocument_PersistentDocumentModel::getInstance('customer', 'customer');
		$propertyNames = array();
		$systemProperties = $this->getExcludedProperty();
		
		foreach ($model->getPropertiesInfos() as $propertyInfo) 
		{
			if ($propertyInfo instanceof PropertyInfo)
			{
				if (in_array($propertyInfo->getName(), $systemProperties)) { continue; }
				if ($propertyInfo->getType() === f_persistentdocument_PersistentDocument::PROPERTYTYPE_LOB ||
					$propertyInfo->getType() === f_persistentdocument_PersistentDocument::PROPERTYTYPE_LONGSTRING ||
					$propertyInfo->getType() === f_persistentdocument_PersistentDocument::PROPERTYTYPE_XHTMLFRAGMENT)
				{
					continue;
				}
				$propertyNames[] = 'modules_customer/customer.' .$propertyInfo->getName();
			}
		}
		$parameter->setAllowedPropertyNames($propertyNames);
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
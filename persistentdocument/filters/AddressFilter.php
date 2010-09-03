<?php
class customer_AddressFilter extends f_persistentdocument_DocumentFilterImpl
{
	public function __construct()
	{
		$addressParameter = f_persistentdocument_DocumentFilterRestrictionParameter::getNewInstance();
		$addressParameter->setAllowedPropertyNames(array(
			'modules_customer/address.creationdate',
			'modules_customer/address.firstname',
			'modules_customer/address.lastname',
			'modules_customer/address.email',
			'modules_customer/address.company',
			'modules_customer/address.addressLine1',
			'modules_customer/address.addressLine2',
			'modules_customer/address.addressLine3',
			'modules_customer/address.zipCode',
			'modules_customer/address.city',
			'modules_customer/address.province',
			'modules_customer/address.phone',
			'modules_customer/address.fax',
		));
		$countryInfo = new BeanPropertyInfoImpl('country', 'modules_zone/country');
		$countryInfo->setListId('modules_zone/publishedcountries');
		$countryInfo->setLabelKey('&modules.customer.document.address.Country;');
		$addressParameter->addAllowedProperty('modules_customer/address.country', $countryInfo);
		$titleInfo = new BeanPropertyInfoImpl('title', 'modules_list/item');
		$titleInfo->setListId('modules_users/title');
		$titleInfo->setLabelKey('&modules.customer.document.address.Title;');
		$addressParameter->addAllowedProperty('modules_customer/address.title', $titleInfo);
		$this->setParameters(array('field' => $addressParameter));
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
		$field = $this->getParameter('field');
		$propName = $field->getPropertyName();
		Framework::fatal($propName);
		if ($propName == 'modules_customer/address.country')
		{
			$restriction = $field->getRestriction();
			$ids = DocumentHelper::getIdArrayFromDocumentArray($field->getParameter()->getValueForQuery());
			$query->createCriteria('address')->add(Restrictions::$restriction('countryid', $ids));
		}
		else if ($propName == 'modules_customer/address.title')
		{
			$restriction = $field->getRestriction();
			$query->createCriteria('address')->add(Restrictions::$restriction('titleid', array($field->getParameter()->getValueForQuery()->getId())));
		}
		else 
		{
			$query->createCriteria('address')->add($this->getParameter('field')->getValueForQuery());
		}
		return $query;
	}
	
	/**
	 * @param customer_persistentdocument_customer $value
	 */
	public function checkValue($value)
	{
		if ($value instanceof customer_persistentdocument_customer)
		{
			$param = $this->getParameter('field');
			$restriction = $param->getRestriction();
			$val = $param->getParameter()->getValue();
			foreach ($value->getAddressArray() as $address)
			{
				list(, $propertyName) = explode('.', $param->getPropertyName());
				$testVal = $this->getTestValueForPropertyName($address, $propertyName);
				if ($this->evalRestriction($testVal, $restriction, $val))
				{
					return true;
				}
			}
		}
		return false;
	}
}
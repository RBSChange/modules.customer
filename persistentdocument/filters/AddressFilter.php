<?php
class customer_AddressFilter extends f_persistentdocument_DocumentFilterImpl
{
	public function __construct()
	{
		$amountParameter = f_persistentdocument_DocumentFilterRestrictionParameter::getNewInstance();
		$amountParameter->setAllowedPropertyNames(array(
			'modules_customer/address.creationdate',
			'modules_customer/address.title',
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
			'modules_customer/address.country',
			'modules_customer/address.phone',
			'modules_customer/address.fax',
		));
		$this->setParameters(array('field' => $amountParameter));
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
		$query->createCriteria('address')->add($this->getParameter('field')->getValueForQuery());
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
?>
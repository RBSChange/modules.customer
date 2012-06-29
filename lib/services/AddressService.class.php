<?php
/**
 * @package mdoules.customer
 * @method customer_AddressService getInstance()
 */
class customer_AddressService extends f_persistentdocument_DocumentService
{
	/**
	 * @return customer_persistentdocument_address
	 */
	public function getNewDocumentInstance()
	{
		return $this->getNewDocumentInstanceByModelName('modules_customer/address');
	}

	/**
	 * Create a query based on 'modules_customer/address' model
	 * @return f_persistentdocument_criteria_Query
	 */
	public function createQuery()
	{
		return $this->getPersistentProvider()->createQuery('modules_customer/address');
	}
	
	/**
	 * Returns the default address attached to the given customer
	 * @param customer_persistentdocument_customer $customer
	 * @return customer_persistentdocument_address
	 */
	public function getDefaultByCustomer($customer)
	{
		if ($customer && $customer->getAddressCount())
		{
			return $customer->getAddress(0);
		}
		return null;
	}
	
	/**
	 * Returns an array with all the addresses for the given zone and customer.
	 * @return Array<customer_persistentdocument_address>
	 */
	public function getByZoneAndCustomer($zone, $customer)
	{
		$validAddressArray = array();
		$addressArray = $customer->getAddressArray();
		foreach ($addressArray as $address)
		{
			$country = $address->getCountry();
			$zipCode = $address->getZipCode();
			if (zone_ZoneService::getInstance()->isCountryInZone($country, $zone) && 
				zone_CountryService::getInstance()->isZipCodeValid($country->getId(), $zipCode))
			{
				$validAddressArray[] = $address;
			}
		}
		return $validAddressArray;
	}
	
	/**
	 * Duplicate the address for the order.
	 * @param customer_persistentdocument_address $address
	 * @return customer_persistentdocument_address
	 */
	public function createFiledCopyForOrder($address)
	{
		$duplicateAddress = $address->duplicate();
		$duplicateAddress->setPublicationstatus('FILED');
		if ($duplicateAddress->getIsdefault())
		{
			$duplicateAddress->setIsdefault(false);
		}
		$duplicateAddress->save();
		return $duplicateAddress;
	}
	
	/**
	 * @param customer_persistentdocument_address $address
	 * @param boolean $includeTitle
	 * @return string
	 */
	public function getFullName($address, $includeTitle = true)
	{
		$label = array();
		if ($includeTitle && $address->getTitle() !==  null)
		{
			$label[] = $address->getTitle()->getLabel();
		}
		$label[] = $address->getFirstname();
		$label[] = $address->getLastname();
		return implode(' ', $label);
	}
	
	/**
	 * @param customer_persistentdocument_address $address
	 * @param customer_persistentdocument_customer $customer
	 */
	public function initFieldsFromCustomer($address, $customer)
	{
		$user = $customer->getUser();
		$address->setTitle($user->getTitle());
		$address->setFirstname($user->getFirstname());
		$address->setLastname($user->getLastname());
		$address->setEmail($user->getEmail());
	}
	
	/**
	 * @param customer_persistentdocument_address $document
	 * @param integer $parentNodeId
	 */
	protected function preInsert($document, $parentNodeId) 
	{
		if ($document->getLabel() === null)
		{
			$document->setLabel($this->getFullName($document));
		}	
	}
	
	/**
	 * @param customer_persistentdocument_address $address
	 * @return array
	 */
	public function getAddressInfos($address)
	{
		$ls = LocaleService::getInstance();
		$addressInfo = array();
		if ($address->getTitle() !== null)
		{
			$addressInfo['title'] = $address->getTitle()->getLabel();
		}
		else
		{
			$addressInfo['title'] = '';
		}
		$addressInfo['firstname'] = $address->getFirstname();
		$addressInfo['lastname'] = $address->getLastname();
		$addressInfo['addressline1'] = $address->getAddressLine1();
		$addressInfo['addressline2'] = $address->getAddressLine2();
		$addressInfo['addressline3'] = $address->getAddressLine3();
		$addressInfo['zipcode'] = $address->getZipcode();
		$addressInfo['city'] = $address->getCity();
		if ($address->getCountry() !== null)
		{
			$addressInfo['country'] = $address->getCountry()->getLabel();
		}
		else
		{
			$addressInfo['country'] = '';
		}
		$addressInfo['email'] = $address->getEmail();
		$addressInfo['phone'] = $address->getPhone();
		$addressInfo['mobilephone'] = $address->getMobilephone();
		$addressInfo['creationdate'] = date_Formatter::toDefaultDateTime($address->getUICreationdate());
		return $addressInfo;
	}
}
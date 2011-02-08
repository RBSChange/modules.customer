<?php
/**
 * customer_AddressService
 * @package mdoules.order
 */
class customer_AddressService extends f_persistentdocument_DocumentService
{
	/**
	 * Singleton
	 * @var customer_AddressService
	 */
	private static $instance = null;

	/**
	 * @return customer_AddressService
	 */
	public static function getInstance()
	{
		if (is_null(self::$instance))
		{
			$className = get_class();
			self::$instance = new $className();
		}
		return self::$instance;
	}

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
		return $this->pp->createQuery('modules_customer/address');
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
	 * @param Boolean $includeTitle
	 * @return String
	 */
	public function getFullName($address, $includeTitle = true)
	{
		$label = array();
		if ($includeTitle && $address->getTitle() !==  null)
		{
			$label[] = $address->getTitle()->getLabelAsHtml();
		}
		$label[] = $address->getFirstnameAsHtml();
		$label[] = $address->getLastnameAsHtml();
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
	 * @param unknown_type $parentNodeId
	 */
	protected function preInsert($document, $parentNodeId) 
	{
		if ($document->getLabel() === null)
		{
			$document->setLabel($this->getFullName($document));
		}	
	}
}
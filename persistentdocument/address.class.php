<?php
/**
 * customer_persistentdocument_address
 * @package modules.customer
 */
class customer_persistentdocument_address extends customer_persistentdocument_addressbase
{
	
	/**
	 * @param String $email
	 * @return Boolean
	 */
	protected function setEmailInternal($email)
	{
		if ($email != null)
		{
			$email = f_util_StringUtils::strtolower(strval($email));
		}
		return parent::setEmailInternal($email);
	}

	/**
	 * @return customer_persistentdocument_customer
	 */
	public function getCustomer()
	{
		return f_util_ArrayUtils::firstElement($this->getCustomerArrayInverse(0, 1));
	}
	
	/**
	 * @param customer_persistentdocument_address $address
	 * @param Boolean $includeTitle
	 * @return String
	 */
	public function getFullName($includeTitle = true)
	{
		return $this->getDocumentService()
			->getFullName($this, $includeTitle);
	}
	
	/**
	 * @param customer_persistentdocument_address $address
	 * @param Boolean $includeTitle
	 * @return String
	 */
	public function getFullNameAsHtml($includeTitle = true)
	{
		return f_util_HtmlUtils::textToHtml($this->getFullName($includeTitle));
	}
	
	/**
	 * @param list_persistentdocument_item $title
	 */
	public function setTitle($title)
	{
		if ($title instanceof list_persistentdocument_item)
		{
			$this->setTitleid($title->getId());
		}
		else 
		{
			$this->setTitleid(null);
		}
	}
	

	/**
	 * @param zone_persistentdocument_country $country
	 */
	public function setCountry($country)
	{
		if ($country instanceof zone_persistentdocument_country)
		{
			$this->setCountryid($country->getId());
		}
		else 
		{
			$this->setCountryid(null);
		}
	}
	
	/**
	 * @return list_persistentdocument_item
	 */
	public function getTitle()
	{
		try
		{
			$titleId = $this->getTitleid();
			if ($titleId)
			{
				return DocumentHelper::getDocumentInstance($titleId, 'modules_list/item');
			}
		}
		catch ( Exception $e )
		{
			Framework::exception($e);
		}
		return null;
	}
	
	/**
	 * @return zone_persistentdocument_country
	 */
	public function getCountry()
	{
		try
		{
			$countryId = $this->getCountryid();
			if ($countryId)
			{
				return DocumentHelper::getDocumentInstance($countryId, 'modules_zone/country');
			}
		}
		catch ( Exception $e )
		{
			Framework::exception($e);
		}
		return null;
	}
	
	/**
	 * @return string
	 */
	public function getCivility()
	{
		$title = $this->getTitle();
		return ($title !== null) ? $title->getLabel() : null;
	}
	
	/**
	 * @return string
	 */
	public function getCountryName()
	{
		$country = $this->getCountry();
		return $country !== null ? $country->getLabel() : null;
	}	

	/**
	 * @return string
	 */
	public function getCountryCode()
	{
		$country = $this->getCountry();
		return $country !== null ? $country->getCode() : null;
	}	
	
}
<?php
/**
 * customer_persistentdocument_customer
 * @package modules.customer
 */
class customer_persistentdocument_customer extends customer_persistentdocument_customerbase
{
	/**
	 * @return customer_persistentdocument_address
	 */
	public function getDefaultAddress()
	{
		return customer_AddressService::getInstance()->getDefaultByCustomer($this);
	}
	
	/**
	 * @return customer_persistentdocument_address[]
	 */
	public function getSecondaryAddresses()
	{
		return array_slice($this->getAddressArray(), 1);
	}

	/**
	 * @return String
	 */
	public function getNotActivatedReasonLabel()
	{
		$reason = "";
		switch ($this->getNotActivatedReason())
		{
			case customer_CustomerService::REASON_CONFIRM_ACCOUNT:
				$reason = f_Locale::translate("&modules.customer.bo.general.Account-confirmation-required;");
				break;
			case customer_CustomerService::REASON_CONFIRM_EMAIL_ADDRESS :
				$reason = f_Locale::translate("&modules.customer.bo.general.Email-address-confirmation-required;");
				break;
		}
		return $reason;
	}

	/**
	 * @return website_persistentdocument_website
	 * @throws customer_Exception
	 */
	public function getWebsite()
	{
		$user = $this->getUser();
		if ($user instanceof users_persistentdocument_websitefrontenduser)
		{
			return DocumentHelper::getDocumentInstance($user->getWebsiteid());
		}
		// This is not suposed to happen if the users module patch 0005 is
		// correctly executed.
		else
		{
			throw new customer_Exception('The user with id ' . $user->getId() . ' is not a websitefrontenduser! Please check the users module patch 0005.');
		}
	}

	/**
	 * @return String
	 */
	public function canBeTrustedAsString()
	{
		return f_Locale::translate($this->getCanBeTrusted() ? '&modules.customer.bo.general.Yes;' : '&modules.customer.bo.general.No;');
	}

	/**
	 * @param order_CartInfo $cart
	 */
	public function setCart($cart)
	{
		if ($cart instanceof order_CartInfo) 
		{
			$this->setCartSerialized(serialize($cart));
			if (!$cart->isEmpty())
			{
				$this->setLastCartUpdate(date_Calendar::getInstance()->toString());
			}
			else
			{
				$this->setLastCartUpdate(null);
			}
		}
		else
		{
			$this->setCartSerialized(null);
			$this->setLastCartUpdate(null);
		}
	}

	/**
	 * @return order_CartInfo
	 */
	public function getCart()
	{
		$cart = $this->getCartSerialized();
		if ($cart === null)
		{
			return null;
		}
		return unserialize($cart);
	}

	
	/**
	 * @param string $moduleName
	 * @param string $treeType
	 * @param array<string, string> $nodeAttributes
	 */
	protected function addTreeAttributes($moduleName, $treeType, &$nodeAttributes)
	{
		if ($treeType != 'wlist')
		{
			return;
		}
		$nodeAttributes['birthday'] = date_DateFormat::format($this->getBirthday(), 'D d M Y');
		$nodeAttributes['email'] = $this->getUser()->getEmail();
		$nodeAttributes['date'] = date_DateFormat::format($this->getCreationdate(), 'D d M Y');

		// Activation.
		if (!$this->isPublished())
		{
			$nodeAttributes['activation'] = $this->getNotActivatedReasonLabel();
		}
		else
		{
			$nodeAttributes['activation'] = f_Locale::translateUI("&modules.customer.bo.general.Account-activated;");
		}
		
		// Website.
		$website = $this->getWebsite();
		if (!is_null($website))
		{
			$nodeAttributes['website'] = $website->getLabel();
		}
		else 
		{
			$nodeAttributes['website'] = '-';
		}
		
		$anonymizer = customer_AnonymizerService::getInstance();		
		$nodeAttributes['canBeAnonymized'] = (!$anonymizer->isAnonymized($this) && $anonymizer->canBeAnonymized($this));	
	}
		
	/**
	 * @param string[] $propertiesNames
	 * @param array $formProperties
	 */
	public function addFormProperties($propertiesNames, &$formProperties)
	{
		$user = $this->getUser();
		
		$dateTimeFormat = customer_ModuleService::getInstance()->getUIDateTimeFormat();
		
		$formProperties['activateTrust'] = ModuleService::getInstance()->getPreferenceValue('customer', 'activateTrust');
		
		// Identification.
		$identification = array();
		if ($this->getWebsite() !== null)
		{
			$identification['website'] = $this->getWebsite()->getLabel();
		}
		if ($user->getTitle() !== null)
		{
			$identification['title'] = $user->getTitle()->getLabel();
		}
		$identification['firstname'] = $user->getFirstname();
		$identification['lastname'] = $user->getLastname();
		$identification['birthday'] = date_DateFormat::format($this->getUIBirthday(), 'd M Y');
		$identification['email'] = $user->getEmail();
		$identification['creationdate'] = date_DateFormat::format($this->getUICreationdate(), $dateTimeFormat);
		$formProperties['identification'] = $identification;
		
		// Addresses.
		$addresses = array();
		foreach ($this->getAddressArray() as $index => $address)
		{	
			$addressInfo = array();
			if ($index == 0)
			{
				$addressInfo['label'] = f_Locale::translateUI('&modules.customer.bo.general.Default-address;');
			}
			else
			{
				$addressInfo['label'] = f_Locale::translateUI('&modules.customer.bo.general.Address-title;', array('number' => $index+1));
			}
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
			$addressInfo['creationdate'] = date_DateFormat::format($address->getUICreationdate(), $dateTimeFormat);
			$addresses[] = $addressInfo;
		}
		$formProperties['addresses'] = $addresses;
	}
	
	/**
	 * @return string
	 */
	public function getCode()
	{
		if (!$this->hasMeta('customerCode'))
		{
			$this->setMeta('customerCode', customer_CustomerCodeGenerator::getInstance()->generate($this));
			$this->saveMeta();
		}
		return $this->getMeta('customerCode');
	}
	
	//Wrapped user info
	
	/**
	 * @return string
	 */
	public function getCivility()
	{
		$user = $this->getUser();
		if ($user !== null)
		{
			$title = $user->getTitle();
			if ($title !== null)
			{
				return $title->getLabel();
			}
		}
		return null;
	}
	
	/**
	 * @return string
	 */
	public function getFirstname()
	{
		$user = $this->getUser();
		return $user !== null ? $user->getFirstname() : null;
	}	
	
	/**
	 * @return string
	 */
	public function getLastname()
	{
		$user = $this->getUser();
		return $user !== null ? $user->getLastname() : null;
	}	
	
	/**
	 * @return string
	 */
	public function getEmail()
	{
		$user = $this->getUser();
		return $user !== null ? $user->getEmail() : null;
	}
}
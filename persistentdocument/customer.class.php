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
	 * @return website_persistentdocument_website
	 * @throws customer_Exception
	 */
	public function getWebsite()
	{
		$website = null;
		$user = $this->getUser();
		if ($user instanceof users_persistentdocument_user)
		{
			
			$profile = users_UsersprofileService::getInstance()->getByAccessorId($user->getId());
			if ($profile !== null && $profile->getRegisteredwebsiteid())
			{
				$website = DocumentHelper::getDocumentInstance($profile->getRegisteredwebsiteid());
			}
			else
			{
				$groupIds = DocumentHelper::getIdArrayFromDocumentArray($user->getGroupsArray());
				if (count($groupIds))
				{
					$website = website_WebsiteService::getInstance()->createQuery()
						->add(Restrictions::in('group.id', $groupIds))
						->setMaxResults(1)->findUnique();
				}
			}
		}
		
		if ($website === null)
		{
			throw new customer_Exception('The customer with id ' . $this->getId() . ' has no website default website');
		}
	}

	/**
	 * @return string
	 */
	public function canBeTrustedAsString()
	{
		return LocaleService::getInstance()->trans('m.uixul.frontoffice.' . ($this->getCanBeTrusted() ? 'yes' : 'no'), array('ucf'));
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
	
	// Wrapped user info.
	
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
	
	// Deprecated.
	
	/**
	 * @deprecated use getCodeReference
	 */
	public function getCode()
	{
		if (!$this->hasMeta('customerCode'))
		{
			return $this->getCodeReference();
		}
		$codeRef = $this->getMeta('customerCode');
		$this->setMeta('customerCode', null);
		$this->setCodeReference($codeRef);
		$this->saveMeta();
		return $codeRef;
	}
}
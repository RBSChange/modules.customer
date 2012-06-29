<?php
/**
 * @package mdoules.customer
 * @method customer_ListShippingcountryService getInstance()
 */
class customer_ListShippingcountryService extends change_BaseService implements list_ListItemsService
{
	/**
	 * @see list_persistentdocument_dynamiclist::getItems()
	 * @return list_Item[]
	 */
	public final function getItems()
	{
		$results = array();
		$shop = catalog_ShopService::getInstance()->getCurrentShop();
		$countries = $this->getAvailableCountries($shop);
		foreach ($countries as $country) 
		{
			$results[] = new list_Item($country->getLabel(), $country->getId());
		}
		return $results;
	}
	
	protected $countries = array();
	
	/**
	 * @param catalog_persistentdocument_shop $shop
	 */
	public function getAvailableCountries($shop)
	{
		if ($shop instanceof catalog_persistentdocument_shop)
		{
			$billingArea = $shop->getCurrentBillingArea();
			if (!isset($this->countries[$shop->getId()]))
			{
				$list = array();
				$reorder = false;
				$countries = catalog_ShippingfilterService::getInstance()->getAvailableCountriesForShop($shop);
				foreach ($countries as $country) 
				{
					$list[$country->getLabel()] = $country;
				}
				
				foreach (catalog_TaxService::getInstance()->getAllZonesForBillingArea($billingArea) as $zone) 
				{
					foreach (zone_CountryService::getInstance()->getCountries($zone) as $country)
					{
						if (!isset($list[$country->getLabel()]))
						{
							$list[$country->getLabel()] = $country;
							$reorder = true;
						}
					}
				}
							
				if ($reorder) {ksort($list);}			
				$this->countries[$shop->getId()] = array_values($list);
			}
			return $this->countries[$shop->getId()];
		}
		return array();
	}
}
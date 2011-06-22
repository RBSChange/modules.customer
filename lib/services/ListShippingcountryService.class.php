<?php
/**
 * customer_ListShippingcountryService
 * @package module.customer
 */
class customer_ListShippingcountryService extends BaseService
{
	/**
	 * @var customer_ListShippingcountryService
	 */
	private static $instance;

	/**
	 * @return customer_ListShippingcountryService
	 */
	public static function getInstance()
	{
		if (self::$instance === null)
		{
			self::$instance = self::getServiceClassInstance(get_class());
		}
		return self::$instance;
	}

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
			if (!isset($this->countries[$shop->getId()]))
			{
				$list = array();
				$reorder = false;
				$countries = catalog_ShippingfilterService::getInstance()->getAvailableCountriesForShop($shop);
				foreach ($countries as $country) 
				{
					$list[$country->getLabel()] = $country;
				}
				
				foreach (catalog_TaxService::getInstance()->getZonesForShop($shop) as $zone) 
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
	
	/**
	 * @see list_persistentdocument_dynamiclist::getItemByValue()
	 * @param string $value;
	 * @return list_Item
	 */
//	public function getItemByValue($value)
//	{
//	}
}
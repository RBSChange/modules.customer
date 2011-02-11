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
		if ($shop === null)
		{
			return $results;
		}
		
		foreach (catalog_TaxService::getInstance()->getZonesForShop($shop) as $zone) 
		{
			foreach (zone_CountryService::getInstance()->getCountries($zone) as $country)
			{
				$results[$country->getId()] = new list_Item($country->getLabel(), $country->getId());
			}
		}
		return $results;
	}

	/**
	 * @var Array
	 */
	private $parameters = array();
	
	/**
	 * @see list_persistentdocument_dynamiclist::getListService()
	 * @param array $parameters
	 */
	public function setParameters($parameters)
	{
		$this->parameters = $parameters;
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
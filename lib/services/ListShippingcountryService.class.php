<?php
/**
 * customer_ListShippingcountryService
 * @package module.customer
 */
class customer_ListShippingcountryService extends customer_ListBaseCountryService
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
		if (is_null(self::$instance))
		{
			self::$instance = new customer_ListShippingcountryService();
		}
		return self::$instance;
	}

	protected function __construct()
	{
		try 
		{
			$shop = catalog_ShopService::getInstance()->getCurrentShop();
		}
		catch (Exception $e)
		{
			$shop = null;
			Framework::exception($e);
		}		
		$zone = ($shop !== null) ? $shop->getShippingZone() : zone_ZoneService::getInstance()->getDefaultZone();
		$this->setZone($zone);
	}
}
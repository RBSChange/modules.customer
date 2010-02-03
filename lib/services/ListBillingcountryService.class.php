<?php
/**
 * customer_ListShippingcountryService
 * @package module.customer
 */
class customer_ListBillingcountryService extends customer_ListBaseCountryService
{
	/**
	 * @var customer_ListBillingcountryService
	 */
	private static $instance;

	/**
	 * @return customer_ListBillingcountryService
	 */
	public static function getInstance()
	{
		if (is_null(self::$instance))
		{
			self::$instance = new customer_ListBillingcountryService();
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
		$zone = ($shop !== null) ? $shop->getBillingZone() : zone_ZoneService::getInstance()->getDefaultZone();
		$this->setZone($zone);
	}
}
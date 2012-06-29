<?php
/**
 * @package mdoules.customer
 * @method customer_ListBillingcountryService getInstance()
 */
class customer_ListBillingcountryService extends customer_ListBaseCountryService
{
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
		$zone = ($shop !== null) ? $shop->getCurrentBillingArea()->getBillingAddressZone() : zone_ZoneService::getInstance()->getDefaultZone();
		$this->setZone($zone);
	}
}
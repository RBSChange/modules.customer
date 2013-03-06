<?php
/**
 * customer_ListBaseCountryService
 * @package module.customer
 */
abstract class customer_ListBaseCountryService extends BaseService
{
	/**
	 * @var zone_persistentdocument_zone
	 */
	private $zone;

	/**
	 * @return array<list_Item>
	 */
	public function getItems()
	{
		if ($this->zone instanceof zone_persistentdocument_zone)
		{
			$results = array();
			foreach (zone_CountryService::getInstance()->getCountries($this->zone) as $country)
			{
				$results[$country->getId()] = new list_Item($country->getLabel(), $country->getId());
			}
			return $results;
		}
		if (Framework::isDebugEnabled())
		{
			Framework::error(__METHOD__ . ' zone not defined');
		}
		return array();
	}
	
	/**
	 * @param String $value
	 * @return list_Item
	 */
	public function getItemByValue($value)
	{
		try 
		{
			$country = DocumentHelper::getDocumentInstance($value);
			return new list_Item($country->getLabel(), $country->getId());
		} 
		catch (Exception $e)
		{
			Framework::exception($e);
		}
		return null;
	}

	/**
	 * @return String
	 */
	public function getDefaultId()
	{
		$items = $this->getItems();
		return f_util_ArrayUtils::firstElement($items)->getValue();
	}
	
	
	/**
	 * @param zone_persistentdocument_zone $zone
	 * @return customer_ListBaseCountryService
	 */
	protected function setZone($zone)
	{
		$this->zone = $zone;
		return $this;
	}
}
<?php
/**
 * @package mdoules.customer
 * @method customer_ListTitleService getInstance()
 */
class customer_ListTitleService extends change_BaseService implements list_ListItemsService
{
	/**
	 * @return Array<Integer, list_Item>
	 */
	public function getItems()
	{
		$results = array();

		$items = list_ListService::getInstance()->getDocumentInstanceByListId('modules_users/title')->getItems();
		foreach ($items as $item)
		{
			$results[$item->getValue()] = $item;
		}
		
		return $results;
	}

	/**
	 * @return integer
	 */
	public function getDefaultId()
	{
		return 0;
	}
}
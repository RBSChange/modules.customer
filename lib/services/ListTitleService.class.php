<?php
/**
 * customer_ListTitleService
 * @author intportg
 */
class customer_ListTitleService extends BaseService
{
	/**
	 * @var customer_ListTitleService
	 */
	private static $instance;

	/**
	 * @return customer_ListTitleService
	 */
	public static function getInstance()
	{
		if (is_null(self::$instance))
		{
			self::$instance = new self();
		}
		return self::$instance;
	}

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
	 * @return Integer
	 */
	public function getDefaultId()
	{
		return 0;
	}
}
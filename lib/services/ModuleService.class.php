<?php
/**
 * @package modules.customer.lib.services
 */
class customer_ModuleService extends ModuleBaseService
{
	/**
	 * Singleton
	 * @var customer_ModuleService
	 */
	private static $instance = null;

	/**
	 * @return customer_ModuleService
	 */
	public static function getInstance()
	{
		if (is_null(self::$instance))
		{
			self::$instance = self::getServiceClassInstance(get_class());
		}
		return self::$instance;
	}
	
	// Deprecated.

	/**
	 * @deprecated
	 */
	public function getUIDateFormat()
	{
		return f_Locale::translateUI('&modules.uixul.bo.datePicker.calendar.dataWriterFormat;');
	}
	
	/**
	 * @deprecated
	 */
	public function getUIDateTimeFormat()
	{
		return f_Locale::translateUI('&modules.uixul.bo.datePicker.calendar.dataWriterTimeFormat;');
	}
}
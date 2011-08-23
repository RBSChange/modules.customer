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
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * @param string $name
	 * @param array $arguments
	 * @deprecated
	 */
	public function __call($name, $arguments)
	{
		switch ($name)
		{
			case 'getUIDateFormat': 
				Framework::error('Call to deleted ' . get_class($this) . '->getUIDateFormat method');
				return date_Formatter::getDefaultDateFormat(RequestContext::getInstance()->getUILang());
				
			case 'getUIDateTimeFormat':
				Framework::error('Call to deleted ' . get_class($this) . '->getUIDateTimeFormat method');
				return date_Formatter::getDefaultDateTimeFormat(RequestContext::getInstance()->getUILang());
			
			default: 
				throw new Exception('No method ' . get_class($this) . '->' . $name);
		}
	}
}
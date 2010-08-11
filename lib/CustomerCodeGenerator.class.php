<?php
class customer_CustomerCodeGenerator
{
	/**
	 * @var customer_CustomerCodeGenerator
	 */
	private static $instance;

	/**
	 * Constructor of customer_CustomerCodeGenerator
	 */
	private function __construct()
	{
	}

	/**
	 * @return customer_CustomerCodeGenerator
	 */
	public static function getInstance()
	{
		if (is_null(self::$instance))
		{
			self::$instance = new customer_CustomerCodeGenerator();
		}
		return self::$instance;
	}

	/**
	 * @return void
	 */
	public static final function clearInstance()
	{
		if (PROFILE != 'test')
		{
			throw new Exception(__METHOD__." is only available in test mode.");
		}
		self::$instance = null;
	}

	/**
	 * @var customer_CustomerCodeStrategy
	 */
	private $strategy = null;

	/**
	 * @param customer_CustomerCodeStrategy $strategy
	 * @return customer_CustomerCodeGenerator $this
	 */
	public final function setStrategy($strategy)
	{
		$this->strategy = $strategy;
		return $this;
	}

	/**
	 * @return customer_CustomerCodeStrategy
	 */
	public final function getStrategy()
	{
		if ($this->strategy === null)
		{
			$className = Framework::getConfigurationValue('modules/customer/customerCodeStrategyClass', 'customer_CustomerCodeDefaultStrategy');
			$this->strategy = new $className;
		}
		return $this->strategy;
	}

	/**
	 * @param customer_persistentdocument_customer $order
	 * @return String
	 */
	public final function generate($order)
	{
		return $this->getStrategy()->generate($order);
	}
}
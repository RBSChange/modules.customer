<?php
/**
 * customer_CouponService
 * @package customer
 */
class customer_CouponService extends f_persistentdocument_DocumentService
{
	/**
	 * @var customer_CouponService
	 */
	private static $instance;

	/**
	 * @return customer_CouponService
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
	 * @return customer_persistentdocument_coupon
	 */
	public function getNewDocumentInstance()
	{
		return $this->getNewDocumentInstanceByModelName('modules_customer/coupon');
	}

	/**
	 * Create a query based on 'modules_customer/coupon' model.
	 * Return document that are instance of modules_customer/coupon,
	 * including potential children.
	 * @return f_persistentdocument_criteria_Query
	 */
	public function createQuery()
	{
		return $this->pp->createQuery('modules_customer/coupon');
	}
	
	/**
	 * Create a query based on 'modules_customer/coupon' model.
	 * Only documents that are strictly instance of modules_customer/coupon
	 * (not children) will be retrieved
	 * @return f_persistentdocument_criteria_Query
	 */
	public function createStrictQuery()
	{
		return $this->pp->createQuery('modules_customer/coupon', false);
	}
}
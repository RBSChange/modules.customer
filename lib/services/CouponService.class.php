<?php
/**
 * @package mdoules.customer
 * @method customer_CouponService getInstance()
 */
class customer_CouponService extends f_persistentdocument_DocumentService
{
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
		return $this->getPersistentProvider()->createQuery('modules_customer/coupon');
	}
	
	/**
	 * Create a query based on 'modules_customer/coupon' model.
	 * Only documents that are strictly instance of modules_customer/coupon
	 * (not children) will be retrieved
	 * @return f_persistentdocument_criteria_Query
	 */
	public function createStrictQuery()
	{
		return $this->getPersistentProvider()->createQuery('modules_customer/coupon', false);
	}

	
	/**
	 * @param string $code
	 * @return coupon_persistentdocument_coupon | null
	 */
	public function getByCode($code)
	{
		return $this->createQuery()->add(Restrictions::eq('code', $code))->findUnique();
	}
		
	/**
	 * @param customer_persistentdocument_coupon $coupon
	 * @param order_CartInfo $cart
	 * @return boolean
	 */
	public function validateForCart($coupon, $cart)
	{
		return false;
	}
}
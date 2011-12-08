<?php
/**
 * customer_VoucherService
 * @package modules.customer
 */
class customer_VoucherService extends customer_CouponService
{
	/**
	 * @var customer_VoucherService
	 */
	private static $instance;

	/**
	 * @return customer_VoucherService
	 */
	public static function getInstance()
	{
		if (self::$instance === null)
		{
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * @return customer_persistentdocument_voucher
	 */
	public function getNewDocumentInstance()
	{
		return $this->getNewDocumentInstanceByModelName('modules_customer/voucher');
	}

	/**
	 * Create a query based on 'modules_customer/voucher' model.
	 * Return document that are instance of modules_customer/voucher,
	 * including potential children.
	 * @return f_persistentdocument_criteria_Query
	 */
	public function createQuery()
	{
		return $this->pp->createQuery('modules_customer/voucher');
	}
	
	/**
	 * Create a query based on 'modules_customer/voucher' model.
	 * Only documents that are strictly instance of modules_customer/voucher
	 * (not children) will be retrieved
	 * @return f_persistentdocument_criteria_Query
	 */
	public function createStrictQuery()
	{
		return $this->pp->createQuery('modules_customer/voucher', false);
	}
	
	/**
	 * @param customer_persistentdocument_voucher $coupon
	 * @param order_CartInfo $cart
	 * @return boolean
	 */
	public function validateForCart($coupon, $cart)
	{
		if ($coupon !== null && $coupon->isPublished() && !$cart->isEmpty())
		{
			$customer = $cart->getCustomer();
			if ($customer === null || $customer->getDocumentService()->hasAlreadyUsedCoupon($customer, $coupon))
			{
				return false;
			}
			
			if ($coupon->getCustomerId() && $coupon->getCustomerId() !== $customer->getId())
			{
				return false;
			}
			
			if ($coupon->getShop() !== null && $cart->getShopId() !== $coupon->getShop()->getId())
			{
				return false;
			}
			
			$linesAmount = 0.0;
			foreach ($cart->getCartLineArray($value) as $cartLineInfo) 
			{
				$linesAmount += $cartLineInfo->getTotalValueWithTax();
			}
			
			return $linesAmount >= $coupon->getAmount();
		}
		return false;
	}

	/**
	 * @param customer_persistentdocument_voucher $document
	 * @param array<string, string> $attributes
	 * @param integer $mode
	 * @param string $moduleName
	 */
	public function completeBOAttributes($document, &$attributes, $mode, $moduleName)
	{
		parent::completeBOAttributes($document, $attributes, $mode, $moduleName);
		if ($mode & DocumentHelper::MODE_CUSTOM)
		{
			$shop = $document->getShop();
			$attributes['shop'] = $shop->getLabel();
			$attributes['amount'] = $document->getAmount() . $shop->getCurrencySymbol();
			$customer = $document->getCustomer();
			$attributes['customer'] = ($customer !== null) ? $customer->getLabel() : '-';
		}
	}
}
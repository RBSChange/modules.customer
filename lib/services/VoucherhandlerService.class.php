<?php
/**
 * customer_VoucherhandlerService
 * @package modules.customer
 */
class customer_VoucherhandlerService extends order_CartmodifierService
{
	/**
	 * @var customer_VoucherhandlerService
	 */
	private static $instance;

	/**
	 * @return customer_VoucherhandlerService
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
	 * @return customer_persistentdocument_voucherhandler
	 */
	public function getNewDocumentInstance()
	{
		return $this->getNewDocumentInstanceByModelName('modules_customer/voucherhandler');
	}

	/**
	 * Create a query based on 'modules_customer/voucherhandler' model.
	 * Return document that are instance of modules_customer/voucherhandler,
	 * including potential children.
	 * @return f_persistentdocument_criteria_Query
	 */
	public function createQuery()
	{
		return $this->pp->createQuery('modules_customer/voucherhandler');
	}
	
	/**
	 * Create a query based on 'modules_customer/voucherhandler' model.
	 * Only documents that are strictly instance of modules_customer/voucherhandler
	 * (not children) will be retrieved
	 * @return f_persistentdocument_criteria_Query
	 */
	public function createStrictQuery()
	{
		return $this->pp->createQuery('modules_customer/voucherhandler', false);
	}
	
	/**
	 * @param customer_persistentdocument_voucherhandler $document
	 * @param Integer $parentNodeId Parent node ID where to save the document.
	 * @return void
	 */
	protected function preInsert($document, $parentNodeId = null)
	{
		parent::preInsert($document, $parentNodeId);
		$document->setInsertInTree(false);
		if ($document->getShop() === null)
		{
			$document->setShop(catalog_persistentdocument_shop::getInstanceById($parentNodeId));
		}
	}
	
	/**
	 * @param order_persistentdocument_cartmodifier $modifier
	 * @param order_CartInfo $cart
	 * @return boolean
	 */
	public function validateForCart($modifier, $cart)
	{
		$couponInfos = $cart->getCoupon();
		if (!$couponInfos instanceof order_CouponInfo)
		{
			return false;
		}
		
		$coupon = customer_persistentdocument_coupon::getInstanceById($couponInfos->getId());
		if (!($coupon instanceof customer_persistentdocument_voucher))
		{
			return false;
		}
		
		return $modifier->getShop()->getId() == $cart->getShopId();
	}
	
	/**
	 * @param order_persistentdocument_cartmodifier $modifier
	 * @param order_CartInfo $cart
	 * @return boolean
	 */
	public function applyToCart($modifier, $cart)
	{
		$couponInfos = $cart->getCoupon();
		if (!$couponInfos instanceof order_CouponInfo)
		{
			return false;
		}
		
		$coupon = customer_persistentdocument_coupon::getInstanceById($couponInfos->getId());
		if (!($coupon instanceof customer_persistentdocument_voucher))
		{
			return false;
		}
		
		$value = $coupon->getAmount();		
		if ($value > 0)
		{
			$discountInfo = $cart->getDiscountById($modifier->getId());
			if ($discountInfo === null)
			{
				$discountInfo = new order_DiscountInfo();
				$discountInfo->setId($modifier->getId());
				$discountInfo->setLabel(LocaleService::getInstance()->transFO('m.customer.document.voucherhandler.label-fo', array('ucf', 'lab')));
				$cart->addDiscount($discountInfo);
			}
			$valueWithTax = $cart->getSubTotalWithTax();
			$valueWithoutTax = $cart->getSubTotalWithoutTax();		
			$discountInfo->setValueWithTax($value);
			$discountInfo->setValueWithoutTax(($valueWithTax != 0) ? (($value / $valueWithTax) * $valueWithoutTax) : 0);
			return true;
		}
		return false;
	}
	
	/**
	 * @param order_persistentdocument_cartmodifier $modifier
	 * @param order_CartInfo $cart
	 * @return boolean
	 */
	public function removeFromCart($modifier, $cart)
	{
		$discountInfo = $cart->getDiscountById($modifier->getId());
		if ($discountInfo !== null)
		{
			$cart->removeDiscount($discountInfo);
		}
		return true;
	}
}
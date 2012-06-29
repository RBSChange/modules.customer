<?php
/**
 * @package mdoules.customer
 * @method customer_VoucherService getInstance()
 */
class customer_VoucherService extends customer_CouponService
{
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
		return $this->getPersistentProvider()->createQuery('modules_customer/voucher');
	}
	
	/**
	 * Create a query based on 'modules_customer/voucher' model.
	 * Only documents that are strictly instance of modules_customer/voucher
	 * (not children) will be retrieved
	 * @return f_persistentdocument_criteria_Query
	 */
	public function createStrictQuery()
	{
		return $this->getPersistentProvider()->createQuery('modules_customer/voucher', false);
	}
	
	/**
	 * @param customer_persistentdocument_voucher $document
	 * @param integer $parentNodeId Parent node ID where to save the document (optionnal).
	 * @return void
	 */
	protected function preInsert($document, $parentNodeId)
	{
		if (!$document->getBillingAreaId() && $document->getShop())
		{
			$document->setBillingAreaId($document->getShop()->getDefaultBillingArea()->getId());
		}
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
			if (!DocumentHelper::equals($coupon->getShop(), $cart->getShop()))
			{
				return false;
			}
			
			if ($coupon->getBillingAreaId() != $cart->getBillingAreaId())
			{
				return false;
			}
			
			
			$customer = $cart->getCustomer();
			if ($customer === null || $customer->getDocumentService()->hasAlreadyUsedCoupon($customer, $coupon))
			{
				return false;
			}
			
			if ($coupon->getCustomerId() && $coupon->getCustomerId() !== $customer->getId())
			{
				return false;
			}
			
			$linesAmount = 0.0;
			foreach ($cart->getCartLineArray() as $cartLineInfo) 
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
			$ba = $document->getBillingArea();
			if ($ba instanceof catalog_persistentdocument_billingarea)
			{
				$attributes['amount'] = $ba->formatPrice($document->getAmount(), $shop->getLang());
				$attributes['shop'] .= '/' . $ba->getLabel();
			}
			else 
			{
				$attributes['amount'] = $document->getAmount();
			}
			$customer = $document->getCustomer();
			$attributes['customer'] = ($customer !== null) ? $customer->getLabel() : '-';
		}
	}
}
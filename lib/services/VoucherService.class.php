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
			self::$instance = self::getServiceClassInstance(get_class());
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
	 * @param customer_persistentdocument_voucher $document
	 * @param Integer $parentNodeId Parent node ID where to save the document (optionnal).
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
	 * @param string $moduleName
	 * @param string $treeType
	 * @param array<string, string> $nodeAttributes
	 */
	public function addTreeAttributes($document, $moduleName, $treeType, &$nodeAttributes)
	{
		parent::addTreeAttributes($document, $moduleName, $treeType, $nodeAttributes);
		if ($treeType == 'wlist')
		{
			$shop = $document->getShop();
			$nodeAttributes['shop'] = $shop->getLabel();
			$ba = $document->getBillingArea();
			if ($ba instanceof catalog_persistentdocument_billingarea)
			{
				$nodeAttributes['amount'] = $ba->formatPrice($document->getAmount(), $shop->getLang());
				$nodeAttributes['shop'] .= '/' . $ba->getLabel();
			}
			else
			{
				$nodeAttributes['amount'] = $document->getAmount();
			}
			$customer = $document->getCustomer();
			$nodeAttributes['customer'] = ($customer !== null) ? $customer->getLabel() : '-';
		}
	}
}
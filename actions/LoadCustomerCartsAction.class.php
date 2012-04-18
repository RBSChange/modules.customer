<?php
/**
 * customer_LoadCustomerCartsAction
 * @package modules.customer.actions
 */
class customer_LoadCustomerCartsAction extends f_action_BaseJSONAction
{
	/**
	 * @param Context $context
	 * @param Request $request
	 */
	public function _execute($context, $request)
	{
		$ls = LocaleService::getInstance();
		$customer = $this->getDocumentInstanceFromRequest($request);
		
		$result = array();		
		if (ModuleService::getInstance()->moduleExists('order'))
		{
			/* @var $cart order_CartInfo */
			$cart = $customer->getCart();
			if ($cart && $cart->getCartLineCount() != 0)
			{
				try
				{
					/* @var $shop catalog_persistentdocument_shop */
					$shop = $cart->getShop();
					$billingArea = $cart->getBillingArea();
					$cartInfo = array();
					
					// Global infos.
					$cartInfo['label'] = $ls->transBO('m.customer.bo.doceditor.panel.carts.title', array('ucf'), array('shop' => $shop->getLabel() . ' / ' . $billingArea->getLabel()));
					$lastUpdate = $customer->getUILastCartUpdate($cart->getShopId());
					if ($lastUpdate !== null)
					{
						$cartInfo['lastupdate'] = date_Formatter::toDefaultDateTimeBO($lastUpdate);
					}
					else
					{
						$lastUpdate = $ls->transBO('m.customer.bo.doceditor.panel.carts.unknown', array('ucf'));
					}
					
					$cartInfo['shippingLabel'] = '';
					if ($cart->getShippingLabel())
					{
						$cartInfo['shippingLabel'] = $cart->getShippingLabel();
					}
					$cartInfo['billingLabel'] = '';
					if ($cart->getBillingLabel())
					{
						$cartInfo['billingLabel'] = $cart->getBillingLabel();
					}
					
					if ($cart->hasCoupon())
					{
						$cartInfo['couponLabel'] = $cart->getCoupon()->getLabel();
					}
					else
					{
						$cartInfo['couponLabel'] = '';
					}
					$cartInfo['subtotalWithoutTax'] = $billingArea->formatPrice($cart->getSubTotalWithoutTax());
					$cartInfo['subtotalWithTax'] = $billingArea->formatPrice($cart->getSubTotalWithTax());
					
					$v = $cart->getDiscountTotalWithTax();
					$cartInfo['discountTotalWithTax'] = ($v > 0) ?$billingArea->formatPrice($v) : '';
					
					
					$v = $cart->getFeesTotalWithTax();
					$cartInfo['feesTotalWithTax'] =  ($v > 0) ?$billingArea->formatPrice($v) : '';
					
					$cartInfo['totalWithoutTax'] = $billingArea->formatPrice($cart->getTotalWithoutTax());				
					$cartInfo['totalWithTax'] = $billingArea->formatPrice($cart->getTotalWithTax());
					
					$cartInfo['tvaAmount'] = $billingArea->formatPrice($cart->getTotalTax());
					
					$v = $cart->getTotalCreditNoteAmount();
					$cartInfo['totalCreditNoteAmount'] = ($v > 0) ?$billingArea->formatPrice($v) : '';
					
					$cartInfo['totalAmount'] = $billingArea->formatPrice($cart->getTotalAmount());
					
					// Lines.
					$cartInfo['lines'] = array();
					foreach ($cart->getCartLineArray() as $line)
					{
						$lineInfo = $this->getLineInfo($line, 'cart-line', $billingArea);
						if ($lineInfo !== null)
						{
							$cartInfo['lines'][] = $lineInfo;
						}
					}
					
					$result[] = $cartInfo;
				}
				catch (Exception $e)
				{
					Framework::exception($e);
				}
			}
		}
		
		return $this->sendJSON($result);
	}
	
	/**
	 * @param order_CartLineInfo $line
	 * @param String $type
	 * @param catalog_persistentdocument_billingarea $billingArea
	 * @return Array<String => String>
	 */
	private function getLineInfo($line, $type, $billingArea)
	{
		$lineInfo = array();
		$lineInfo['linetype'] = $type;
		$product = $line->getProduct();
		if ($product === null)
		{
			$lineInfo['productLabel'] = LocaleService::getInstance()->transBO('m.customer.bo.doceditor.panel.carts.unexisting-product', array('ucf'));
			$lineInfo['codeReference'] = '';
			$lineInfo['availability'] = '';
		}
		else
		{
			$lineInfo['productLabel'] = $product->getLabel();
			$lineInfo['codeReference'] = $product->getCodeReference();
			$lineInfo['availability'] = $product->getAvailability();
		}

		$lineInfo['unitPriceWithoutTax'] = $billingArea->formatPrice($line->getValueWithoutTax());
		$lineInfo['unitPriceWithTax'] = $billingArea->formatPrice($line->getValueWithTax());
		$lineInfo['quantity'] = $line->getQuantity();
		$lineInfo['totalPriceWithoutTax'] = $billingArea->formatPrice($line->getTotalValueWithoutTax());
		$lineInfo['totalPriceWithTax'] = $billingArea->formatPrice($line->getTotalValueWithTax());
		
		return $lineInfo;
	}
}
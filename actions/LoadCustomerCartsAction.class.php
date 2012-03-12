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
		$customer = $this->getDocumentInstanceFromRequest($request);
		$dateTimeFormat = customer_ModuleService::getInstance()->getUIDateTimeFormat();
		
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
					$cartInfo['label'] = f_Locale::translateUI('&modules.customer.bo.doceditor.panel.carts.Title;', array('shop' => $shop->getLabel()));
					$lastUpdate = $customer->getUILastCartUpdate($cart->getShopId());
					if ($lastUpdate !== null)
					{
						$cartInfo['lastupdate'] = date_DateFormat::format($lastUpdate, $dateTimeFormat);
					}
					else
					{
						$lastUpdate = f_Locale::translateUI('&modules.customer.bo.doceditor.panel.carts.Unknown;');
					}
					
					$cartInfo['shippingLabel'] = '';
					if ($cart->getShippingLabel())
					{
						$cartInfo['shippingLabel'] = $cart->getShippingLabel();
						$cartInfo['shippingPrice'] = $billingArea->formatPrice($cart->getShippingPriceWithTax());
					}
					$cartInfo['billingLabel'] = '';
					if ($cart->getBillingLabel())
					{
						$cartInfo['billingLabel'] = $cart->getBillingLabel();
					}
					
					$cartInfo['couponLabel'] = '';
					
					$cartInfo['totalWithoutTax'] = $billingArea->formatPrice($cart->getTotalWithoutTax());
					$cartInfo['tvaAmount'] = $billingArea->formatPrice($cart->getTotalTax());
					$cartInfo['totalWithTax'] = $billingArea->formatPrice($cart->getTotalWithTax());
					
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
			$lineInfo['productLabel'] = f_Locale::translateUI('&module.customer.bo.doceditor.panel.carts.Unexisting-product;');
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
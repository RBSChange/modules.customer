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
			$cart = $customer->getCart();
			if ($cart && $cart->getCartLineCount() != 0)
			{
				try
				{
					$shop = $cart->getShop();
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
						$cartInfo['shippingPrice'] = $shop->formatPrice($cart->getShippingPriceWithTax());
					}
					$cartInfo['billingLabel'] = '';
					if ($cart->getBillingLabel())
					{
						$cartInfo['billingLabel'] = $cart->getBillingLabel();
					}
					
					$cartInfo['couponLabel'] = '';
					
					$cartInfo['totalWithoutTax'] = $shop->formatPrice($cart->getTotalWithoutTax());
					$cartInfo['tvaAmount'] = $shop->formatPrice($cart->getTotalTax());
					$cartInfo['totalWithTax'] = $shop->formatPrice($cart->getTotalWithTax());
					
					// Lines.
					$cartInfo['lines'] = array();
					foreach ($cart->getCartLineArray() as $line)
					{
						$lineInfo = $this->getLineInfo($line, 'cart-line', $shop);
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
	 * @param catalog_persistentdocument_shop $shop
	 * @return Array<String => String>
	 */
	private function getLineInfo($line, $type, $shop)
	{
		$lineInfo = array();
		$lineInfo['linetype'] = $type;
		$product = $line->getProduct();
		if ($product === null)
		{
			$lineInfo['productLabel'] = f_Locale::translateUI('&module.customer.bo.doceditor.panel.carts.Unexisting-product;');
			$lineInfo['availability'] = '';
		}
		else
		{
			$lineInfo['productLabel'] = $product->getLabel();
			$lineInfo['availability'] = $product->getAvailability();
		}

		$lineInfo['unitPriceWithoutTax'] = $shop->formatPrice($line->getValueWithoutTax());
		$lineInfo['unitPriceWithTax'] = $shop->formatPrice($line->getValueWithTax());
		$lineInfo['quantity'] = $line->getQuantity();
		$lineInfo['totalPriceWithoutTax'] = $shop->formatPrice($line->getTotalValueWithoutTax());
		$lineInfo['totalPriceWithTax'] = $shop->formatPrice($line->getTotalValueWithTax());
		
		return $lineInfo;
	}
}
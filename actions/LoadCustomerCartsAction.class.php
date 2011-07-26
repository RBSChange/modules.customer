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
		$result = array();
		
		if (ModuleService::getInstance()->moduleExists('order'))
		{
			$ls = LocaleService::getInstance();
			$cart = $customer->getCart();
			if ($cart && $cart->getCartLineCount() != 0)
			{
				try
				{
					$shop = $cart->getShop();
					$cartInfo = array();
					
					// Global infos.
					$cartInfo['label'] = $ls->transBO('m.customer.bo.doceditor.panel.carts.title', array('ucf'), array('shop' => $shop->getLabel()));
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

		$lineInfo['unitPriceWithoutTax'] = $shop->formatPrice($line->getValueWithoutTax());
		$lineInfo['unitPriceWithTax'] = $shop->formatPrice($line->getValueWithTax());
		$lineInfo['quantity'] = $line->getQuantity();
		$lineInfo['totalPriceWithoutTax'] = $shop->formatPrice($line->getTotalValueWithoutTax());
		$lineInfo['totalPriceWithTax'] = $shop->formatPrice($line->getTotalValueWithTax());
		
		return $lineInfo;
	}
}
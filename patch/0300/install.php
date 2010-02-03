<?php
/**
 * @author intportg
 * @package modules.order
 */
class customer_patch_0300 extends patch_BasePatch
{
	/**
	 * Entry point of the patch execution.
	 */
	public function execute()
	{
		parent::execute();
		
		$customers = customer_CustomerService::getInstance()->createQuery()->find();
		foreach ($customers as $customer)
		{
			$cart = $customer->getCartSerialized();
			if ($cart !== null)
			{
				// intportg & intclail : delete serialize cart because catalog_PriceInfo not exists
				
//				$cart = unserialize($cart);
//				if ($cart instanceof order_CartInfo)
//				{
//					$carts = array($cart->getCatalogId() => $cart);
//					$customer->setCartSerialized(serialize($carts));
//				}
//				else if (!is_array($cart))
//				{
					$customer->setCartSerialized(null);
//				}
//				else 
//				{
//					continue;
//				}
				$customer->save();
			}
		}
	}

	/**
	 * Returns the name of the module the patch belongs to.
	 *
	 * @return String
	 */
	protected final function getModuleName()
	{
		return 'customer';
	}

	/**
	 * Returns the number of the current patch.
	 * @return String
	 */
	protected final function getNumber()
	{
		return '0300';
	}

}
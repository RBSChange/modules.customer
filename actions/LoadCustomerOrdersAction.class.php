<?php
/**
 * customer_LoadCustomerOrdersAction
 * @package modules.customer.actions
 */
class customer_LoadCustomerOrdersAction extends f_action_BaseJSONAction
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
			$orders = order_OrderService::getInstance()->getByCustomer($customer);
			foreach ($orders as $order)
			{	
				/* @var $order order_persistentdocument_order */
				$orderInfo = array();
				$orderInfo['id'] = $order->getId();			
				$orderInfo['label'] = f_Locale::translateUI('&modules.customer.bo.general.Order-title;', array('number' => $order->getId()));			
				$orderInfo['ordernumber'] = $order->getOrderNumber();			
				$orderInfo['creationdate'] = date_DateFormat::format($order->getUICreationdate(), $dateTimeFormat);			
				$orderInfo['statusClass'] = str_replace('_', '-', strtolower($order->getOrderStatus()));
				$orderInfo['status'] = $order->getBoOrderStatusLabel();
							
				$pf = catalog_PriceFormatter::getInstance();	
				$orderInfo['totalamountwithtax'] = $pf->applyFormat($order->getTotalAmountWithTax(), $order->getPriceFormat(), $order->getCurrencyCode());
				$orderInfo['totalamountwithouttax'] = $pf->applyFormat($order->getTotalAmountWithoutTax(), $order->getPriceFormat(), $order->getCurrencyCode());
				$orderInfo['commentadmin'] = $order->getCommentadmin();			
				$result[] = $orderInfo;
			}
		}
		
		return $this->sendJSON($result);
	}
}
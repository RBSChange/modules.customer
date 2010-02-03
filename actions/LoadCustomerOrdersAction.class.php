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
				$orderInfo = array();
				$orderInfo['id'] = $order->getId();			
				$orderInfo['label'] = f_Locale::translateUI('&modules.customer.bo.general.Order-title;', array('number' => $order->getId()));			
				$orderInfo['ordernumber'] = $order->getOrderNumber();			
				$orderInfo['creationdate'] = date_DateFormat::format($order->getUICreationdate(), $dateTimeFormat);			
				$orderInfo['statusClass'] = str_replace('_', '-', strtolower($order->getOrderStatus()));
				$orderInfo['status'] = $order->getBoOrderStatusLabel();
				$orderInfo['totalamountwithtax'] = catalog_PriceHelper::applyFormat($order->getTotalAmountWithTax(), $order->getPriceFormat());
				$orderInfo['totalamountwithouttax'] = catalog_PriceHelper::applyFormat($order->getTotalAmountWithoutTax(), $order->getPriceFormat());
				$orderInfo['commentadmin'] = $order->getCommentadminAsHtml();			
				$result[] = $orderInfo;
			}
		}
		
		return $this->sendJSON($result);
	}
}
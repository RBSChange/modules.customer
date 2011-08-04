<?php
/**
 * customer_LoadCustomerOrdersAction
 * @package modules.customer.actions
 */
class customer_LoadCustomerOrdersAction extends change_JSONAction
{
	/**
	 * @param change_Context $context
	 * @param change_Request $request
	 */
	public function _execute($context, $request)
	{
		$customer = $this->getDocumentInstanceFromRequest($request);
		$result = array();
		
		if (ModuleService::getInstance()->moduleExists('order'))
		{
			$ls = LocaleService::getInstance();
			$orders = order_OrderService::getInstance()->getByCustomer($customer);
			foreach ($orders as $order)
			{	
				$orderInfo = array();
				$orderInfo['id'] = $order->getId();			
				$orderInfo['label'] = $ls->transBO('m.customer.bo.general.order-title', array('ucf'), array('number' => $order->getId()));			
				$orderInfo['ordernumber'] = $order->getOrderNumber();			
				$orderInfo['creationdate'] = date_Formatter::toDefaultDateTimeBO($order->getUICreationdate());			
				$orderInfo['statusClass'] = str_replace('_', '-', strtolower($order->getOrderStatus()));
				$orderInfo['status'] = $order->getBoOrderStatusLabel();
				$orderInfo['totalamountwithtax'] = catalog_PriceHelper::applyFormat($order->getTotalAmountWithTax(), $order->getPriceFormat());
				$orderInfo['totalamountwithouttax'] = catalog_PriceHelper::applyFormat($order->getTotalAmountWithoutTax(), $order->getPriceFormat());
				$orderInfo['commentadmin'] = $order->getCommentadmin();			
				$result[] = $orderInfo;
			}
		}
		
		return $this->sendJSON($result);
	}
}
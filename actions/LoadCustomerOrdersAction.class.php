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
			$orders = order_OrderService::getInstance()->getByCustomer($customer);
			foreach ($orders as $order)
			{
				/* @var $order order_persistentdocument_order */
				$orderInfo = array();
				$orderInfo['id'] = $order->getId();
				$orderInfo['label'] = LocaleService::getInstance()->trans('m.customer.bo.general.order-title', array('ucf'), array('number' => $order->getId()));			
				$orderInfo['ordernumber'] = $order->getOrderNumber();			
				$orderInfo['creationdate'] = date_Formatter::toDefaultDateTimeBO($order->getUICreationdate());			
				$orderInfo['statusClass'] = str_replace('_', '-', strtolower($order->getOrderStatus()));
				$orderInfo['status'] = $order->getBoOrderStatusLabel();
				
				$pf = catalog_PriceFormatter::getInstance();	
				$orderInfo['totalamountwithtax'] = $order->formatPrice($order->getTotalAmountWithTax());
				$orderInfo['totalamountwithouttax'] = $order->formatPrice($order->getTotalAmountWithoutTax());
				$orderInfo['commentadmin'] = $order->getCommentadmin();			
				$result[] = $orderInfo;
			}
		}
		
		return $this->sendJSON($result);
	}
}
<?php
class customer_LastOrderCustomerFilter extends customer_OrderFilterBase
{
	public function __construct()
	{
		$parameter = f_persistentdocument_DocumentFilterRestrictionParameter::getNewInstance();
		$parameter->setAllowedPropertyNames(array(
			'modules_order/order.creationdate',
			'modules_order/order.currencyCode',
			'modules_order/order.totalAmountWithoutTax',
			'modules_order/order.totalAmountWithTax',
			'modules_order/order.orderStatus'
		));		
		$this->setParameter('field', $parameter);
	}
	
	/**
	 * @return string
	 */
	public static function getDocumentModelName()
	{
		return 'modules_customer/customer';
	}

	/**
	 * @return f_persistentdocument_criteria_Query
	 */
	public function getQuery()
	{
		$query = customer_CustomerService::getInstance()->createQuery();
		$subQuery = $query->createPropertyCriteria('lastOrderId', 'modules_order/order');
		$subQuery->add($this->getParameter('field')->getValueForQuery());
		return $query;
	}
}
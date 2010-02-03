<?php
class customer_OrderFilter extends customer_OrderFilterBase
{
	public function __construct()
	{
		$parameters = array();
		
		$info = new BeanPropertyInfoImpl('count', 'Integer');
		$parameter = f_persistentdocument_DocumentFilterRestrictionParameter::getNewHavingInstance($info);
		$parameters['count'] = $parameter;
		
		$info = new BeanPropertyInfoImpl('mode', 'String');
		$info->setLabelKey('status des commandes');
		$info->setListId('modules_order/paymentstatuscategories');
		$parameter = new f_persistentdocument_DocumentFilterValueParameter($info);
		$parameters['status'] = $parameter;
		
		$parameter = f_persistentdocument_DocumentFilterRestrictionParameter::getNewInstance();
		$parameter->setAllowedPropertyNames(array(
			'modules_order/order.creationdate',
			'modules_order/order.currencyCode',
			'modules_order/order.totalAmountWithoutTax',
			'modules_order/order.totalAmountWithTax',
			'modules_order/order.orderStatus'
		));		
		$parameters['field'] = $parameter;
		
		$this->setParameters($parameters);
	}
	
	/**
	 * @return String
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
		$query = customer_CustomerService::getInstance()->createQuery()->setFetchColumn('this');
		$subQuery = $query->createCriteria('order');
		$subQuery->add($this->getParameter('field')->getValueForQuery());
		$subQuery->setProjection(Projections::rowCount('count'));
		$this->addStatusRestiction($subQuery, $this->getParameter('status')->getValue());
		$query->having($this->getParameter('count')->getValueForQuery());
		return $query;
	}
}
?>
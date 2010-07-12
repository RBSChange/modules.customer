<?php
class customer_OrderAmountAverageFilter extends customer_OrderFilterBase
{
	public function __construct()
	{
		$parameters = array();
		
		$info = new BeanPropertyInfoImpl('status', 'String');
		$info->setLabelKey('status des commandes');
		$info->setListId('modules_order/paymentstatuscategories');
		$parameter = new f_persistentdocument_DocumentFilterValueParameter($info);
		$parameters['status'] = $parameter;
		
		$parameter = f_persistentdocument_DocumentFilterRestrictionParameter::getNewInstance();
		$parameter->setAllowedPropertyNames(array(
			'modules_order/order.totalAmountWithoutTax',
			'modules_order/order.totalAmountWithTax'
		));		
		$parameters['average'] = $parameter;
		
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
		$query = customer_CustomerService::getInstance()->createQuery();
		$subQuery = $query->createCriteria('order');
		$subQuery->setProjection(Projections::avg($this->getParameter('average')->getPropertyInfo()->getName(), 'average'));
		$this->addStatusRestiction($subQuery, $this->getParameter('status')->getValue());
		$query->having($this->getAverageRestrictionForQuery());
		return $query;
	}
	
	/**
	 * @return f_persistentdocument_criteria_Criterion
	 */
	private function getAverageRestrictionForQuery()
	{
		$param = $this->getParameter('average');
		$param->validate(true);
		$arguments = array('average', $param->getParameter()->getValueForQuery());
		return f_util_ClassUtils::callMethodArgs('HavingRestrictions', $param->getRestriction(), $arguments);
	}
}
<?php
class customer_OrderCountCustomerFilter extends customer_OrderFilterBase
{
	public function __construct()
	{
		$info = new BeanPropertyInfoImpl('count', 'Integer');
		$parameter = f_persistentdocument_DocumentFilterRestrictionParameter::getNewHavingInstance($info);
		$this->setParameter('count', $parameter);
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
		$subQuery = $query->createLeftCriteria('paidByCustomerId', 'modules_order/bill');
		$subQuery->setProjection(Projections::distinctCount('order', 'count'));
		$query->having($this->getParameter('count')->getValueForQuery());
		return $query;
	}
}
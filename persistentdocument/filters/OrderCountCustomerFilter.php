<?php
class customer_OrderCountCustomerFilter extends customer_OrderFilterBase
{
	public function __construct()
	{
		$info = new BeanPropertyInfoImpl('count', 'Integer');
		$parameter = f_persistentdocument_DocumentFilterRestrictionParameter::getNewHavingInstance($info);
		$this->setParameter('count', $parameter);

		$info = new BeanPropertyInfoImpl('status', 'String');
		$info->setListId('modules_order/statusesforordercountfilter');
		$parameter = new f_persistentdocument_DocumentFilterValueParameter($info);
		$this->setParameter('status', $parameter);
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
		switch ($this->getParameter('status')->getValueForQuery())
		{
			case 'paid':
				$subQuery = $query->createLeftCriteria('paidByCustomerId', 'modules_order/bill');
				$subQuery->setProjection(Projections::distinctCount('order', 'count'));
				$query->having($this->getParameter('count')->getValueForQuery());
				break;
			
			case 'uncancelled':
				$subQuery = $query->createLeftCriteria('customer', 'modules_order/order');
				$subQuery->add(Restrictions::orExp(Restrictions::ne('orderStatus', order_OrderService::CANCELED), Restrictions::isNull('orderStatus')));
				$subQuery->setProjection(Projections::rowCount('count'));
				$query->having($this->getParameter('count')->getValueForQuery());
				break;
			
			case 'all':
				$subQuery = $query->createLeftCriteria('customer', 'modules_order/order');
				$subQuery->setProjection(Projections::rowCount('count'));
				$query->having($this->getParameter('count')->getValueForQuery());
				break;
		}
		return $query;
	}
}
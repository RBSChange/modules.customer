<?php
class customer_OrderCountCustomerFilter extends customer_OrderFilterBase
{
	public function __construct()
	{
		$info = new BeanPropertyInfoImpl('count', 'Integer');
		$info->addConstraint('min', 1);
		$parameter = f_persistentdocument_DocumentFilterRestrictionParameter::getNewHavingInstance($info);
		$parameter->setAllowedRestrictions('count', array('eq', 'ge', 'gt'));
		$this->setParameter('count', $parameter);

		$info = new BeanPropertyInfoImpl('status', 'String');
		$info->setListId('modules_order/statusesforordercountfilter');
		$parameter = new f_persistentdocument_DocumentFilterValueParameter($info);
		$this->setParameter('status', $parameter);
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
		switch ($this->getParameter('status')->getValueForQuery())
		{
			case 'paid':
				$subQuery = $query->createPropertyCriteria('paidByCustomerId', 'modules_order/bill');
				$subQuery->setProjection(Projections::distinctCount('order', 'count'));
				$query->having($this->getParameter('count')->getValueForQuery());
				break;
			case 'uncancelled':
				$subQuery = $query->createCriteria('order');
				$subQuery->add(Restrictions::ne('orderStatus', order_OrderService::CANCELED));
				$subQuery->setProjection(Projections::rowCount('count'));
				$query->having($this->getParameter('count')->getValueForQuery());
				break;			
			case 'all':
				$subQuery = $query->createCriteria('order');
				$subQuery->setProjection(Projections::rowCount('count'));
				$query->having($this->getParameter('count')->getValueForQuery());
				break;
		}
		return $query;
	}
}
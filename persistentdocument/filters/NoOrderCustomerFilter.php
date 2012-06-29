<?php
class customer_NoOrderCustomerFilter extends customer_OrderFilterBase
{
	public function __construct()
	{
		$info = new BeanPropertyInfoImpl('status', 'String');
		$info->setListId('modules_customer/noordercustomertype');
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
			case 'nopaidorder':
				$subQuery = $query->createLeftCriteria('paidByCustomerId', 'modules_order/bill');
				$subQuery->add(Restrictions::isNull('id'));
				break;	
			case 'noorder':
				$subQuery = $query->createLeftCriteria('order');
				$subQuery->add(Restrictions::isNull('id'));
				break;
		}
		return $query;
	}
}
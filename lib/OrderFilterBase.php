<?php
abstract class customer_OrderFilterBase extends f_persistentdocument_DocumentFilterImpl
{
	/**
	 * @param f_persistentdocument_criteria_Query $query
	 * @param string $statusValue
	 * @return f_persistentdocument_criteria_Query
	 */
	protected function addStatusRestiction($query, $statusValue)
	{
		switch ($statusValue)
		{
			case 'paid' :
				$query->createCriteria('bill')
					->add(Restrictions::published())
					->add(Restrictions::eq('status', order_BillService::SUCCESS));
				break;
			case 'paid-or-waiting' :
				$query->createCriteria('bill')
					->add(Restrictions::published())
					->add(Restrictions::in('status', array(order_BillService::SUCCESS, order_BillService::WAITING)));
				break;
			
			case 'all' :
			default :
				// All statuses.
				break;
		}
		return $query;
	}
	
	/**
	 * @param customer_persistentdocument_customer $value
	 */
	public function checkValue($value)
	{
		if ($value instanceof customer_persistentdocument_customer)
		{
			$query = $this->getQuery()->add(Restrictions::eq('id', $value->getId()));
			if ($query->findUnique() !== null)
			{
				return true;
			}
		}
		return false;
	}
}
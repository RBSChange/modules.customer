<?php
abstract class customer_OrderFilterBase extends f_persistentdocument_DocumentFilterImpl
{
	/**
	 * @return f_persistentdocument_criteria_Query
	 */
	protected function addStatusRestiction($query, $statusValue)
	{
		switch ($statusValue)
		{
			case 'paid' :
				$query->add(Restrictions::orExp(
					Restrictions::eq('orderStatus', order_OrderService::PAYMENT_SUCCESS),
					Restrictions::eq('orderStatus', order_OrderService::SHIPPED)
				));
				break;
				
			case 'paid-or-waiting' :
				$query->add(Restrictions::orExp(
					Restrictions::eq('orderStatus', order_OrderService::PAYMENT_SUCCESS),
					Restrictions::eq('orderStatus', order_OrderService::SHIPPED),
					Restrictions::eq('orderStatus', order_OrderService::PAYMENT_WAITING),
					Restrictions::eq('orderStatus', order_OrderService::PAYMENT_DELAYED)
				));
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
?>
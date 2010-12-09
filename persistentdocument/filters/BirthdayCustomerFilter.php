<?php
class customer_BirthdayCustomerFilter extends f_persistentdocument_DocumentFilterImpl
{
	public function __construct()
	{
		$parameters = array();
		
		$info = new BeanPropertyInfoImpl('count', 'Integer');
		$countParameter = new f_persistentdocument_DocumentFilterValueParameter($info);
		$parameters['count'] = $countParameter;
		
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
		$count = $this->getParameter('count')->getValueForQuery();
		$todayPlusCount = date_Calendar::getInstance()->now();
		$todayPlusCount->add(date_Calendar::DAY,$count);
		$today = date_Calendar::getInstance()->now();
		$todayDayNumber = ($today->getMonth()*31 + $today->getDay());
		$todayPlusCountDayNumber = ($todayPlusCount->getMonth()*31 + $todayPlusCount->getDay());
		
		
		if (($todayDayNumber + $count) > 403)
		{
			return customer_CustomerService::getInstance()->createQuery()->add(Restrictions::orExp(
				Restrictions::ge('birthdayDayNumber', $todayDayNumber), 
				Restrictions::le('birthdayDayNumber', $todayPlusCountDayNumber)
			));
		}
		else 
		{
			return customer_CustomerService::getInstance()->createQuery()->add(Restrictions::between('birthdayDayNumber', $todayDayNumber, $todayPlusCountDayNumber));	
		}
		
	}
	
	/**
	 * @param customer_persistentdocument_customer $value
	 */
	public function checkValue($value)
	{	
		if ($value instanceof customer_persistentdocument_customer)
		{
			$birthdayDayNumber = $value->getBirthdayDayNumber();
			if ($birthdayDayNumber !== null)
			{
				$count = $this->getParameter('count');
				$todayPlusCount = date_Calendar::getInstance()->now();
				$todayPlusCount->add(date_Calendar::DAY,$count);
				$today = date_Calendar::getInstance()->now();
				$todayDayNumber = ($today->getMonth()*31 + $today->getDay());
				$todayPlusCountDayNumber = ($todayPlusCount->getMonth()*31 + $todayPlusCount->getDay()); 
				
				if (($todayDayNumber + $count) > 403)
				{
					if ((($birthdayDayNumber >= $todayDayNumber) && ($birthdayDayNumber <= 403)) ||	(($birthdayDayNumber >= 32) && ($birthdayDayNumber <= $todayPlusCountDayNumber)))
					{
						return true;
					}
				}
				else if (($birthdayDayNumber >= $todayDayNumber) && ($birthdayDayNumber < $todayPlusCountDayNumber))
				{
					return true;
				}
			}
		}
		return false;
	}
}
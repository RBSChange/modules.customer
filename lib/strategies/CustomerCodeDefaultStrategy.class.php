<?php
class customer_CustomerCodeDefaultStrategy implements customer_CustomerCodeStrategy
{
	/**
	 * @param customer_persistentdocument_customer $customer
	 * @return String
	 */
	public function generate($customer)
	{
		return str_pad($customer->getId(), 13, '0', STR_PAD_LEFT);
	}
}
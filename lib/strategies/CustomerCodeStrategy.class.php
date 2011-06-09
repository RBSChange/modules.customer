<?php
interface customer_CustomerCodeStrategy
{
	/**
	 * @param customer_persistentdocument_customer $customer
	 * @return String
	 */
	public function generate($customer);
}
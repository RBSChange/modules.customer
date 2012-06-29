<?php
interface customer_CustomerCodeStrategy
{
	/**
	 * @param customer_persistentdocument_customer $customer
	 * @return string
	 */
	public function generate($customer);
}
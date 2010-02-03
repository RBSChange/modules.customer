<?php
interface customer_CustomerCodeStrategy
{
	/**
	 * @param customer_persistentdocument_customer $order
	 * @return String
	 */
	public function generate($order);
}
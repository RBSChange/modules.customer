<?php
/**
 * @package modules.customer.lib.bean
 * @constraints(propEq:password,passwordconfirm)
 */
class customer_CustomerWrapperBean
{
	/**
	 * @var customer_persistentdocument_customer
	 */	
	public $customer;
	
	/**
	 * @var String
	 */	
	public $password;
	
	/**
	 * @var String
	 */	
	public $passwordconfirm;
	
	/**
	 * @var String
	 */
	public $emailconfirm;
	
	/**
	 * @var Integer
	 */
	public $id;
	
	/**
	 * @param Integer $userId
	 * @return customer_CustomerWrapperBean
	 */
	public static function getInstanceById($userId)
	{
		$customerWrapper = new self();
		$customerWrapper->id = $userId;
		$user = DocumentHelper::getDocumentInstance($userId, 'modules_users/user');
		$cs = customer_CustomerService::getInstance();
		if (($customer = $cs->getByUser($user)) === null)
		{
			$customer = $cs->getNewDocumentInstance();
			$customer->setUser($user);
		}
		$customerWrapper->customer = $customer;		
		return $customerWrapper;
	}
}
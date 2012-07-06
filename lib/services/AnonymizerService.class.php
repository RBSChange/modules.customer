<?php
/**
 * @package mdoules.customer
 * @method customer_AnonymizerService getInstance()
 */
class customer_AnonymizerService extends change_BaseService
{
	/**
	 * 
	 * @param customer_persistentdocument_customer $customer
	 * @return boolean
	 */
	public function isAnonymized($customer)
	{
		return $customer->getPublicationstatus() == 'FILED';
	}
	
	/**
	 * 
	 * @param customer_persistentdocument_customer $customer
	 * @return boolean
	 */
	public function canBeAnonymized($customer)
	{
		$rows = order_OrderService::getInstance()->createQuery()->add(Restrictions::eq('customer', $customer))
			->add(Restrictions::notin('orderStatus', array(order_OrderService::CANCELED, order_OrderService::COMPLETE)))
			->setProjection(Projections::rowCount('count'))->findColumn('count');
		return $rows[0] == 0;
	}
	
	/**
	 * @param customer_persistentdocument_customer $customer
	 */
	public function anonymizeCustomer($customer)
	{
		$this->anonymizeUser($customer->getUser());
		foreach ($customer->getAddressArray() as $address)
		{
			$this->anonymizeAddress($address);
		}
		foreach ($customer->getOrderArrayInverse() as $order)
		{
			$this->anonymizeAddress($order->getBillingAddress());
			$this->anonymizeAddress($order->getShippingAddress());
		}
		
		$customer->setLabel('Anonymous');
		$customer->save();
		
		foreach ($customer->getEditablecustomergroupArrayInverse() as $group)
		{
			$group->removeMember($customer);
			$group->save();
		}
		
		$customer->getDocumentService()->file($customer->getId());
	}
	
	/**
	 * @param users_persistentdocument_user $user
	 */
	protected function anonymizeUser($user)
	{
		$user->getDocumentService()->anonymize($user);
	}
	
	/**
	 * @param customer_persistentdocument_address $address
	 */
	protected function anonymizeAddress($address)
	{
		$address->setFirstname('Anonymous');
		$address->setLastname('Anonymous');
		$address->setEmail(Framework::getConfigurationValue('modules/users/anonymousEmailAddress'));
		$address->setAddressLine1('Anonymous');
		$address->setAddressLine2('Anonymous');
		$address->setAddressLine3('Anonymous');
		$address->setPhone('0000000000');
		$address->setFax('0000000000');
		$address->save();
	}
}
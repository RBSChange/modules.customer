<?php
/**
 * @author intportg
 * @package modules.customer
 */
class customer_MailinglistGroupFeederService extends emailing_MailinglistFeederBaseService
{
	/**
	 * @var customer_MailinglistGroupFeederService
	 */
	private static $instance;

	/**
	 * @return customer_MailinglistGroupFeederService
	 */
	public static function getInstance()
	{
		if (self::$instance === null)
		{
			self::$instance = self::getServiceClassInstance(get_class());
		}
		return self::$instance;
	}	

	/**
	 * @param emailing_persistentdocument_dynamicmailinglist $list
	 */
	public function getRelatedIds($list)
	{
		$groupId = $list->getParameter('groupId');
		if ($groupId !== null)
		{
			$group = DocumentHelper::getDocumentInstance($groupId);
			return $group->getDocumentService()->getMemberIds($group);
		}
		return array();
	}
	
	/**
	 * @param Integer $id
	 * @param emailing_persistentdocument_dynamicmailinglist $list
	 */
	public function refreshSubscriber($id, $list)
	{
		$fields = $this->getFields($list);
		$customer = DocumentHelper::getDocumentInstance($id);
		$billingAddress = $customer->getBillingAddress();
		$email = $billingAddress->getEmail();
		$subscriber = $this->getSubscriber($customer->getId(), $email, $list);				
		$subscriber->setEmail($email);
		$subscriber->setRelatedDocument($customer);
		$subscriber->setExtendFieldValue($fields['firstname'], $billingAddress->getFirstname());
		$subscriber->setExtendFieldValue($fields['lastname'], $billingAddress->getLastname());
		if ($billingAddress->getTitle() !== null)
		{
			$subscriber->setExtendFieldValue($fields['title'], $billingAddress->getTitle()->getLabel());
		}
		$subscriber->setDisable(false);
		$subscriber->save();
		$subscriber->activate();
	}
}
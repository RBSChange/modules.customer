<?php
/**
 * @author intportg
 * @package modules.customer
 */
class customer_CreateGroupMailinglistAction extends f_action_BaseAction
{
	/**
	 * @param Context $context
	 * @param Request $request
	 */
	public function _execute($context, $request)
	{
		$group = $this->getDocumentInstanceFromRequest($request);
		
		$dmls = emailing_DynamicmailinglistService::getInstance();
		$list = $dmls->getNewDocumentInstance();
		$list->setLabel($group->getLabel());
		$list->setClassName('customer_MailinglistGroupFeederService');
		$list->setParameter('groupId', $group->getId());
		
		$srof = emailing_SubscriberrofieldService::getInstance();
		$field = $srof->getNewDocumentInstance();
		$field->setMailingname('firstname');
		$field->setLabel('Prénom');
		$list->addSubscriberfields($field);
		
		$field = $srof->getNewDocumentInstance();
		$field->setMailingname('lastname');
		$field->setLabel('Nom');
		$list->addSubscriberfields($field);
		
		$field = $srof->getNewDocumentInstance();
		$field->setMailingname('title');
		$field->setLabel('Civilité');
		$list->addSubscriberfields($field);
		
		$systemFolderId = ModuleService::getInstance()->getSystemFolderId('emailing', 'customer');		
		$list->save($systemFolderId);
		
		return self::getSuccessView();
	}
}
<?php
/**
 * customer_patch_0303
 * @package modules.customer
 */
class customer_patch_0303 extends patch_BasePatch
{
	/**
	 * Entry point of the patch execution.
	 */
	public function execute()
	{

		$pp = $this->getPersistentProvider();
		$cs = customer_CustomerService::getInstance();
		
		// Delete obsolete forms.
		$service = form_FormService::getInstance();
		$ids = array('modules_customer/changepassword', 'modules_customer/passwordlost',
					'modules_customer/createaccount', 'modules_customer/editaccount',
					'modules_customer/editaddress');
		foreach ($ids as $id)
		{
			$document = $service->getByFormId($id);
			if ($document !== null)
			{
				$pp->createQuery()->add(Restrictions::descendentOf($document->getId()))->delete();
				TreeService::getInstance()->setTreeNodeCache(false)->setTreeNodeCache(true);
				$document->delete();
			}
		}
		
		
		// Delete obsolete notifications.
		$service = notification_NotificationService::getInstance();
		$ids = array('modules_customer/lostpassword', 'modules_customer/changepassword', 'modules_customer/newsletterSubscription');
		foreach ($ids as $id)
		{
			$service->createQuery()->add(Restrictions::like('codename', $id.'/', MatchMode::START()))->delete();
			$service->createQuery()->add(Restrictions::eq('codename', $id))->delete();
		}
		
		// Delete obsolete lists.
		$service = list_ListService::getInstance();
		$ids = array('modules_customer/title');
		foreach ($ids as $id)
		{
			$document = $service->getDocumentInstanceByListId($id);
			if ($document !== null)
			{
				$document->delete();
			}
		}
		
		// Migrate addresses.
		$this->executeSQLQuery("ALTER TABLE m_customer_doc_address DROP COLUMN isdefault;");
		
		$this->executeSQLQuery("UPDATE m_customer_doc_address SET document_model = 'modules_customer/address' WHERE document_model = 'modules_customer/billingaddress';");
		$this->executeSQLQuery("UPDATE f_document SET document_model = 'modules_customer/address' WHERE document_model = 'modules_customer/billingaddress';");
		$this->executeSQLQuery("UPDATE f_relation SET document_model_id1 = 'modules_customer/address' WHERE document_model_id1 = 'modules_customer/billingaddress';");
		$this->executeSQLQuery("UPDATE f_relation SET document_model_id2 = 'modules_customer/address' WHERE document_model_id2 = 'modules_customer/billingaddress';");
		
		$this->executeSQLQuery("UPDATE m_customer_doc_address SET document_model = 'modules_customer/address' WHERE document_model = 'modules_customer/shippingaddress';");
		$this->executeSQLQuery("UPDATE f_document SET document_model = 'modules_customer/address' WHERE document_model = 'modules_customer/shippingaddress';");
		$this->executeSQLQuery("UPDATE f_relation SET document_model_id1 = 'modules_customer/address' WHERE document_model_id1 = 'modules_customer/shippingaddress';");
		$this->executeSQLQuery("UPDATE f_relation SET document_model_id2 = 'modules_customer/address' WHERE document_model_id2 = 'modules_customer/shippingaddress';");
		
		// Migrate customers.
		
		$this->executeSQLQuery("ALTER TABLE `m_customer_doc_customer` CHANGE `cartserialized` `cartserialized` MEDIUMTEXT CHARACTER SET utf8 COLLATE utf8_bin NULL DEFAULT NULL");
		$this->executeSQLQuery("ALTER TABLE `m_customer_doc_customer` ADD `synchroid` INT( 11 ) NULL");

		$this->executeSQLQuery("ALTER TABLE m_customer_doc_customer ADD address int(11) default '0';");
		$this->executeSQLQuery("ALTER TABLE m_customer_doc_customer DROP COLUMN synchrostatus;");
		$this->executeSQLQuery("ALTER TABLE m_customer_doc_customer DROP COLUMN code;");
		$this->executeSQLQuery("ALTER TABLE m_customer_doc_customer DROP COLUMN billingaddress;");
		$this->executeSQLQuery("ALTER TABLE m_customer_doc_customer DROP COLUMN shippingaddress;");
		foreach ($cs->createQuery()->find() as $customer)
		{
			//$relations = $pp->getChildRelationByMasterDocumentId($customer->getId(), 'billingAddress');
			$allBillingAddressRelations = $pp->getChildRelationByMasterDocumentId($customer->getId());
			foreach ($allBillingAddressRelations as $relation)
			{
				if($relation->getName() == 'billingAddress')
				{
					$customer->addAddress(DocumentHelper::getDocumentInstance($relation->getDocumentId2()));
				}
			}			
			//$relations = $pp->getChildRelationByMasterDocumentId($customer->getId(), 'shippingAddress');
			$allShippingAddressRelations = $pp->getChildRelationByMasterDocumentId($customer->getId());
			foreach ($allShippingAddressRelations as $relation)
			{
				if($relation->getName() == 'shippingAddress')
				{
					$customer->addAddress(DocumentHelper::getDocumentInstance($relation->getDocumentId2()));
				}
			}
			$customer->save();
		}		
		$this->executeSQLQuery("DELETE FROM f_relation WHERE relation_name = 'billingAddress' AND document_model_id1 = 'modules_customer/customer';");
		$this->executeSQLQuery("DELETE FROM f_relation WHERE relation_name = 'shippingAddress' AND document_model_id1 = 'modules_customer/customer';");
		// Replace tags.
		$ts = TagService::getInstance();
		foreach ($ts->getDocumentsByTag('contextual_website_website_modules_customer_my-account-address') as $document)
		{
			$ts->removeTag($document, 'contextual_website_website_modules_customer_my-account-address');
			$ts->addTag($document, 'contextual_website_website_modules_customer_manageaddress');
		}
	}

	/**
	 * Returns the name of the module the patch belongs to.
	 *
	 * @return String
	 */
	protected final function getModuleName()
	{
		return 'customer';
	}

	/**
	 * Returns the number of the current patch.
	 * @return String
	 */
	protected final function getNumber()
	{
		return '0303';
	}
}
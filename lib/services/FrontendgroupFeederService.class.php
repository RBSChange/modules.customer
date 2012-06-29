<?php
/**
 * @package mdoules.customer
 * @method customer_GroupFeederService getInstance()
 */
class customer_GroupFeederService extends users_GroupFeederBaseService
{
	/**
	 * @param users_persistentdocument_dynamicgroup $group
	 */
	public function getUserIds($group)
	{
		$docId = $group->getParameter('referenceId');
		if ($docId !== null)
		{
			$doc = DocumentHelper::getDocumentInstance($docId, 'modules_customer/customergroup');
			$customerIds = $doc->getDocumentService()->getMemberIds($doc);
			$query = users_UserService::getInstance()->createQuery()->add(Restrictions::in('customer.id', $customerIds));
			return $query->setProjection(Projections::property('id'))->findColumn('id');
		}
		return array();
	}
}
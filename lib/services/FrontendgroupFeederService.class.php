<?php
/**
 * @author intportg
 * @package modules.customer
 */
class customer_GroupFeederService extends users_GroupFeederBaseService
{
	/**
	 * @var customer_FrontendgroupFeederService
	 */
	private static $instance;

	/**
	 * @return customer_FrontendgroupFeederService
	 */
	public static function getInstance()
	{
		if (self::$instance === null)
		{
			self::$instance = new self();
		}
		return self::$instance;
	}	

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
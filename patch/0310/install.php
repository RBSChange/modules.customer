<?php
/**
 * customer_patch_0310
 * @package modules.customer
 */
class customer_patch_0310 extends patch_BasePatch
{
	/**
	 * Entry point of the patch execution.
	 */
	public function execute()
	{
		$newPath = f_util_FileUtils::buildWebeditPath('modules/customer/persistentdocument/customer.xml');
		$newModel = generator_PersistentModel::loadModelFromString(f_util_FileUtils::read($newPath), 'customer', 'customer');
		$newProp = $newModel->getPropertyByName('lastOrderId');
		f_persistentdocument_PersistentProvider::getInstance()->addProperty('customer', 'customer', $newProp);
		
		$query = order_OrderService::getInstance()->createQuery();
		$query->createCriteria('customer')->setProjection(Projections::groupProperty('id'));
		$query->setProjection(Projections::max('id', 'maxid'));
		foreach ($query->findColumn('maxid') as $orderId)
		{
			$order = DocumentHelper::getDocumentInstance($orderId, 'modules_order/order');
			$customer = $order->getCustomer();
			$customer->setLastOrderId($orderId);
			$customer->save();
		}
	}

	/**
	 * @return String
	 */
	protected final function getModuleName()
	{
		return 'customer';
	}

	/**
	 * @return String
	 */
	protected final function getNumber()
	{
		return '0310';
	}
}
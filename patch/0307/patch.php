<?php
define('WEBEDIT_HOME', realpath('.'));
require_once WEBEDIT_HOME . '/framework/Framework.php';
Controller::newInstance("controller_ChangeController");
$pp = f_persistentdocument_PersistentProvider::getInstance();
$tm = f_persistentdocument_TransactionManager::getInstance();
foreach (array_slice($_SERVER['argv'], 1) as $id)
{
	try 
	{
		$customer = DocumentHelper::getDocumentInstance($id);
		if (!$customer->hasMeta('modules.customer.lastCartUpdate'))
		{
			continue;
		}
		$tm->beginTransaction();
		$serializedCart = getCartSerialized; 
		if (f_util_StringUtils::isNotEmpty($customer->getCartSerialized()))
		{
			$customer->setLastCartUpdate($customer->getMeta('modules.customer.lastCartUpdate'));
			$pp->updateDocument($customer);
		}
		$customer->setMeta('modules.customer.lastCartUpdate', null);
		$customer->saveMeta();
		$tm->commit();
	}
	catch (Exception $e)
	{
		$tm->rollBack($e);
		Framework::exception($e);
	}
}
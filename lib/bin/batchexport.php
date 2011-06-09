<?php
$tmpFileName = $_POST['argv'][0];
$customerIds = array_slice($_POST['argv'], 1);
$tm = f_persistentdocument_TransactionManager::getInstance();

$cs = customer_CustomerService::getInstance();
$names = array_keys($cs->getExportFields());
$fp = fopen($tmpFileName, 'a');	
try
{
	$tm->beginTransaction();
	foreach ($customerIds as $customerId)
	{
		$customer = customer_persistentdocument_customer::getInstanceById($customerId);
		$values = $cs->getExportValues($customer, $names);
		fputcsv($fp, array_values($values), ';');
	}
	$tm->commit();
}
catch (Exception $e)
{
	$tm->rollBack($e);
}
fclose($fp);
echo 'OK';
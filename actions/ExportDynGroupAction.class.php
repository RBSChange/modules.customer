<?php
/**
 * customer_ExportDynGroupAction
 * @package modules.customer.actions
 */
class customer_ExportDynGroupAction extends change_Action
{
	/**
	 * @param change_Context $context
	 * @param change_Request $request
	 */
	public function _execute($context, $request)
	{
		$cs = customer_CustomerService::getInstance();
		$fieldNames = $cs->getExportFields();
		$name = array_keys($fieldNames);
		$tmpFileName = f_util_FileUtils::getTmpFile('export_customer');
		$fp = fopen($tmpFileName, 'w');
		fputcsv($fp, array_values($fieldNames), ';');
		fclose($fp);
	
		$group = customer_persistentdocument_customergroup::getInstanceById($this->getDocumentIdFromRequest($request));
		if ($group instanceof customer_persistentdocument_dynamiccustomergroup)
		{
			$queryIntersection = f_persistentdocument_DocumentFilterService::getInstance()->getQueryIntersectionFromJson($group->getQuery());
			$ids = $queryIntersection->findIds();
		}
		elseif ($group instanceof customer_persistentdocument_editablecustomergroup)
		{
			$ids = $cs->createQuery()->add(Restrictions::eq('editablecustomergroup', $group))
				->setProjection(Projections::property('id', 'id'))->findColumn('id');
		}
		else
		{
			$ids = array();
		}
		
		$fileName = "export_group_".f_util_FileUtils::cleanFilename($group->getLabel()).'_'.date('Ymd_His').'.csv';
		$batchPath = 'modules/customer/lib/bin/batchexport.php';
	
		foreach (array_chunk($ids, 250) as $chunk)
		{
			$result = f_util_System::execScript($batchPath, array_merge(array($tmpFileName), $chunk));
			if ($result !== 'OK')
			{
				Framework::error(__METHOD__ . " " . $result);
			}
		}
		clearstatcache();
		$size = filesize($tmpFileName);
		
		header("Content-type: text/comma-separated-values");
		header('Content-length: '.$size);
		header('Content-disposition: attachment; filename="'.$fileName.'"');
		readfile($tmpFileName);
		unlink($tmpFileName);
		exit;
	}
}
<?php
/**
 * @package modules.customer
 */
class customer_GetDocumentHistoryAction extends generic_GetDocumentHistoryAction
{
	/**
	 * @param f_persistentdocument_PersistentDocument $document
	 * @param users_persistentdocument_user $user
	 */
	protected function getLogs($document, $user)
	{
		$logs = parent::getLogs($document, null);
		if ($document instanceof customer_persistentdocument_customer)
		{
			$logs = $this->mergeLogs($logs, parent::getLogs(null, $document->getUser()));
		}
		return $logs;
	}
}
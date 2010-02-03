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
			$logs = array_merge($logs, parent::getLogs(null, $document->getUser()));
			usort($logs, array($this, 'compareLogEntries'));
		}
		return $logs;
	}
	
	/**
	 * @param Array $a
	 * @param Array $b
	 * @return Integer
	 */
	public function compareLogEntries($a, $b)
	{
		if ($a['entry_date'] == $b['entry_date'])
		{
			return 0; 
		}
		return (($a['entry_date'] > $b['entry_date']) ? -1 : 1);
	}
}
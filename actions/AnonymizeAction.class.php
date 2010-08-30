<?php
/**
 * customer_AnonymizeAction
 * @package modules.customer.actions
 */
class customer_AnonymizeAction extends f_action_BaseJSONAction
{
	/**
	 * @param Context $context
	 * @param Request $request
	 */
	public function _execute($context, $request)
	{
		$result = array();

		$customer = DocumentHelper::getDocumentInstance($request->getParameter('cmpref'), 'modules_customer/customer');
		$anonymizer = customer_AnonymizerService::getInstance();
		if ($anonymizer->isAnonymized($customer))
		{
			return $this->sendJSONError(f_Locale::translateUI('&modules.customer.bo.general.Already-anonymized;'));
		}
		else if (!$anonymizer->canBeAnonymized($customer))
		{
			return $this->sendJSONError(f_Locale::translateUI('&modules.customer.bo.general.Has-not-finished-order;'));
		}
		$anonymizer->anonymizeCustomer($customer);
		
		return $this->sendJSON($result);
	}
}
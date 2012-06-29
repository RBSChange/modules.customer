<?php
/**
 * customer_AnonymizeAction
 * @package modules.customer.actions
 */
class customer_AnonymizeAction extends change_JSONAction
{
	/**
	 * @param change_Context $context
	 * @param change_Request $request
	 */
	public function _execute($context, $request)
	{
		$result = array();

		$customer = DocumentHelper::getDocumentInstance($request->getParameter('cmpref'), 'modules_customer/customer');
		$anonymizer = customer_AnonymizerService::getInstance();
		if ($anonymizer->isAnonymized($customer))
		{
			return $this->sendJSONError(LocaleService::getInstance()->trans('m.customer.bo.general.already-anonymized', array('ucf')));
		}
		else if (!$anonymizer->canBeAnonymized($customer))
		{
			return $this->sendJSONError(LocaleService::getInstance()->trans('m.customer.bo.general.has-not-finished-order', array('ucf')));
		}
		$anonymizer->anonymizeCustomer($customer);
		
		return $this->sendJSON($result);
	}
}
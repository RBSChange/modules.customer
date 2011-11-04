<?php
/**
 * customer_BlockEditaccountAction
 * @package modules.customer
 */
class customer_BlockEditaccountAction extends website_BlockAction
{
	/**
	 * @see website_BlockAction::execute()
	 * @param f_mvc_Request $request
	 * @param f_mvc_Response $response
	 * @return String
	 */
	function execute($request, $response)
	{
		if ($this->isInBackofficeEdition())
		{
			return website_BlockView::NONE;
		}
		elseif($this->isInBackofficePreview())
		{
			return $this->getInputViewName();
		}
		
		$customer = customer_CustomerService::getInstance()->getCurrentCustomer();
		if ($customer === null)
		{
			HttpController::getInstance()->redirectToUrl(LinkHelper::getTagUrl('contextual_website_website_modules_customer_new-account'));
		}		
		$request->setAttribute('customer', $customer);
		return $this->getInputViewName();
	}
	
	/**
	 * @param f_mvc_Request $request
	 * @param customer_persistentdocument_customer $customer
	 * @return boolean
	 */
	function validateSaveInput($request, $customer)
	{
		$validationRules = array_merge(
			BeanUtils::getBeanValidationRules('customer_persistentdocument_customer'), 
			BeanUtils::getSubBeanValidationRules('customer_persistentdocument_customer', 'user', null, array('label', 'login', 'passwordmd5', 'websiteid'))
		);
		return $this->processValidationRules($validationRules, $request, $customer);
	}

	/**
	 * @see website_BlockAction::execute()
	 * @param f_mvc_Request $request
	 * @param f_mvc_Response $response
	 * @param customer_persistentdocument_customer $customer
	 * @return String
	 */
	function executeSave($request, $response, customer_persistentdocument_customer $customer)
	{
		$currentCustomer = customer_CustomerService::getInstance()->getCurrentCustomer();
		if (!DocumentHelper::isEquals($currentCustomer, $customer))
		{
			throw new Exception("Bad parameter");
		}
		$user = $customer->getUser();
		$user->save();
		$customer->save();
		$this->addMessage(f_Locale::translate('&modules.customer.frontoffice.Updating-success;'));
		return 'Save';
	}
}

<?php
/**
 * customer_BlockEditaccountAction
 * @package modules.customer
 */
class customer_BlockEditaccountAction extends website_BlockAction
{
	/**
	 * @param f_mvc_Request $request
	 * @param f_mvc_Response $response
	 * @return String
	 */
	public function execute($request, $response)
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
	 * @return string[]|null
	 */
	public function getCustomerBeanInclude()
	{
		if (Framework::getConfigurationValue('modules/website/useBeanPopulateStrictMode') != 'false')
		{
			return array('user.email', 'user.titleid', 'user.firstname', 'user.lastname', 'birthday');
		}
		return null;
	}

	/**
	 * @param f_mvc_Request $request
	 * @param customer_persistentdocument_customer $customer
	 * @return boolean
	 */
	public function validateSaveInput($request, $customer)
	{
		$validationRules = array_merge(
			BeanUtils::getBeanValidationRules('customer_persistentdocument_customer'), 
			BeanUtils::getSubBeanValidationRules('customer_persistentdocument_customer', 'user', null, array('label', 'login', 'passwordmd5', 'websiteid'))
		);
		return $this->processValidationRules($validationRules, $request, $customer);
	}

	/**
	 * @param f_mvc_Request $request
	 * @param f_mvc_Response $response
	 * @param customer_persistentdocument_customer $customer
	 * @throws Exception
	 * @return String
	 */
	public function executeSave($request, $response, customer_persistentdocument_customer $customer)
	{
		$currentCustomer = customer_CustomerService::getInstance()->getCurrentCustomer();
		if (!DocumentHelper::equals($currentCustomer, $customer) || $customer->isPropertyModified('user'))
		{
			throw new Exception('Bad parameter');
		}
		$user = $customer->getUser();
		$user->save();
		$customer->save();
		$this->addMessage(LocaleService::getInstance()->transFO('m.customer.frontoffice.updating-success', array('ucf')));
		return 'Save';
	}
}
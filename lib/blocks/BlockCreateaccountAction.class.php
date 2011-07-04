<?php
/**
 * customer_BlockCreateaccountAction
 * @package modules.customer
 */
class customer_BlockCreateaccountAction extends website_BlockAction
{
	/**
	 * @param f_mvc_Request $request
	 * @param f_mvc_Response $response
	 * @return String
	 */
	public function execute($request, $response)
	{
		// If there is already a customer, redirect to account.
		if ($this->isInBackofficeEdition())
		{
			return website_BlockView::NONE;
		}
		elseif ($this->isInBackofficePreview()) 
		{
			return $this->getInputViewName();
		}
		$customer = customer_CustomerService::getInstance()->getCurrentCustomer();
		if ($customer !== null)
		{
			HttpController::getInstance()->redirectToUrl(LinkHelper::getTagUrl('contextual_website_website_modules_customer_my-account'));
		}

		$user = users_UserService::getInstance()->getCurrentFrontEndUser();
		if ($user !== null)
		{
			$request->setAttribute("beanId", $user->getId());
		}

		return $this->getInputViewName();
	}

	/**
	 * @param f_mvc_Request $request
	 * @param customer_CustomerWrapperBean $customerWrapper
	 * @return boolean
	 */
	public function validateSaveInput($request, $customerWrapper)
	{
		// Validation.
		$validationRules = array_merge(
			BeanUtils::getBeanValidationRules('customer_CustomerWrapperBean'), 
			BeanUtils::getSubBeanValidationRules('customer_CustomerWrapperBean', 'customer', null, array('label', 'user')), 
			BeanUtils::getSubBeanValidationRules('customer_CustomerWrapperBean', 'customer.user', null, array('label', 'login', 'passwordmd5', 'websiteid'))
		);
		if (!$customerWrapper->id)
		{
			$securityLevel = ModuleService::getInstance()->getPreferenceValue('users', 'securitylevel');
			$validationRules[] = "password{blank:false;password:$securityLevel}";
		}
		$cfg = $this->getConfiguration();
		if ($cfg->getConfirmEmail())
		{
			$validationRules[] = "propEq:customer.user.email,emailconfirm";
		}
		$isOk = $this->processValidationRules($validationRules, $request, $customerWrapper);

		// Login validation.
		$website = website_WebsiteModuleService::getInstance()->getCurrentWebsite();
		$user = $customerWrapper->customer->getUser();
		$login = ($user->getLogin() !== null) ? $user->getLogin() : $user->getEmail();
		$existingUser = users_UserService::getInstance()->getFrontendUserByLogin($login, $website->getId());
		if ($existingUser !== null && $existingUser->getId() !== $user->getId())
		{
			$this->addError(LocaleService::getInstance()->transFO('m.customer.document.customerwrapperbean.invalid-login-error', array('ucf')));
			$isOk = false;
		}
		
		return $isOk;
	}

	/**
	 * @param f_mvc_Request $request
	 * @param f_mvc_Response $response
	 * @param customer_CustomerWrapperBean $customerWrapper
	 * @return String
	 */
	public function executeSave($request, $response, customer_CustomerWrapperBean $customerWrapper)
	{
		$customer = $customerWrapper->customer;		
		$user = $customer->getUser();			
		if ($user->isNew())
		{				
			customer_CustomerService::getInstance()->saveNewCustomer($customer, $customerWrapper->password);
		}
		else
		{
			$user->save();
			$customer->save();
		}
		$request->setAttribute('customerWrapper', $customerWrapper);
		return 'Save';
	}
}
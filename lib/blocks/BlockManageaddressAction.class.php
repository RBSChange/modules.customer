<?php
/**
 * customer_BlockManageaddressAction
 * @package modules.customer
 */
class customer_BlockManageaddressAction extends website_BlockAction
{
	/**
	 * @see website_BlockAction::execute()
	 * @param f_mvc_Request $request
	 * @param f_mvc_Response $response
	 * @param customer_persistentdocument_address $address
	 * @return string
	 */
	public function execute($request, $response, customer_persistentdocument_address $address = null)
	{
		if ($this->isInBackofficeEdition())
		{
			return website_BlockView::NONE;
		}
		elseif ($this->isInBackofficePreview())
		{
			return website_BlockView::INPUT;
		}
		
		$customer = customer_CustomerService::getInstance()->getCurrentCustomer();
		if ($customer === null)
		{
			change_Controller::getInstance()->redirectToUrl(LinkHelper::getTagUrl('contextual_website_website_modules_customer_new-account'));
		}
		$request->setAttribute('customer', $customer);
		if ($request->hasParameter('message'))
		{
			$this->addMessage($request->getParameter('message'));
		}
		if ($request->hasParameter('error'))
		{
			$this->addError($request->getParameter('error'));
		}
		if ($customer->getAddressCount() === 0)
		{
			$address->getDocumentService()->initFieldsFromCustomer($address, $customer);
			$request->setAttribute('address', $address);
			return website_BlockView::INPUT;
		}
		return website_BlockView::SUCCESS;
	}
	
	/**
	 * @param f_mvc_Request $request
	 * @param f_mvc_Response $response
	 * @return string
	 */
	public function executeCancel($request, $response)
	{
		$this->redirect('customer', 'manageaddress');
		return website_BlockView::NONE;
	}
		
	/**
	 * @param f_mvc_Request $request
	 * @param f_mvc_Response $response
	 * @param customer_persistentdocument_address $address
	 * @return string
	 */
	public function executeAdd($request, $response, customer_persistentdocument_address $address)
	{
		$customer = customer_CustomerService::getInstance()->getCurrentCustomer();
		if ($customer === null)
		{
			change_Controller::getInstance()->redirectToUrl(LinkHelper::getTagUrl('contextual_website_website_modules_customer_new-account'));
		}
		$address->getDocumentService()->initFieldsFromCustomer($address, $customer);
		$request->setAttribute('address', $address);
		return website_BlockView::INPUT;
	}
	
	/**
	 * @param f_mvc_Request $request
	 * @param f_mvc_Response $response
	 * @param customer_persistentdocument_address $address
	 * @return string
	 */
	public function executeEdit($request, $response, customer_persistentdocument_address $address)
	{
		$customer = customer_CustomerService::getInstance()->getCurrentCustomer();
		if ($customer === null)
		{
			change_Controller::getInstance()->redirectToUrl(LinkHelper::getTagUrl('contextual_website_website_modules_customer_new-account'));
		}
		else if ($customer->getIndexofAddress($address) === null)
		{
			$this->redirect('customer', 'manageaddress', array('error' => LocaleService::getInstance()->trans('m.customer.frontoffice.not-your-address-error', array('ucf'))));
			return website_BlockView::NONE;
		}
		return website_BlockView::INPUT;
	}
	
	/**
	 * @param f_mvc_Request $request
	 * @param customer_persistentdocument_address $address
	 * @return string
	 */
	public function getSaveInputValidationRules($request)
	{
		$rules = BeanUtils::getBeanValidationRules('customer_persistentdocument_address', null, array('label'));
		$rules[] = 'email{email:true}';
		return $rules;
	}
	
	/**
	 * @param f_mvc_Request $request
	 * @param f_mvc_Response $response
	 * @param customer_persistentdocument_address $address
	 * @return string
	 */
	public function executeSave($request, $response, customer_persistentdocument_address $address)
	{
		$ls = LocaleService::getInstance();
		$customer = customer_CustomerService::getInstance()->getCurrentCustomer();
		if ($customer === null)
		{
			change_Controller::getInstance()->redirectToUrl(LinkHelper::getTagUrl('contextual_website_website_modules_customer_new-account'));
		}
		else if (!$address->isNew() && $customer->getIndexofAddress($address) === -1)
		{
			$this->redirect('customer', 'manageaddress', array('error' => $ls->trans('m.customer.frontoffice.not-your-address-error', array('ucf'))));
			return website_BlockView::NONE;
		}
		$request->setAttribute('customer', $customer);
		
		$isNew = $address->isNew();
		$address->save();
		if ($isNew)
		{
			$customer->addAddress($address);
			$customer->save();
			$this->redirect('customer', 'manageaddress', array('message' => $ls->trans('m.customer.frontoffice.creating-address-success', array('ucf'))));
		}
		else 
		{
			$this->redirect('customer', 'manageaddress', array('message' => $ls->trans('m.customer.frontoffice.updating-success', array('ucf'))));
		}
		return website_BlockView::NONE;
	}
	
	/**
	 * @param f_mvc_Request $request
	 * @param f_mvc_Response $response
	 * @param customer_persistentdocument_address $address
	 * @return string
	 */
	public function executeSetDefault($request, $response, customer_persistentdocument_address $address)
	{
		$ls = LocaleService::getInstance();
		$customer = customer_CustomerService::getInstance()->getCurrentCustomer();
		if ($customer === null)
		{
			change_Controller::getInstance()->redirectToUrl(LinkHelper::getTagUrl('contextual_website_website_modules_customer_new-account'));
		}
		else if ($customer->getIndexofAddress($address) === null)
		{
			$this->redirect('customer', 'manageaddress', array('error' => $ls->trans('m.customer.frontoffice.not-your-address-error', array('ucf'))));
			return website_BlockView::NONE;
		}
		$request->setAttribute('customer', $customer);
		$customer->removeAddress($address);
		$customer->setAddressArray(array_merge(array($address), $customer->getAddressArray()));
		$customer->save();
		$this->redirect('customer', 'manageaddress', array('message' => $ls->trans('m.customer.frontoffice.set-default-address-success', array('ucf'))));
		return website_BlockView::NONE;
	}
	
	/**
	 * @param f_mvc_Request $request
	 * @param f_mvc_Response $response
	 * @param customer_persistentdocument_address $address
	 * @return string
	 */
	public function executeDelete($request, $response, customer_persistentdocument_address $address)
	{
		$ls = LocaleService::getInstance();
		$customer = customer_CustomerService::getInstance()->getCurrentCustomer();
		if ($customer === null)
		{
			change_Controller::getInstance()->redirectToUrl(LinkHelper::getTagUrl('contextual_website_website_modules_customer_new-account'));
		}
		else if ($customer->getIndexofAddress($address) === null)
		{
			$this->redirect('customer', 'manageaddress', array('error' => $ls->trans('m.customer.frontoffice.not-your-address-error', array('ucf'))));
			return website_BlockView::NONE;
		}
		$request->setAttribute('customer', $customer);
		
		$address->delete();
		$this->redirect('customer', 'manageaddress', array('message' => $ls->trans('m.customer.frontoffice.deleting-address-success', array('ucf'))));
		return website_BlockView::NONE;
	}
}
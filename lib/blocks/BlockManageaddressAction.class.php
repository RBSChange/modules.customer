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
	 * @return String
	 */
	public function execute($request, $response, customer_persistentdocument_address $address)
	{
		$customer = customer_CustomerService::getInstance()->getCurrentCustomer();
		if ($customer === null)
		{
			HttpController::getInstance()->redirectToUrl('website', 'Error404');
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
	 * @return String
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
	 * @return String
	 */
	public function executeAdd($request, $response, customer_persistentdocument_address $address)
	{
		$customer = customer_CustomerService::getInstance()->getCurrentCustomer();
		if ($customer === null)
		{
			HttpController::getInstance()->redirectToUrl('website', 'Error404');
		}
		$address->getDocumentService()->initFieldsFromCustomer($address, $customer);
		$request->setAttribute('address', $address);
		return website_BlockView::INPUT;
	}
	
	/**
	 * @param f_mvc_Request $request
	 * @param f_mvc_Response $response
	 * @param customer_persistentdocument_address $address
	 * @return String
	 */
	public function executeEdit($request, $response, customer_persistentdocument_address $address)
	{
		$customer = customer_CustomerService::getInstance()->getCurrentCustomer();
		if ($customer === null)
		{
			HttpController::getInstance()->redirectToUrl('website', 'Error404');
		}
		else if ($customer->getIndexofAddress($address) === null)
		{
			$this->redirect('customer', 'manageaddress', array('error' => f_Locale::translate('&modules.customer.frontoffice.Not-your-address-error;')));
			return website_BlockView::NONE;
		}
		return website_BlockView::INPUT;
	}
	
	/**
	 * @param f_mvc_Request $request
	 * @param customer_persistentdocument_address $address
	 * @return String
	 */
	function getSaveInputValidationRules($request)
	{
		return BeanUtils::getBeanValidationRules('customer_persistentdocument_address', null, array('label'));
	}
	
	/**
	 * @param f_mvc_Request $request
	 * @param f_mvc_Response $response
	 * @param customer_persistentdocument_address $address
	 * @return String
	 */
	public function executeSave($request, $response, customer_persistentdocument_address $address)
	{
		$customer = customer_CustomerService::getInstance()->getCurrentCustomer();
		if ($customer === null)
		{
			HttpController::getInstance()->redirectToUrl('website', 'Error404');
		}
		else if (!$address->isNew() && $customer->getIndexofAddress($address) === -1)
		{
			$this->redirect('customer', 'manageaddress', array('error' => f_Locale::translate('&modules.customer.frontoffice.Not-your-address-error;')));
			return website_BlockView::NONE;
		}
		$request->setAttribute('customer', $customer);
		
		$isNew = $address->isNew();
		$address->save();
		if ($isNew)
		{
			$customer->addAddress($address);
			$customer->save();
			$this->redirect('customer', 'manageaddress', array('message' => f_Locale::translate('&modules.customer.frontoffice.Creating-address-success;')));
		}
		else 
		{
			$this->redirect('customer', 'manageaddress', array('message' => f_Locale::translate('&modules.customer.frontoffice.Updating-success;')));
		}
		return website_BlockView::NONE;
	}
	
	/**
	 * @param f_mvc_Request $request
	 * @param f_mvc_Response $response
	 * @param customer_persistentdocument_address $address
	 * @return String
	 */
	public function executeSetDefault($request, $response, customer_persistentdocument_address $address)
	{
		$customer = customer_CustomerService::getInstance()->getCurrentCustomer();
		if ($customer === null)
		{
			HttpController::getInstance()->redirectToUrl('website', 'Error404');
		}
		else if ($customer->getIndexofAddress($address) === null)
		{
			$this->redirect('customer', 'manageaddress', array('error' => f_Locale::translate('&modules.customer.frontoffice.Not-your-address-error;')));
			return website_BlockView::NONE;
		}
		$request->setAttribute('customer', $customer);
		$customer->removeAddress($address);
		$customer->setAddressArray(array_merge(array($address), $customer->getAddressArray()));
		$customer->save();
		$this->redirect('customer', 'manageaddress', array('message' => f_Locale::translate('&modules.customer.frontoffice.Set-default-address-success;')));
		return website_BlockView::NONE;
	}
	
	/**
	 * @param f_mvc_Request $request
	 * @param f_mvc_Response $response
	 * @param customer_persistentdocument_address $address
	 * @return String
	 */
	public function executeDelete($request, $response, customer_persistentdocument_address $address)
	{
		$customer = customer_CustomerService::getInstance()->getCurrentCustomer();
		if ($customer === null)
		{
			HttpController::getInstance()->redirectToUrl('website', 'Error404');
		}
		else if ($customer->getIndexofAddress($address) === null)
		{
			$this->redirect('customer', 'manageaddress', array('error' => f_Locale::translate('&modules.customer.frontoffice.Not-your-address-error;')));
			return website_BlockView::NONE;
		}
		$request->setAttribute('customer', $customer);
		
		$address->delete();
		$this->redirect('customer', 'manageaddress', array('message' => f_Locale::translate('&modules.customer.frontoffice.Deleting-address-success;')));
		return website_BlockView::NONE;
	}
}
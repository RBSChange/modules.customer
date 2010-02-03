<?php
/**
 * customer_BlockResendConfirmationEmailAction
 * @package modules.customer
 */
class customer_BlockResendConfirmationEmailAction extends website_BlockAction
{
	/**
	 * @see website_BlockAction::execute()
	 * @param f_mvc_Request $request
	 * @param f_mvc_Response $response
	 * @return String
	 */
	function execute($request, $response)
	{
		if ($this->isInBackoffice())
		{
			return website_BlockView::BACKOFFICE;
		}
		
		$customerService = customer_CustomerService::getInstance();
		$customer = $customerService->getCurrentCustomer();
		$success = $customerService->sendEmailConfirmationEmail($customer);
		$request->setAttribute('success', $success);
		return website_BlockView::SUCCESS;
	}
}
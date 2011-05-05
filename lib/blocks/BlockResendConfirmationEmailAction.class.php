<?php
/**
 * @deprecated (will be removed in 4.0)
 */
class customer_BlockResendConfirmationEmailAction extends website_BlockAction
{
	/**
	 * @deprecated (will be removed in 4.0)
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
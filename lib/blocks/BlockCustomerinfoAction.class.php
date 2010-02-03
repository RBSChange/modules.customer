<?php
/**
 * customer_BlockCustomerinfoAction
 * @package modules.customer
 */
class customer_BlockCustomerinfoAction extends website_BlockAction
{
	/**
	 * @see website_BlockAction::execute()
	 * @param f_mvc_Request $request
	 * @param f_mvc_Response $response
	 * @return String
	 */
	function execute($request, $response)
	{
		$customer = customer_CustomerService::getInstance()->getCurrentCustomer();
		$user = users_UserService::getInstance()->getCurrentFrontEndUser();
		$request->setAttribute('customer', $customer);
		$request->setAttribute('user', $user);
		return website_BlockView::SUCCESS;
	}
}
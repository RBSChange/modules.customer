<?php
/**
 * customer_BlockIndexAction
 * @package modules.customer
 */
class customer_BlockIndexAction extends website_BlockAction
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
			return website_BlockView::BACKOFFICE;
		}
		$request->setAttribute('user', users_UserService::getInstance()->getCurrentFrontEndUser());
		return website_BlockView::SUCCESS;
	}
}
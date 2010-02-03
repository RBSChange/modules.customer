<?php
/**
 * customer_EmailConfirmationAction
 * @package modules.customer
 */
class customer_EmailConfirmationAction extends f_action_BaseAction
{
	/**
	 * @param Context $context
	 * @param Request $request
	 */
	public function _execute($context, $request)
	{
		$cs = customer_CustomerService::getInstance();
		
		// Validate and perform the confirmation.
		$customer = $this->getDocumentInstanceFromRequest($request);
		$confirmationStatus = $cs->validateEmailConfirmation($customer, $request->getParameter('mailref'));
		
		// Redirect to the good page.
		$url = $cs->getEmailConfirmationRedirectionUrl($confirmationStatus);
		$context->getController()->redirectToUrl(str_replace('&amp;', '&', $url));
		return View::NONE;
	}
	
	public function getRequestMethods()
	{
		return Request::POST | Request::GET;
	}

	public function isSecure()
    {
    	return false;
    }
}
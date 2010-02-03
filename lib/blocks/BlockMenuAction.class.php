<?php
class customer_BlockMenuAction extends block_BlockAction
{
	/**
	 * @param block_BlockContext $context
	 * @param block_BlockRequest $request
	 * @return String the view name
	 */
	public function execute($context, $request)
	{
		$ws = website_WebsiteModuleService::getInstance();

		try
		{
			$menuaccount = $ws->getMenuByTag('modules_customer_menu-account', 1);
			$this->setParameter('menuaccount', $menuaccount);
			$this->setParameter('displayMenuaccount', count($menuaccount) > 0);
		}
		catch (TopicException $e)
		{
			Framework::exception($e);
		}

		try
		{
			$menuorders = $ws->getMenuByTag('modules_customer_menu-orders', 1);
			$this->setParameter('menuorders', $menuorders);
			$this->setParameter('displayMenuorders', count($menuorders) > 0);
		}
		catch (TopicException $e)
		{
			Framework::exception($e);
		}
		
		try
		{
			$menuservices = $ws->getMenuByTag('modules_customer_menu-services', 1);
			$this->setParameter('menuservices', $menuservices);
			$this->setParameter('displayMenuservices', count($menuservices) > 0);
		}
		catch (TopicException $e)
		{
			Framework::exception($e);
		}
		return block_BlockView::SUCCESS;
	}
}
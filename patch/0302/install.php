<?php
/**
 * @author intportg
 * @package modules.customer
 */
class customer_patch_0302 extends patch_BasePatch
{
	/**
	 * Entry point of the patch execution.
	 */
	public function execute()
	{
		parent::execute();
		
		$cs = customer_CustomerService::getInstance();
		$group = $cs->getCustomerUserGroup();
		foreach ($cs->createQuery()->find() as $customer)
		{
			$user = $customer->getUser();
			$user->addGroups($group);
			$user->save();
		}
	}

	/**
	 * Returns the name of the module the patch belongs to.
	 *
	 * @return String
	 */
	protected final function getModuleName()
	{
		return 'customer';
	}

	/**
	 * Returns the number of the current patch.
	 * @return String
	 */
	protected final function getNumber()
	{
		return '0302';
	}
}
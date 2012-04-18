<?php
/**
 * customer_patch_0360
 * @package modules.customer
 */
class customer_patch_0360 extends patch_BasePatch
{

	/**
	 * Entry point of the patch execution.
	 */
	public function execute()
	{
		$newPath = f_util_FileUtils::buildWebeditPath('modules/customer/persistentdocument/voucher.xml');
		$newModel = generator_PersistentModel::loadModelFromString(f_util_FileUtils::read($newPath), 'customer', 'voucher');
		$newProp = $newModel->getPropertyByName('billingAreaId');
		f_persistentdocument_PersistentProvider::getInstance()->addProperty('customer', 'voucher', $newProp);

		$this->execChangeCommand('compile-locales', array('customer'));
		
		$array = customer_VoucherService::getInstance()->createQuery()->find();
		foreach ($array as $voucher)
		{
			/* @var $voucher customer_persistentdocument_voucher */
			if (!$voucher->getBillingAreaId())
			{
				if ($voucher->getShop() === null)
				{
					$this->logWarning('Invalid Voucher ' . $voucher->getDocumentModelName() . ' ' . $voucher->getId() . ' - ' . $voucher->getLabel());
				}
				else
				{
					$voucher->setBillingAreaId($voucher->getShop()->getDefaultBillingArea()->getId());
					$voucher->save();
				}
			}
		}
	}
}
<?php
/**
 * customer_patch_0353
 * @package modules.customer
 */
class customer_patch_0353 extends patch_BasePatch
{
//  by default, isCodePatch() returns false.
//  decomment the following if your patch modify code instead of the database structure or content.
    /**
     * Returns true if the patch modify code that is versionned.
     * If your patch modify code that is versionned AND database structure or content,
     * you must split it into two different patches.
     * @return Boolean true if the patch modify code that is versionned.
     */
//	public function isCodePatch()
//	{
//		return true;
//	}
 
	/**
	 * Entry point of the patch execution.
	 */
	public function execute()
	{
		$mbs = uixul_ModuleBindingService::getInstance();
		$mbs->addImportInPerspective('catalog', 'customer', 'catalog.perspective');
		$mbs->addImportInActions('catalog', 'customer', 'catalog.actions');
		$result = $mbs->addImportform('catalog', 'modules_customer/voucherhandler');
		if ($result['action'] == 'create')
		{
			uixul_DocumentEditorService::getInstance()->compileEditorsConfig();
		}
		f_permission_PermissionService::getInstance()->addImportInRight('catalog', 'customer', 'catalog.rights');
		
		$voucherhandlers = customer_VoucherhandlerService::getInstance()->createQuery()->find();
		$trs = TreeService::getInstance();
		foreach ($voucherhandlers as $voucherhandler) 
		{
			if ($voucherhandler instanceof customer_persistentdocument_voucherhandler)
			{
				if ($voucherhandler->getTreeId() !== null)
				{
					$this->log('Remove voucherhandler ' . $voucherhandler->getId() . ' from tree ' . $voucherhandler->getTreeId());
					$node = $trs->getInstanceByDocument($voucherhandler); 
					$trs->deleteNode($node);
				}
			}
		}
	}

	/**
	 * @return String
	 */
	protected final function getModuleName()
	{
		return 'customer';
	}

	/**
	 * @return String
	 */
	protected final function getNumber()
	{
		return '0353';
	}
}
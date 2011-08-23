<?php
class customer_Setup extends object_InitDataSetup
{
	/**
	 * Returns an array of packages names that are required to install the data.
	 * Please override this method in the initData.php of your module if needed.
	 * @return Array<String>
	 */
	public function getRequiredPackages()
	{
		return array('modules_users', 'modules_zone');
	}

	public function install()
	{
		try
		{
			$scriptReader = import_ScriptReader::getInstance();
       	 	$scriptReader->executeModuleScript('customer', 'init.xml');
       	 	$scriptReader->executeModuleScript('customer', 'list.xml');
       	 	
			$mbs = uixul_ModuleBindingService::getInstance();
			$mbs->addImportInPerspective('catalog', 'customer', 'catalog.perspective');
			$mbs->addImportInActions('catalog', 'customer', 'catalog.actions');
			$result = $mbs->addImportform('catalog', 'modules_customer/voucherhandler');
			if ($result['action'] == 'create')
			{
				uixul_DocumentEditorService::getInstance()->compileEditorsConfig();
			}
				
			change_PermissionService::getInstance()->addImportInRight('catalog', 'customer', 'catalog.rights');
		}
		catch (Exception $e)
		{
			echo "ERROR: " . $e->getMessage() . "\n";
			Framework::exception($e);
		}
	}
}
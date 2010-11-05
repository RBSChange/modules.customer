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
		}
		catch (Exception $e)
		{
			echo "ERROR: " . $e->getMessage() . "\n";
			Framework::exception($e);
		}
	}
}
?>
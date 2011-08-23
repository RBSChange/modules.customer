<?php
/**
 * customer_TarifcustomergroupfolderService
 * @package customer
 */
class customer_TarifcustomergroupfolderService extends generic_FolderService
{
	/**
	 * @var customer_TarifcustomergroupfolderService
	 */
	private static $instance;

	/**
	 * @return customer_TarifcustomergroupfolderService
	 */
	public static function getInstance()
	{
		if (self::$instance === null)
		{
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * @return customer_persistentdocument_tarifcustomergroupfolder
	 */
	public function getNewDocumentInstance()
	{
		return $this->getNewDocumentInstanceByModelName('modules_customer/tarifcustomergroupfolder');
	}

	/**
	 * Create a query based on 'modules_customer/tarifcustomergroupfolder' model.
	 * Return document that are instance of modules_customer/tarifcustomergroupfolder,
	 * including potential children.
	 * @return f_persistentdocument_criteria_Query
	 */
	public function createQuery()
	{
		return $this->pp->createQuery('modules_customer/tarifcustomergroupfolder');
	}
	
	/**
	 * Create a query based on 'modules_customer/tarifcustomergroupfolder' model.
	 * Only documents that are strictly instance of modules_customer/tarifcustomergroupfolder
	 * (not children) will be retrieved
	 * @return f_persistentdocument_criteria_Query
	 */
	public function createStrictQuery()
	{
		return $this->pp->createQuery('modules_customer/tarifcustomergroupfolder', false);
	}
}
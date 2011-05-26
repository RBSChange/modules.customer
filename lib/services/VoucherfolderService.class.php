<?php
/**
 * customer_VoucherfolderService
 * @package modules.customer
 */
class customer_VoucherfolderService extends generic_FolderService
{
	/**
	 * @var customer_VoucherfolderService
	 */
	private static $instance;

	/**
	 * @return customer_VoucherfolderService
	 */
	public static function getInstance()
	{
		if (self::$instance === null)
		{
			self::$instance = self::getServiceClassInstance(get_class());
		}
		return self::$instance;
	}

	/**
	 * @return customer_persistentdocument_voucherfolder
	 */
	public function getNewDocumentInstance()
	{
		return $this->getNewDocumentInstanceByModelName('modules_customer/voucherfolder');
	}

	/**
	 * Create a query based on 'modules_customer/voucherfolder' model.
	 * Return document that are instance of modules_customer/voucherfolder,
	 * including potential children.
	 * @return f_persistentdocument_criteria_Query
	 */
	public function createQuery()
	{
		return $this->pp->createQuery('modules_customer/voucherfolder');
	}
	
	/**
	 * Create a query based on 'modules_customer/voucherfolder' model.
	 * Only documents that are strictly instance of modules_customer/voucherfolder
	 * (not children) will be retrieved
	 * @return f_persistentdocument_criteria_Query
	 */
	public function createStrictQuery()
	{
		return $this->pp->createQuery('modules_customer/voucherfolder', false);
	}
}
<?php
/**
 * @author intportg
 * @package modules.customer
 */
class customer_patch_0301 extends patch_BasePatch
{
	/**
	 * Entry point of the patch execution.
	 */
	public function execute()
	{
		parent::execute();
				
		$pp = f_persistentdocument_PersistentProvider::getInstance();
		$tm = f_persistentdocument_TransactionManager::getInstance();
		
		// Drop unused column.
		$this->executeSQLQuery("ALTER TABLE `m_customer_doc_customer` DROP COLUMN `subscriberid`");
		
		// Replace or remove old blocks.
		try
		{
			$tm->beginTransaction();
			$pages = website_PageService::getInstance()->createQuery()->find();
			echo("\n".count($pages)." pages to check:\n");
			foreach ($pages as $page)
			{
				$content = $page->getContent();
				$doc = new DOMDocument();
				$doc->loadXML($content);
				
				foreach ($doc->getElementsByTagNameNS('http://www.rbs.fr/change/1.0/schema', 'block') as $block)
				{
					switch ($block->getAttribute('type'))
					{
						case 'modules_customer_passwordlost' :
							$block->setAttribute('type', 'modules_users_resetpassword');
							echo(" - replace modules_customer_passwordlost in page ".$page->getId()."\n");
							break;
							
						case 'modules_customer_passwordchange' :
							$block->setAttribute('type', 'modules_users_changepassword');
							echo(" - replace modules_customer_passwordchange in page ".$page->getId()."\n");
							break;
							
						case 'modules_customer_mailinglist' :
							$block->parentNode->removeChild($block);
							echo(" - remove modules_customer_mailinglist from page ".$page->getId()."\n");
							break;
					}
				}
				
				$page->setContent($doc->saveXML());
				if ($page->isModified())
				{
					$pp->updateDocument($page);
				}
			}
			$tm->commit();
		}
		catch (Exception $e)
		{
			$tm->rollBack($e);
		}
		echo("End.\n");
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
		return '0301';
	}
}
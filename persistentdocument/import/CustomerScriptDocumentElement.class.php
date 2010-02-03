<?php
class customer_CustomerScriptDocumentElement extends import_ScriptDocumentElement
{
	private $initByLogin;
	
    /**
     * @return customer_persistentdocument_customer
     */
    protected function initPersistentDocument()
    {
    	if ($this->initByLogin)
    	{
    		$customer = customer_CustomerService::getInstance()->createQuery()
    			->add(Restrictions::eq('user.login', $this->initByLogin))->findUnique();
    		if (!$customer)
    		{
    			throw new Exception('Invalid login : ' . $this->initByLogin);
    		}
    		return $customer;	
    	}
    	return customer_CustomerService::getInstance()->getNewDocumentInstance();
    }
	
	/**
	 * @see import_ScriptDocumentElement::getPersistentDocument()
	 *
	 * @return f_persistentdocument_PersistentDocument
	 */
	public function getPersistentDocument()
	{
		if (isset($this->attributes['byLogin']))
		{
			$this->initByLogin = $this->attributes['byLogin'];
			unset($this->attributes['byLogin']);	
		}
		return parent::getPersistentDocument();
	}

}
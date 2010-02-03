<?php
/**
 * customer_persistentdocument_customergroup
 * @package customer.persistentdocument
 */
class customer_persistentdocument_customergroup extends customer_persistentdocument_customergroupbase 
{
	public function getAllMembers()
	{
		return $this->getDocumentService()->getMembers($this);
	}
}
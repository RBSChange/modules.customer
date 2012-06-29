<?php
/**
 * customer_patch_0400
 * @package modules.customer
 */
class customer_patch_0400 extends change_Patch
{
	/**
	 * @return array
	 */
	public function getPreCommandList()
	{
		return array(
			array('disable-site'),
		);
	}
	
	/**
	 * Entry point of the patch execution.
	 */
	public function execute()
	{
		$doc = customer_PreferencesService::getInstance()->createQuery()->findUnique();
		$doc->setLabel('m.customer.bo.general.module-name');
		$doc->save();
	}
	
	/**
	 * @return array
	 */
	public function getPostCommandList()
	{
		return array(
			array('clear-documentscache'),
			array('enable-site'),
		);
	}
	
	/**
	 * @return string
	 */
	public function getExecutionOrderKey()
	{
		return '2012-06-28 09:47:15';
	}
		
	/**
	 * @return string
	 */
	public function getBasePath()
	{
		return dirname(__FILE__);
	}
	
	/**
	 * @return false
	 */
	public function isCodePatch()
	{
		return false;
	}
}
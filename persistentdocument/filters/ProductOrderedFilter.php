<?php
class customer_ProductOrderedFilter extends f_persistentdocument_DocumentFilterImpl
{
	/**
	 * @return String
	 */
	public static function getDocumentModelName()
	{
		return 'modules_customer/customer';
	}
	
	public function __construct()
	{
		$info = new BeanPropertyInfoImpl('product', BeanPropertyType::DOCUMENT, 'catalog_persistentdocument_product');
		$info->setLabelKey('&modules.order.bo.documentfilters.parameter.cart-product;');
		$parameter = new f_persistentdocument_DocumentFilterValueParameter($info);
		$parameter->setCustomPropertyAttribute('product', 'dialog', 'productselector');
		$allow = DocumentHelper::expandAllowAttribute('[modules_catalog_product],[modules_catalog_declinedproduct]');
		$parameter->setCustomPropertyAttribute('product', 'allow', $allow);
		$this->setParameter('product', $parameter);
	}
	
	/**
	 * @return f_persistentdocument_criteria_Query
	 */
	public function getQuery()
	{
		$query = customer_CustomerService::getInstance()->createQuery();
		$ids = array();
		foreach ($this->getParameter('product')->getValueForQuery() as $document) 
		{
			if ($document instanceof catalog_persistentdocument_product)
			{
				$ids[$document->getId()] = true;
			}
			elseif ($document instanceof catalog_persistentdocument_declinedproduct)
			{
			   foreach ($document->getDeclinationArray() as $document) 
			   {	
			   		$ids[$document->getId()] = true;
			   }
			}
		}
		if (count($ids) === 0)
		{
			$query->add(Restrictions::eq('id', 0));
			return $query;
		}
	
		$orderQuery = $query->createCriteria('order');
		$orderQuery->add(Restrictions::in('orderStatus', array(order_OrderService::IN_PROGRESS, order_OrderService::COMPLETE)));
		
		$orderLineQuery = $orderQuery->createCriteria('line');
		$orderLineQuery->add(Restrictions::in('productId', array_keys($ids)));
		return $query;
	}
	
	/**
	 * @param customer_persistentdocument_customer $value
	 */
	public function checkValue($value)
	{
		if ($value instanceof customer_persistentdocument_customer)
		{
			$query = $this->getQuery()->add(Restrictions::eq('id', $value->getId()));
			if ($query->findUnique() !== null)
			{
				return true;
			}
		}
		return false;
	}
}
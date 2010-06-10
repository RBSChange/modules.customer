<?php
/**
 * @package modules.customer
 */
class customer_GetTreeChildrenJSONAction extends generic_GetTreeChildrenJSONAction
{
	/**
	 * @param f_persistentdocument_PersistentDocument $document
	 * @param string[] $subModelNames
	 * @param string $propertyName
	 * @return array<f_persistentdocument_PersistentDocument>
	 */
	protected function getVirtualChildren($document, $subModelNames, $propertyName)
	{
		if ($document instanceof customer_persistentdocument_dynamiccustomergroup)
		{
			$queryIntersection = f_persistentdocument_DocumentFilterService::getInstance()->getQueryIntersectionFromJson($document->getQuery());
			$totalCount = 0;
			$result = $queryIntersection->findAtOffset($this->getStartIndex(), $this->getPageSize(), $totalCount);
			$this->setTotal($totalCount);		
			return $result;
		}
		else
		{
			return parent::getVirtualChildren($document, $subModelNames, $propertyName);
		}
	}
}

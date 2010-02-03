<?php
class customer_LoginFilter extends f_persistentdocument_DocumentFilterImpl
{
	public function __construct()
	{
		$parameters = array();
		
		$info = new BeanPropertyInfoImpl('mode', 'String');
		$info->setLabelKey('mode de recherche');
		$info->setListId('modules_filter/matchmodes');
		$parameter = new f_persistentdocument_DocumentFilterValueParameter($info);
		$parameters['mode'] = $parameter;
		
		$info = new BeanPropertyInfoImpl('code', 'String');
		$info->setLabelKey('chaine recherchée');
		$parameter = new f_persistentdocument_DocumentFilterValueParameter($info);
		$parameters['code'] = $parameter;
		
		$this->setParameters($parameters);
	}

	/**
	 * @return String
	 */
	public static function getDocumentModelName()
	{
		return 'modules_customer/customer';
	}

	/**
	 * @return f_persistentdocument_criteria_Query
	 */
	public function getQuery()
	{
		$dfs = f_persistentdocument_DocumentFilterService::getInstance();
		$mode = $dfs->getMatchMode($this->getParameter('mode')->getValueForQuery());
		$query = customer_CustomerService::getInstance()->createQuery();
		$query->createCriteria('user')->add(Restrictions::like('login', $this->getParameter('code')->getValueForQuery(), $mode, true));
		return $query;
	}
	
	/**
	 * @param customer_persistentdocument_customer $value
	 */
	public function checkValue($value)
	{
		if ($value instanceof customer_persistentdocument_customer)
		{
			$login = $value->getUser()->getLogin();
			$needle = $this->getParameter('code')->getValue();
			switch ($this->getParameter('mode')->getValue())
			{
				case 'start':
					return f_util_StringUtils::beginsWith($login, $needle);
					
				case 'end':
					return f_util_StringUtils::endsWith($login, $needle);
					
				case 'anywhere':
					return f_util_StringUtils::contains($login, $needle);
					
				case 'exact':
					return $login == $needle;
			}
		}
		return false;
	}
}
?>
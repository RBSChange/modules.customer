<?php
/**
 * customer_CustomerService
 * @package modules.customer
 */
class customer_CustomerService extends f_persistentdocument_DocumentService
{
	const REASON_CONFIRM_EMAIL_ADDRESS = 1;
	const REASON_CONFIRM_ACCOUNT       = 2;
	
	const GROUP_TAG = 'default_modules_customer_customer-website-user-group';

	/**
	 * Singleton
	 * @var customer_CustomerService
	 */
	private static $instance = null;

	/**
	 * @return customer_CustomerService
	 */
	public static function getInstance()
	{
		if (is_null(self::$instance))
		{
			self::$instance = self::getServiceClassInstance(get_class());
		}
		return self::$instance;
	}

	/**
	 * @return customer_persistentdocument_customer
	 */
	public function getNewDocumentInstance()
	{
		return $this->getNewDocumentInstanceByModelName('modules_customer/customer');
	}

	/**
	 * Create a query based on 'modules_customer/customer' model
	 * @return f_persistentdocument_criteria_Query
	 */
	public function createQuery()
	{
		return $this->pp->createQuery('modules_customer/customer');
	}

	/**
	 * @param f_persistentdocument_PersistentDocument $document
	 * @return boolean true if the document is publishable, false if it is not.
	 */
	public function isPublishable($document)
	{
		return parent::isPublishable($document) && ! $document->getNotActivatedReason();
	}
	
	/**
	 * @param customer_persistentdocument_customer $document
	 * @param Integer $parentNodeId Parent node ID where to save the document (optionnal => can be null !).
	 * @return void
	 */
	protected function preSave($document, $parentNodeId)
	{
		$user = $document->getUser();
		if ($user !== null)
		{
			$document->setLabel($user->getLabel());
		}
		
		if ($document->isPropertyModified('birthday'))
		{
			if ($document->getBirthday())
			{
				$date = date_Calendar::getInstance($document->getUIBirthday());
				$document->setBirthdayDayNumber($date->getMonth()*31 + $date->getDay());
			}
			else
			{
				$document->setBirthdayDayNumber(null);
			}
		}
	}

	/**
	 * @param customer_persistentdocument_customer $document
	 * @param Integer $parentNodeId Parent node ID where to save the document (optionnal => can be null !).
	 * @return void
	 */
	protected function preInsert($document, $parentNodeId)
	{
		$group = $this->getCustomerUserGroup();
		$document->getUser()->addGroups($group);
		
		
	}

	/**
	 * @param customer_persistentdocument_customer $document
	 * @param Integer $parentNodeId Parent node ID where to save the document (optionnal => can be null !).
	 * @return void
	 */
	protected function postInsert($document, $parentNodeId)
	{
		if ($document->getCodeReference() === null)
		{
			$document->setCodeReference($this->generateCode($document));
			if ($document->getCodeReference() !== null)
			{
				$this->pp->updateDocument($document);
			}
		}
	}
	
	/**
	 * @param customer_persistentdocument_customer $document
	 * @return string
	 */
	public function generateCode($document)
	{
		$className = Framework::getConfigurationValue('modules/customer/customerCodeStrategyClass');
		if ($className !== null && f_util_ClassUtils::classExists($className))
		{
			$strategy = new $className();
			if ($strategy instanceof customer_CustomerCodeStrategy)
			{
				return $strategy->generate($document);
			}
		}
		return str_pad($document->getId(), 13, '0', STR_PAD_LEFT);
	}

	/**
	 * @return customer_persistentdocument_customer
	 */
	public function getCurrentCustomer()
	{
		$user = users_UserService::getInstance()->getCurrentFrontEndUser();
		if ($user != null)
		{
			return $this->getByUser($user);
		}
		return null;
	}

	/**
	 * @var customer_persistentdocument_customer[]
	 */
	private $customerByUserId = array();
	
	/**
	 * @param users_persistentdocument_websitefrontenduser $user
	 * @return customer_persistentdocument_customer
	 */
	public function getByUser($user)
	{
		if (!($user instanceof users_persistentdocument_websitefrontenduser))
		{
			return null;
		}
		return $this->getByUserId($user->getId());
	}
	
	/**
	 * @param integer $userId
	 * @return customer_persistentdocument_customer
	 */
	public function getByUserId($userId)
	{
		if ($userId === null)
		{
			return null;
		}

		if (!isset($this->customerByUserId[$userId]))
		{
			$this->customerByUserId[$userId] = $this->createQuery()->add(Restrictions::eq('user.id', $userId))->findUnique();
		}
		return $this->customerByUserId[$userId];
	}
	
	/**
	 * @param website_persistentdocument_website $website
	 * @return customer_persistentdocument_customer[]
	 */
	public function getByWebsite($website)
	{
		$query = $this->createQuery();
		$query->add(Restrictions::eq('user.websiteid', $website->getId()));
		return $query->find();
	}
	
	/**
	 * @param integer $websiteId
	 * @param string $email
	 * @param string $firstname
	 * @param string $lastname
	 * @param string $password
	 * @return customer_persistentdocument_customer
	 */
	public final function createNewCustomer($websiteId, $email, $firstname, $lastname, $password)
	{
		$tm = $this->getTransactionManager();
		try
		{
			$tm->beginTransaction();

			$user = users_WebsitefrontenduserService::getInstance()->getNewDocumentInstance();
			$user->setFirstname($firstname);
			$user->setLastname($lastname);
			$user->setLogin($email);
			$user->setEmail($email);
			$user->setPassword($password);
		
			$website = website_persistentdocument_website::getInstanceById($websiteId);
			$group = users_WebsitefrontendgroupService::getInstance()->getDefaultByWebsite($website);
			$user->setWebsiteid($group->getWebsiteid());
			$user->addGroups($group);
			
			// Save the user.
			$user->save();
			$user->activate();
			
			$customer = customer_CustomerService::getInstance()->getNewDocumentInstance();
			$customer->setUser($user);
			
			// Save the customer.
			$customer->save();
			$tm->commit();
			
			return $customer;
		}
		catch (Exception $e)
		{
			$tm->rollBack($e);
		}
		return null;	
	}
	
	/**
	 * @param users_persistentdocument_websitefrontenduser $user
	 * @return customer_persistentdocument_customer
	 */
	public final function createNewCustomerFromUser($user)
	{		
		$tm = $this->getTransactionManager();
		try
		{
			$tm->beginTransaction();
			$customer = customer_CustomerService::getInstance()->getNewDocumentInstance();
			$customer->setUser($user);
			$customer->save();
			$tm->commit();
			return $customer;
		}
		catch (Exception $e)
		{
			$tm->rollBack($e);
			
		}
		return null;	
	}
	
	/**
	 * @param customer_persistentdocument_customer $customer
	 * @param String $password
	 * @param website_persistentdocument_website $website
	 * @param Boolean $authenticate
	 */
	public function saveNewCustomer($customer, $password, $website = null, $authenticate = true)
	{
		$tm = f_persistentdocument_TransactionManager::getInstance();
		try
		{
			$tm->beginTransaction();			
			if ($website === null)
			{
				$website = website_WebsiteModuleService::getInstance()->getCurrentWebsite();
			}
			$group = users_WebsitefrontendgroupService::getInstance()->getDefaultByWebsite($website);
			$user = $customer->getUser();
			$user->setLogin($user->getEmail());
			$user->setPassword($password);
			$user->setWebsiteid($group->getWebsiteid());
			$user->addGroups($group);			
			$user->save();
			$user->activate();
				
			$customer->save();
			$tm->commit();
		}
		catch (Exception $e)
		{
			$tm->rollBack($e);
		}
		
		if ($authenticate)
		{
			$cartService = order_CartService::getInstance();
			$sessionCart = $cartService->getDocumentInstanceFromSession();
			$sessionCart->setMergeWithUserCart(false);
			users_UserService::getInstance()->authenticateFrontEndUser($user);
		}
	}
	
	/**
	 * @param website_persistentdocument_website $website
	 * @return users_persistentdocument_websitefrontendgroup
	 */
	public function getCustomerUserGroup()
	{
		$group = TagService::getInstance()->getDocumentByExclusiveTag(self::GROUP_TAG, false);
		if ($group === null)
		{
			$group = users_FrontendgroupService::getInstance()->getNewDocumentInstance();
			$group->setLabel(LocaleService::getInstance()->transFO('m.customer.bo.general.customer-user-group-label', array('ucf')));
			$group->save(ModuleService::getInstance()->getRootFolderId('users'));
			TagService::getInstance()->addTag($group, self::GROUP_TAG);
		}
		return $group;
	}

	/**
	 * @param customer_persistentdocument_customer $customer
	 * @param order_persistentdocument_coupon $coupon
	 * @return Boolean
	 */
	public function hasAlreadyUsedCoupon($customer, $coupon)
	{
		if ($customer !== null && $coupon !== null)
		{
			foreach ($customer->getUsedCouponArray() as $usedCoupon)
			{
				if ($coupon->getId() == $usedCoupon->getId())
				{
					return true;
				}
			}
		}
		return false;
	}

	/**
	 * @param order_persistentdocument_coupon $coupon
	 * @return Array<Integer>
	 */
	public function getIdsByUsedCoupon($coupon)
	{
		if ($coupon === null)
		{
			return array();
		}

		$query = $this->createQuery();
		$query->createCriteria('usedCoupon')->add(Restrictions::eq('id', $coupon->getId()));
		$query->setProjection(Projections::property('id'));
		$rows = $query->find();
		$ids = array();
		foreach ($rows as $row)
		{
			$ids[] = $row['id'];
		}
		return $ids;
	}
	
	/**
	 * @param website_persistentdocument_website $website
	 * @return Integer
	 */
	public function getCountByWebsite($website)
	{
		return f_util_ArrayUtils::firstElement($this->createQuery()
			->add(Restrictions::eq('user.websiteid', $website->getId()))
			->setProjection(Projections::rowCount('count'))->findColumn('count'));
	}
	
	/**
	 * @param website_persistentdocument_website $website
	 * @param date_Calendar $fromDate
	 * @param date_Calendar $toDate
	 * @return Array<String, Double>
	 */
	public function getStatisticsByWebsite($website, $fromDate, $toDate)
	{
		return array(
			'monthLabel' => ucfirst(date_Formatter::format($fromDate, 'F Y')),
			'monthShortLabel' => date_Formatter::format($fromDate, 'm/Y'),
			'new' => $this->findProjectedTotal($website, $fromDate, $toDate, Projections::rowCount('projection'), 'creationdate'),
			'lastlogin' => $this->findProjectedTotal($website, $fromDate, $toDate, Projections::rowCount('projection'), 'user.lastlogin'),
			'hasorder' => $this->findProjectedTotal($website, $fromDate, $toDate, Projections::rowCount('projection'), 'order.creationdate')
		);
	}
	
	/**
	 * @param website_persistentdocument_website $website
	 * @param date_Calendar $fromDate
	 * @param date_Calendar $toDate
	 * @param f_persistentdocument_criteria_OperationProjection $projection
	 * @param String $orderStatus
	 * @return Mixed
	 */
	private function findProjectedTotal($website, $fromDate, $toDate, $projection, $dateToCompare)
	{
		$query = $this->createQuery()->add(Restrictions::between(
			$dateToCompare,
			$fromDate->toString(),
			$toDate->toString()
		));
		$query->add(Restrictions::eq('user.websiteid', $website->getId()));
		return f_util_ArrayUtils::firstElement($query->setProjection($projection)->findColumn('projection'));
	}
	
	/**
	 * @param customer_persistentdocument_customer $document
	 * @param string $forModuleName
	 * @param array $allowedSections
	 * @return array
	 */
	public function getResume($document, $forModuleName, $allowedSections = null)
	{
		$data = parent::getResume($document, $forModuleName, $allowedSections);
		$rc = RequestContext::getInstance();
		$contextlang = $rc->getLang();
		$usecontextlang = $document->isLangAvailable($contextlang);
		$lang = $usecontextlang ? $contextlang : $document->getLang();
			
		try 
		{
			$rc->beginI18nWork($lang);
			
			$data['properties']['fullName'] = $document->getUser()->getFullName();
			$data['properties']['codeReference'] = $document->getCodeReference();
			
			// Carts.
			$data['carts'] = array();
			$cart = $document->getCart();
			if ($cart && $cart->getCartLineCount() != 0)
			{
				try 
				{
					$data['carts'][] = array(
						'lineCount' => $cart->getCartLineCount(),
						'totalTax' => $cart->getFormattedTotalTax(),
						'totalAmount' => $cart->getFormattedTotalAmount()
					);

				}
				catch (Exception $e)
				{
					Framework::exception($e);
				}
			}
			
			// Orders.
			$data['orders'] = array();
			$query = order_OrderService::getInstance()->createQuery();
			$query->add(Restrictions::eq('customer.id', $document->getId()));
			$query->add(Restrictions::notin('orderStatus', array(order_OrderService::CANCELED)));
			$query->setProjection(Projections::rowCount('count'), Projections::sum('totalAmountWithTax', 'amounttotal'), Projections::avg('totalAmountWithTax', 'amountaverage'), Projections::groupProperty('shopId'));
			$rows = $query->find();
			
			foreach ($rows as $row)
			{
				$shop = DocumentHelper::getDocumentInstance($row['shopId']);
				$data['orders'][] = array(
					'shop' => $shop->getLabelAsHtml(),
					'count' => $row['count'],
					'amounttotal' => $shop->formatPrice($row['amounttotal']),
					'amountaverage' => $shop->formatPrice($row['amountaverage'])
				);
			}
									
			$rc->endI18nWork();
		}
		catch (Exception $e)
		{
			$rc->endI18nWork($e);
		}
		
		return $data;
	}
	
	/**
	 * @param customer_persistentdocument_customer $customer
	 * @param customer_persistentdocument_coupon $coupon
	 */
	public function addUsedCoupon($customer, $coupon)
	{
		if (!in_array($coupon, $customer->getUsedCouponArray()))
		{
			try 
			{
				$this->tm->beginTransaction();
				$customer->addUsedCoupon($coupon);
				$this->pp->updateDocument($customer);
				$this->tm->commit();
			}
			catch (Exception $e)
			{
				$this->tm->rollBack($e);
				throw $e;
			}
			
			f_event_EventManager::dispatchEvent('addUsedCouponOnCustomer', $this, array('customer' => $customer, 'coupon' => $coupon));
		}
	}
	
	/**
	 * @param customer_persistentdocument_customer $customer
	 * @param customer_persistentdocument_coupon $coupon
	 */
	public function removeUsedCoupon($customer, $coupon)
	{
		if (in_array($coupon, $customer->getUsedCouponArray()))
		{
			try 
			{
				$this->tm->beginTransaction();
				$customer->removeUsedCoupon($coupon);
				$this->pp->updateDocument($customer);
				$this->tm->commit();
			}
			catch (Exception $e)
			{
				$this->tm->rollBack($e);
				throw $e;
			}
			
			f_event_EventManager::dispatchEvent('removeUsedCouponFromCustomer', $this, array('customer' => $customer, 'coupon' => $coupon));
		}
	}
	
	/**
	 * @param integer $min (excluded)
	 * @param integer $max (included)
	 */
	public function getIdsBetweenBirthdayDayNumber($min, $max)
	{
		return $this->createQuery()->add(Restrictions::gt('birthdayDayNumber', $min))
			->add(Restrictions::le('birthdayDayNumber', $max))
			->setProjection(Projections::property('id'))->findColumn('id');
	}
	
	/**
	 * @param customer_persistentdocument_customer $customer
	 * @param string $moduleName
	 * @param string $treeType
	 * @param array<string, string> $nodeAttributes
	 */
	public function addTreeAttributes($customer, $moduleName, $treeType, &$nodeAttributes)
	{
		if ($treeType != 'wlist')
		{
			return;
		}
		$nodeAttributes['birthday'] = date_Formatter::toDefaultDateBO($customer->getBirthday());
		$nodeAttributes['email'] = $customer->getUser()->getEmail();
		$nodeAttributes['date'] = date_Formatter::toDefaultDateTimeBO($customer->getCreationdate());

		// Activation.
		if (!$customer->isPublished())
		{
			$nodeAttributes['activation'] = $customer->getNotActivatedReasonLabel();
		}
		else
		{
			$nodeAttributes['activation'] = LocaleService::getInstance()->transBO("m.customer.bo.general.account-activated", array('ucf'));
		}
		
		// Website.
		$website = $customer->getWebsite();
		if ($website !== null)
		{
			$nodeAttributes['website'] = $website->getLabel();
		}
		else 
		{
			$nodeAttributes['website'] = '-';
		}
		
		$anonymizer = customer_AnonymizerService::getInstance();		
		$nodeAttributes['canBeAnonymized'] = (!$anonymizer->isAnonymized($customer) && $anonymizer->canBeAnonymized($customer));	
	}
		
	/**
	 * @param customer_persistentdocument_customer $customer
	 * @param string[] $propertiesNames
	 * @param array $formProperties
	 */
	public function addFormProperties($customer, $propertiesNames, &$formProperties)
	{
		$user = $customer->getUser();
		
		$formProperties['activateTrust'] = ModuleService::getInstance()->getPreferenceValue('customer', 'activateTrust');
		
		// Identification.
		$identification = array();
		if ($customer->getWebsite() !== null)
		{
			$identification['website'] = $customer->getWebsite()->getLabel();
		}
		if ($user->getTitle() !== null)
		{
			$identification['title'] = $user->getTitle()->getLabel();
		}
		$identification['firstname'] = $user->getFirstname();
		$identification['lastname'] = $user->getLastname();
		$identification['birthday'] = $customer->getUIBirthday();
		$identification['email'] = $user->getEmail();
		$identification['creationdate'] = date_Formatter::toDefaultDateTimeBO($customer->getUICreationdate());
		$formProperties['identification'] = $identification;
		
		// Addresses.
		$addresses = array();
		$ls = LocaleService::getInstance();
		foreach ($customer->getAddressArray() as $index => $address)
		{	
			$addressInfo = $address->getDocumentService()->getAddressInfos($address);
			if ($index == 0)
			{
				$addressInfo['label'] = $ls->transBO('m.customer.bo.general.default-address', array('ucf'));
			}
			else
			{
				$addressInfo['label'] = $ls->transBO('m.customer.bo.general.address-title', array('ucf'), array('number' => $index+1));
			}
			$addresses[] = $addressInfo;
		}
		$formProperties['addresses'] = $addresses;
	}
		
	/**
	 * @param customer_persistentdocument_customer $customer
	 * @return catalog_persistentdocument_shop[]
	 */
	public function getAllShops($customer)
	{
		$usergroup = users_WebsitefrontendgroupService::getInstance()->getDefaultByUser($customer->getUser());
		$websites = $usergroup->getWebsites();
		return catalog_ShopService::getInstance()->getPublishedByWebsites($websites);
	}
	
	/**
	 * @return string[string]
	 */
    public function getExportFields()
    {
    	$fieldNames = array();
    	$names = array('id', 'email', 'codeReference', 'birthday', 'civility', 'firstname', 'lastname', 
			'company', 'addressLine1', 'addressLine2', 'addressLine3', 'zipCode', 'city', 'province', 'countryCode', 'phone');
    	foreach ($names as $name) 
    	{
    		$fieldNames[$name] = $name;
    	}
        return $fieldNames;
    }
	
    /**
     * @param customer_persistentdocument_customer $customer
     * @param string[] $names
     */
    public function getExportValues($customer, $names)
    {
    	$address = $customer->getAddressCount() ? $customer->getAddress(0) : null; 
        $values = array();
		foreach ($names as $propertyName)
		{
			if ($propertyName === 'id')
			{
				 $values[$propertyName] = $customer->getId();
			}
			elseif ($propertyName === 'email')
			{
				 $values[$propertyName] = $customer->getEmail();
			}
			elseif ($propertyName === 'codeReference')
			{
				 $values[$propertyName] = $customer->getCodeReference();
			} 
			elseif ($propertyName === 'birthday')
			{
				 $values[$propertyName] = $customer->getUIBirthday();
			}
		    elseif ($address === null)
			{
				$values[$propertyName] = null;
			}
			else
			{
				$getter = 'get'.ucfirst($propertyName);
				$values[$propertyName] = $address->{$getter}();
			}
		}
		
		return array_map(array($this, 'fieldValueUTF8Decode'), $values);
	}
	
	/**
	 * @param string $value
	 * @return string
	 */
	private function fieldValueUTF8Decode($value)
	{
		if (is_string($value))
		{
			return mb_convert_encoding($value, 'ISO-8859-1', 'UTF-8');
		}
		return $value;
	}
	
	// Deprecated.
	
	/**
	 * @deprecated (will be removed in 4.0)
	 */
	const EMAIL_CONFIRMATION_OK = 1;
	
	/**
	 * @deprecated (will be removed in 4.0)
	 */
	const EMAIL_CONFIRMATION_NO_CUSTOMER = 2;
	
	/**
	 * @deprecated (will be removed in 4.0)
	 */
	const EMAIL_CONFIRMATION_BAD_STATE = 3;
	
	/**
	 * @deprecated (will be removed in 4.0)
	 */
	const EMAIL_CONFIRMATION_BAD_EMAIL = 4;
	
	/**
	 * @deprecated (will be removed in 4.0)
	 */
	public function sendEmailConfirmationEmail($customer)
	{
		if ($customer === null || $customer->getNotActivatedReason() != self::REASON_CONFIRM_EMAIL_ADDRESS)
		{
			return false;
		}
		$user = $customer->getUser();

		$url = LinkHelper::getActionUrl('customer', 'EmailConfirmation', array(
			'cmpref' => $customer->getId(),
			'lang' => $customer->getLang(),
			'mailref' => $user->getEmail()
		));
		$link = sprintf('<a class="link" href="%s" title="%s">%s</a>', $url, LocaleService::getInstance()->transFO('m.customer.mail.click-here-to-conforirm', array('ucf')), $url);

		$notificationService = notification_NotificationService::getInstance();
		$notification = $notificationService->getByCodeName('modules_customer/emailConfirmation');

		$recipients = new mail_MessageRecipients();
		$recipients->setTo($user->getEmail());

		$replacements = array();
		$replacements['title'] = ($user->getTitle() !== null) ? $user->getTitle()->getLabel() : '';
		$replacements['lastname'] = $user->getLastname();
		$replacements['fullname'] = $user->getFullname();
		$replacements['confirmation-url'] = $url;
		$replacements['confirmation-link'] = $link;

		return $notificationService->send($notification, $recipients, $replacements, 'customer');
	}
	
	/**
	 * @deprecated (will be removed in 4.0)
	 */
	public function validateEmailConfirmation($customer, $email)
	{
		// No customer.
		if (is_null($customer))
		{
			return self::EMAIL_CONFIRMATION_NO_CUSTOMER;
		}
		// Customer not needing confirmation.
		else if ($customer->getNotActivatedReason() != self::REASON_CONFIRM_EMAIL_ADDRESS)
		{
			return self::EMAIL_CONFIRMATION_BAD_STATE;
		}
		// Bad e-mail.
		else if ($customer->getUser()->getEmail() != $email)
		{
			return self::EMAIL_CONFIRMATION_BAD_EMAIL;
		}
		else
		{
			$customer->setNotActivatedReason(null);
			$customer->save();
			return self::EMAIL_CONFIRMATION_OK;
		}
	}

	/**
	 * @deprecated (will be removed in 4.0)
	 */
	public function getEmailConfirmationRedirectionUrl($confirmationCode)
	{
		$website = website_WebsiteModuleService::getInstance()->getCurrentWebsite();
		$tagService = TagService::getInstance();

		try
		{
			$page = $tagService->getDocumentByContextualTag('contextual_website_website_modules_customer_my-account', $website);
		}
		catch (TagException $e)
		{
			$e; // Avoid warning in Eclipse.
			$page = $tagService->getDocumentByContextualTag('contextual_website_website_error404', $website);
		}

		return LinkHelper::getDocumentUrl($page, RequestContext::getInstance()->getLang(), array('customerParam'=> array('confirmationCode' => $confirmationCode)));
	}
}
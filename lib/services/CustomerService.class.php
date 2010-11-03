<?php
/**
 * customer_CustomerService
 * @package modules.customer
 */
class customer_CustomerService extends f_persistentdocument_DocumentService
{
	const REASON_CONFIRM_EMAIL_ADDRESS = 1;
	const REASON_CONFIRM_ACCOUNT       = 2;

	const EMAIL_CONFIRMATION_OK = 1;
	const EMAIL_CONFIRMATION_NO_CUSTOMER = 2;
	const EMAIL_CONFIRMATION_BAD_STATE = 3;
	const EMAIL_CONFIRMATION_BAD_EMAIL = 4;
	
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
		if (!is_null($user))
		{
			$document->setLabel($user->getLabel());
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
		
			$websiteFrontendGroupService = users_WebsitefrontendgroupService::getInstance();
			$group = $websiteFrontendGroupService->getDefaultByWebsite(DocumentHelper::getDocumentInstance($websiteId));
			// Save the user.
			$user->save($group->getId());
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
			$user->save($group->getId());
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
		$frontendGroupService = users_FrontendgroupService::getInstance();
		try 
		{
			return $frontendGroupService->getDocumentByExclusiveTag(self::GROUP_TAG);
		}
		catch (Exception $e)
		{
			if (Framework::isDebugEnabled())
			{
				Framework::exception($e);
			}
			$group = $frontendGroupService->getNewDocumentInstance();
			$group->setLabel(f_Locale::translate('&modules.customer.bo.general.Customer-user-group-label;'));
			$group->save();
			TagService::getInstance()->addTag($group, self::GROUP_TAG);
		}
		return $group;
	}

	/**
	 * @param customer_persistentdocument_customer $customer
	 * @return Boolean
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
		$link = sprintf('<a class="link" href="%s" title="%s">%s</a>', $url, f_Locale::translate('&modules.customer.mail.Click-here-to-conforirm;'), $url);

		$notificationService = notification_NotificationService::getInstance();
		$notification = $notificationService->getNotificationByCodeName('modules_customer/emailConfirmation');

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
	 * @param customer_persistentdocument_customer $customer
	 * @param String $email
	 * @return Integer
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
	 * @param Integer $confirmationCode
	 * @return String
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

	/**
	 * @param customer_persistentdocument_customer $customer
	 * @param order_persistentdocument_coupon $coupon
	 * @return Boolean
	 */
	public function hasAlreadyUsedCoupon($customer, $coupon)
	{
		if (!is_null($customer) && !is_null($coupon))
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
		if (is_null($coupon))
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
			'monthLabel' => ucfirst(date_DateFormat::format($fromDate, 'F Y')),
			'monthShortLabel' => date_DateFormat::format($fromDate, 'm/Y'),
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
		$dbFormat = 'Y-m-d H:i:s';
		$query = $this->createQuery()->add(Restrictions::between(
			$dateToCompare,
			date_DateFormat::format($fromDate, $dbFormat),
			date_DateFormat::format($toDate, $dbFormat)
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
}
<?php

namespace Aventi\ForceLogin\Observer;

use Magento\Customer\Model\Context;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Psr\Log\LoggerInterface;

class RestrictWebsite implements ObserverInterface
{
    /**
     * @var \Aventi\ForceLogin\Helper\Data
     */
    private $_helper;
    /**
     * @var LoggerInterface
     */
    private $_logger;

    /**
     * RestrictWebsite constructor.
     * @param \Magento\Framework\App\Response\Http $response
     * @param \Magento\Framework\UrlFactory $urlFactory
     * @param \Magento\Framework\App\Http\Context $context
     * @param \Magento\Framework\App\ActionFlag $actionFlag
     * @param \Aventi\ForceLogin\Helper\Data $helper
     */
    public function __construct(
        \Magento\Framework\App\Response\Http $response,
        \Magento\Framework\UrlFactory $urlFactory,
        \Magento\Framework\App\Http\Context $context,
        \Magento\Framework\App\ActionFlag $actionFlag,
        \Aventi\ForceLogin\Helper\Data $helper,
        LoggerInterface $logger
    ) {
        $this->_response = $response;
        $this->_urlFactory = $urlFactory;
        $this->_context = $context;
        $this->_actionFlag = $actionFlag;
        $this->_helper = $helper;
        $this->_logger = $logger;
    }

    /**
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        if (!$this->_helper->isModuleEnabled()) {
            return true;
        }
        $allowedRoutes = [
            'customer_account_login',
            'customer_account_loginpost',
            'customer_account_logoutsuccess',
            'customer_account_confirm',
            'customer_account_confirmation',
            'customer_account_forgotpassword',
            'customer_account_forgotpasswordpost',
            'customer_account_createpassword',
            'customer_account_resetpasswordpost',
            'customer_section_load',
            'locationpopup_index_index',
            'citydropdown_index_index',
            'credit_index_index',
            'citydropdown_index_index',
            'captcha_refresh_index'
        ];

        $request = $observer->getEvent()->getRequest();
        $isCustomerLoggedIn = $this->_context->getValue(Context::CONTEXT_AUTH);
        $actionFullName = strtolower($request->getFullActionName());
        if (!$isCustomerLoggedIn && !in_array($actionFullName, $allowedRoutes)) {
            $this->_response->setRedirect($this->_urlFactory->create()->getUrl('customer/account/login'));
        }
    }
}

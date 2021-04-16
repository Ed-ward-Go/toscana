<?php
namespace Aventi\ForceLogin\Observer;

use Magento\Framework\Event\ObserverInterface;

class CustomerLogin implements ObserverInterface
{
    /**
     * @var \Magento\Framework\App\ResponseFactory
     */
    private $responseFactory;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    private $url;
    private $customerSession;
    private $messageManager;

    public function __construct(
        \Magento\Framework\App\ResponseFactory $responseFactory,
        \Magento\Framework\UrlInterface $url,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\Message\ManagerInterface $messageManager
    ) {
        $this->responseFactory = $responseFactory;
        $this->url = $url;
        $this->customerSession= $customerSession;
        $this->messageManager = $messageManager;
    }
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $customer = $observer->getEvent()->getCustomer();

        if($customer->getIsBlocked()){
            $this->customerSession->logout();
            $this->messageManager->addErrorMessage(__('Your account has been disabled'));
            $redirectionUrl = $this->url->getUrl('set-your-redirect-url');
            $this->responseFactory->create()->setRedirect($redirectionUrl)->sendResponse();

            return $this;
        }
    }
}

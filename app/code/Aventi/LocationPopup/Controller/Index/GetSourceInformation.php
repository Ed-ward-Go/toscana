<?php
declare(strict_types=1);

namespace Aventi\LocationPopup\Controller\Index;

class GetSourceInformation extends \Magento\Framework\App\Action\Action
{

    protected $resultPageFactory;
    protected $jsonHelper;

    protected $country;

    protected $helper;

    /**
     * Constructor
     *
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Framework\Json\Helper\Data $jsonHelper
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Aventi\LocationPopup\Helper\Data $helper
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \Psr\Log\LoggerInterface $logger,
        \Aventi\LocationPopup\Helper\Data $helper
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->jsonHelper = $jsonHelper;
        $this->logger = $logger;
        $this->helper = $helper;
        parent::__construct($context);
    }

    /**
     * Execute view action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        try {
            $result = $this->helper->getCurrentSourceInformation();
            return $this->jsonResponse($result);
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            return $this->jsonResponse($e->getMessage());
        } catch (\Exception $e) {
            $this->logger->critical($e);
            return $this->jsonResponse($e->getMessage());
        }
    }

    /**
     * Create json response
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function jsonResponse($response = '')
    {
        return $this->getResponse()->representJson(
            $this->jsonHelper->jsonEncode($response)
        );
    }
}


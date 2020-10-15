<?php
declare(strict_types=1);

namespace Aventi\LocationPopup\Controller\Index;

class SaveLocation extends \Magento\Framework\App\Action\Action
{

    protected $resultPageFactory;
    protected $jsonHelper;

    protected $country;

    protected $helper;

    private $_cacheTypeList;

    private $_cacheFrontendPool;

    private $cache;

    private $_checkoutSession;

    private $helperCities;
    /**
     * Constructor
     *
     * @param \Magento\Framework\App\Action\Context  $context
     * @param \Magento\Framework\Json\Helper\Data $jsonHelper
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \Psr\Log\LoggerInterface $logger,
        \Aventi\LocationPopup\Helper\Data $helper,
        \Aventi\PriceByCity\Helper\Data $helperCities,
        \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList,
        \Magento\Framework\App\Cache\Frontend\Pool $cacheFrontendPool,
        \Magento\Framework\App\CacheInterface $cache,
        \Magento\Checkout\Model\Session $_checkoutSession
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->jsonHelper = $jsonHelper;
        $this->logger = $logger;
        $this->helper = $helper;
        $this->_cacheTypeList = $cacheTypeList;
        $this->_cacheFrontendPool = $cacheFrontendPool;
        $this->cache = $cache;
        $this->_checkoutSession = $_checkoutSession;
        $this->helperCities = $helperCities;
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
            $data = $this->_request->getParam('data');                        
                      
            if($data){
                $this->helper->setValue($data);
            }
            //Bad solution, don't try this in house
            $types = array('block_html');
            foreach ($types as $type) {
                $this->_cacheTypeList->cleanType($type);
            }
            /*foreach ($this->_cacheFrontendPool as $cacheFrontend) {
                $cacheFrontend->getBackend()->clean();
            }      */
                    
            $deleted = $this->helperCities->updateItemsInCart($this->_checkoutSession->getQuote());            
            //$this->cache->clean('catalog_product_2');        
            return $this->jsonResponse($deleted);
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->logger->critical($e);
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


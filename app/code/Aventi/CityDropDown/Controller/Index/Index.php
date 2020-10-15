<?php


namespace Aventi\CityDropDown\Controller\Index;

class Index extends \Magento\Framework\App\Action\Action
{

    protected $resultPageFactory;
    protected $jsonHelper;
    /**
     * @var \Magento\Framework\Api\SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;
    /**
     * @var \Magento\Framework\Api\FilterBuilder
     */
    private $filterBuilder;
    /**
     * @var \Magento\Framework\Api\Search\FilterGroupBuilder
     */
    private $filterGroupBuilder;
    /**
     * @var \Aventi\CityDropDown\Model\CityRepository
     */
    private $cityRepository;
    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;
    /**
     * @var \Magento\Framework\Api\Search\SortOrder
     */
    private $sortOrder;    

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
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder,
        \Magento\Framework\Api\FilterBuilder $filterBuilder,
        \Magento\Framework\Api\SortOrder $sortOrder,        
        \Magento\Framework\Api\Search\FilterGroupBuilder $filterGroupBuilder,
        \Aventi\CityDropDown\Model\CityRepository $cityRepository,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->jsonHelper = $jsonHelper;
        parent::__construct($context);
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->filterBuilder = $filterBuilder;
        $this->filterGroupBuilder = $filterGroupBuilder;
        $this->sortOrder = $sortOrder;        
        $this->cityRepository = $cityRepository;
        $this->logger = $logger;
    }

    /**
     * Execute view action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        try {

            $region = $this->_request->getParam('region_id');
            $filterGroup = $this->filterGroupBuilder;
            $sortOrder = $this->sortOrder;
            $filterGroup->addFilter(
                $this->filterBuilder
                    ->setField('region_id')
                    ->setConditionType('like')
                    ->setValue($region)                    
                    ->create()
            );

            $sortOrder
                ->setField("name")
                ->setDirection("ASC");

            $searchCriteria = $this->searchCriteriaBuilder
                ->setFilterGroups([$filterGroup->create()])
                ->create();
                        
            $searchCriteria->setSortOrders([$sortOrder]);                
            $cities = $this->cityRepository->getList($searchCriteria)->getItems();

            $items = [];
            foreach ($cities as $city){
                $items[] =  [
                    'name' => $city->getName(),
                    'id' => $city->getCityId(),
                    'postalCode' => $city->getPostalCode()
                ];
            }

            usort($items, function($a, $b) {
                return $a['name'] <=> $b['name'];
            });

            return $this->jsonResponse($items);
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

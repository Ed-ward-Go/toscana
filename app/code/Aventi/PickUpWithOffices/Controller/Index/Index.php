<?php


namespace Aventi\PickUpWithOffices\Controller\Index;

class Index extends \Magento\Framework\App\Action\Action
{

    protected $resultPageFactory;
    protected $jsonHelper;
    /**
     * @var \Aventi\PickUpWithOffices\Model\OfficeRepository
     */
    private $officeRepository;
    /**
     * @var \Magento\Framework\Api\SearchCriteriaInterface
     */
    private $searchCriteria;
    /**
     * @var \Magento\Framework\Api\SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;
    /**
     * @var \Magento\Framework\Api\SortOrderBuilder
     */
    private $sortOrderBuilder;


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
        \Aventi\PickUpWithOffices\Model\OfficeRepository $officeRepository

    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->jsonHelper = $jsonHelper;
        parent::__construct($context);
        $this->officeRepository = $officeRepository;
    }

    /**
     * Execute view action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        try {

            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $this->searchCriteriaBuilder = $objectManager->create('Magento\Framework\Api\SearchCriteriaBuilder');
            $this->sortOrderBuilder = $objectManager->create('Magento\Framework\Api\SortOrderBuilder');
            $order =  $this->sortOrderBuilder->setField('city')->setDirection('ASC')->create();
            $searchCriteria = $this->searchCriteriaBuilder
                ->create();
            $offices = $this->officeRepository->getList($searchCriteria);
            $r = $rows = [];
            foreach ($offices->getItems() as $office){
                $r[trim($office->getCity())][] = [
                  'title' => $office->getTitle(),
                  'city' => $office->getCity(),
                  'schedule' => $office->getSchedule(),
                  'address' => str_replace("\n","<br>",$office->getAddress()),
                  'id' => $office->getOfficeId()
                ];
            }

            foreach ($r as $key=>$t){
                $rows['data'][] = [
                  'category' => $key,
                  'offices' => $t
                ];
            }


            return $this->jsonResponse($rows);
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
<?php

namespace Aventi\LocationPopup\Helper;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\Exception\NoSuchEntityException;

class Data extends AbstractHelper
{
    /**
     * @var \Magento\Framework\Session\SessionManagerInterface
     */

    private $coreSession;

    /**
     * @var \Magento\Framework\Json\Helper\Data
     */
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

    protected $country;

    private $region;
    /**
     * @var \Magento\Customer\Model\Session
     */
    private $_customerSession;
    /**
     * @var \Magento\InventoryApi\Api\SourceRepositoryInterface
     */
    private $_sourceRepository;
    /**
     * @var CustomerRepositoryInterface
     */
    private $_customerRepository;

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\Session\SessionManagerInterface $coreSession,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \Magento\Directory\Model\Country $country,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder,
        \Magento\Framework\Api\FilterBuilder $filterBuilder,
        \Magento\Framework\Api\SortOrder $sortOrder,
        \Magento\Framework\Api\Search\FilterGroupBuilder $filterGroupBuilder,
        \Aventi\CityDropDown\Model\CityRepository $cityRepository,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Directory\Model\Region $region,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\InventoryApi\Api\SourceRepositoryInterface $sourceRepository,
        CustomerRepositoryInterface $customerRepository
    ) {
        parent::__construct($context);
        $this->coreSession = $coreSession;
        $this->jsonHelper = $jsonHelper;
        $this->country = $country;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->filterBuilder = $filterBuilder;
        $this->filterGroupBuilder = $filterGroupBuilder;
        $this->sortOrder = $sortOrder;
        $this->cityRepository = $cityRepository;
        $this->logger = $logger;
        $this->region = $region;
        $this->_customerSession = $customerSession;
        $this->_sourceRepository = $sourceRepository;
        $this->_customerRepository = $customerRepository;
    }

    /**
     * @param $array
     */
    public function setValue($array)
    {
        $this->coreSession->start();
        $this->coreSession->setLocation($array);
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        $this->coreSession->start();
        $result = false;//$this->coreSession->getLocation();

        if (!$result) {
            if ($this->_customerSession->isLoggedIn()) {
                $customerId = $this->_customerSession->getCustomerId();
                if ($customer = $this->_customerRepository->getById($customerId)) {
                    $options = '';

                    $source = $customer->getCustomAttribute('warehouse_group')->getValue();
                    $sources = $this->getSourceByWarehouseGroup($source);
                    $result = [
                        "id" => $sources['id'],
                        "name" => $sources['name'],
                        "source" => $sources['source'],
                        "default" => true
                    ];
                }
            }
        }
        return $result;
    }

    /**
     * @return mixed
     */
    public function unsValue()
    {
        $this->coreSession->start();
        return $this->coreSession->unsLocation();
    }

    public function getSources()
    {
        $sources = $this->getAllSources();

        if ($this->_customerSession->isLoggedIn()) {
            $customerId = $this->_customerSession->getCustomerId();
            if ($customer = $this->_customerRepository->getById($customerId)) {
                $options = '';

                $source = $customer->getCustomAttribute('warehouse_group')->getValue();
                foreach ($sources as $key => $value) {
                    $selected = '';
                    if ($value['source'] == $source) {
                        $selected = "selected";
                    }
                    $options .= '<option data-source="' . $value['source'] . '" data-name="' . $value['name'] . '" value="' . $value['id'] . '" ' . $selected . '>' . $value['name'] . '</option>';
                }
                return $options;
            }
        }
    }

    public function getAllSources()
    {
        try {
            $sortOrder = $this->sortOrder;

            $filter1 = $this->filterBuilder
                ->setField("enabled")
                ->setValue(1)
                ->setConditionType("eq")->create();

            $filterGroup1 = $this->filterGroupBuilder
                ->addFilter($filter1)->create();

            $filter2 = $this->filterBuilder
                ->setField("source_code")
                ->setValue('default')
                ->setConditionType("neq")->create();

            $filterGroup2 = $this->filterGroupBuilder
                ->addFilter($filter2)->create();

            $sortOrder
                ->setField("name")
                ->setDirection("ASC");

            $searchCriteria = $this->searchCriteriaBuilder
                ->setFilterGroups([$filterGroup1, $filterGroup2])
                ->create();

            $searchCriteria->setSortOrders([$sortOrder]);
            $sources = $this->_sourceRepository->getList($searchCriteria)->getItems();
            $items = [];
            foreach ($sources as $source) {
                $items[] =  [
                    'name' => $source->getName(),
                    'id' => $source->getSourceCode(),
                    'source' => $source->getContactName()
                ];
            }
            return $items;
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->logger->error($e->getMessage());
        } catch (\Exception $e) {
            $this->logger->critical($e);
        }
    }

    public function getSourceByWarehouseGroup($group)
    {
        try {
            $sortOrder = $this->sortOrder;

            $filter = $this->filterBuilder
                ->setField("contact_name")
                ->setValue($group)
                ->setConditionType("eq")->create();

            $filterGroup = $this->filterGroupBuilder
                ->addFilter($filter)->create();

            $sortOrder
                ->setField("name")
                ->setDirection("ASC");

            $searchCriteria = $this->searchCriteriaBuilder
                ->setFilterGroups([$filterGroup])
                ->create();

            $searchCriteria->setSortOrders([$sortOrder]);
            $sources = $this->_sourceRepository->getList($searchCriteria)->getItems();
            $items = [];
            foreach ($sources as $source) {
                $items =  [
                    'name' => $source->getName(),
                    'id' => $source->getSourceCode(),
                    'source' => $source->getContactName()
                ];
            }
            return $items;
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->logger->error($e->getMessage());
        } catch (\Exception $e) {
            $this->logger->critical($e);
        }
    }

    public function getRegionName($id)
    {
        return $this->region->load($id)->getName();
    }

    public function getCurrentSourceInformation()
    {
        $selectedSource = $this->getValue();
        $currentSource = [
            'id' => '',
            'name' => '',
            'address' => '',
            'city' => ''
        ];
        if ($selectedSource) {
            try {
                $source = $this->_sourceRepository->get($selectedSource['id']);
                if ($source) {
                    $currentSource = [
                        'id' => $source->getSourceCode(),
                        'name' => $source->getName(),
                        'address' => $source->getStreet(),
                        'city' => $source->getCity()
                    ];
                }
            } catch (NoSuchEntityException $e) {
                $this->logger->error("Error to get the source information");
            }
        }

        return $currentSource;
    }
}

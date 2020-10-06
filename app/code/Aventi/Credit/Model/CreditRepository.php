<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Aventi\Credit\Model;

use Aventi\Credit\Api\CreditRepositoryInterface;
use Aventi\Credit\Api\Data\CreditInterface;
use Aventi\Credit\Api\Data\CreditInterfaceFactory;
use Aventi\Credit\Api\Data\CreditSearchResultsInterfaceFactory;
use Aventi\Credit\Model\ResourceModel\Credit as ResourceCredit;
use Aventi\Credit\Model\ResourceModel\Credit\CollectionFactory as CreditCollectionFactory;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\ExtensibleDataObjectConverter;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\FilterGroupBuilder;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\DB\Select;
use Magento\Framework\EntityManager\EntityManager;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Model\ResourceModel\Db\Context as DbContext;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Store\Model\StoreManagerInterface;

class CreditRepository implements CreditRepositoryInterface
{

    private $collectionProcessor;

    protected $creditFactory;

    protected $resource;

    protected $extensibleDataObjectConverter;
    protected $searchResultsFactory;

    protected $dataObjectProcessor;

    private $storeManager;

    protected $creditCollectionFactory;

    protected $dataCreditFactory;

    protected $extensionAttributesJoinProcessor;

    protected $dataObjectHelper;

    private $registryByCustomerId = [];

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;
    /**
     * @var FilterBuilder
     */
    private $filterBuilder;
    /**
     * @var FilterGroupBuilder
     */
    private $filterGroupBuilder;

    /**
     * @param ResourceCredit $resource
     * @param CreditFactory $creditFactory
     * @param CreditInterfaceFactory $dataCreditFactory
     * @param CreditCollectionFactory $creditCollectionFactory
     * @param CreditSearchResultsInterfaceFactory $searchResultsFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param StoreManagerInterface $storeManager
     * @param CollectionProcessorInterface $collectionProcessor
     * @param JoinProcessorInterface $extensionAttributesJoinProcessor
     * @param ExtensibleDataObjectConverter $extensibleDataObjectConverter
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param FilterBuilder $filterBuilder
     * @param FilterGroupBuilder $filterGroupBuilder
     */
    public function __construct(
        ResourceCredit $resource,
        CreditFactory $creditFactory,
        CreditInterfaceFactory $dataCreditFactory,
        CreditCollectionFactory $creditCollectionFactory,
        CreditSearchResultsInterfaceFactory $searchResultsFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        StoreManagerInterface $storeManager,
        CollectionProcessorInterface $collectionProcessor,
        JoinProcessorInterface $extensionAttributesJoinProcessor,
        ExtensibleDataObjectConverter $extensibleDataObjectConverter,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        FilterBuilder $filterBuilder,
        FilterGroupBuilder $filterGroupBuilder
    ) {
        $this->resource = $resource;
        $this->creditFactory = $creditFactory;
        $this->creditCollectionFactory = $creditCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataCreditFactory = $dataCreditFactory;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->storeManager = $storeManager;
        $this->collectionProcessor = $collectionProcessor;
        $this->extensionAttributesJoinProcessor = $extensionAttributesJoinProcessor;
        $this->extensibleDataObjectConverter = $extensibleDataObjectConverter;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->filterBuilder = $filterBuilder;
        $this->filterGroupBuilder = $filterGroupBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public function save(
        \Aventi\Credit\Api\Data\CreditInterface $credit
    ) {
        /* if (empty($credit->getStoreId())) {
            $storeId = $this->storeManager->getStore()->getId();
            $credit->setStoreId($storeId);
        } */

        $creditData = $this->extensibleDataObjectConverter->toNestedArray(
            $credit,
            [],
            \Aventi\Credit\Api\Data\CreditInterface::class
        );

        $creditModel = $this->creditFactory->create()->setData($creditData);

        try {
            $this->resource->save($creditModel);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the credit: %1',
                $exception->getMessage()
            ));
        }
        return $creditModel->getDataModel();
    }

    /**
     * {@inheritdoc}
     */
    public function get($creditId)
    {
        $credit = $this->creditFactory->create();
        $this->resource->load($credit, $creditId);
        if (!$credit->getId()) {
            throw new NoSuchEntityException(__('Credit with id "%1" does not exist.', $creditId));
        }
        return $credit->getDataModel();
    }

    /**
     * @inheritdoc
     */
    public function getByCustomerId($customerId, $reload = false)
    {
        if (!isset($this->registryByCustomerId[$customerId]) || $reload) {
            $creditData = $this->loadByCustomerId($customerId);
            if (!$creditData) {
                throw NoSuchEntityException::singleField(CreditInterface::CUSTOMER_ID, $customerId);
            }
            $creditSummary = $this->prepareDataObjectFromRowData($creditData);
            if ($creditSummary->getCreditId()) {
                $this->registry[$creditSummary->getCreditId()] = $creditSummary;
            }
            $this->registryByCustomerId[$customerId] = $creditSummary;
        }
        return $this->registryByCustomerId[$customerId];
    }

    /**
     * {@inheritdoc}
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    ) {
        $collection = $this->creditCollectionFactory->create();

        $this->extensionAttributesJoinProcessor->process(
            $collection,
            \Aventi\Credit\Api\Data\CreditInterface::class
        );

        $this->collectionProcessor->process($criteria, $collection);

        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($criteria);

        $items = [];
        foreach ($collection as $model) {
            $items[] = $model->getDataModel();
        }

        $searchResults->setItems($items);
        $searchResults->setTotalCount($collection->getSize());
        return $searchResults;
    }

    /**
     * {@inheritdoc}
     */
    public function delete(
        \Aventi\Credit\Api\Data\CreditInterface $credit
    ) {
        try {
            $creditModel = $this->creditFactory->create();
            $this->resource->load($creditModel, $credit->getCreditId());
            $this->resource->delete($creditModel);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the Credit: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($creditId)
    {
        return $this->delete($this->get($creditId));
    }

    /**
     * Retrieves data object from model
     *
     * @param CreditSummary|DataObject|array $model
     * @return CreditInterface
     */
    private function prepareDataObjectFromModel($model)
    {
        /** @var CreditInterface $object */
        $object = $this->dataCreditFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $object,
            is_array($model) ? $model : $model->getData(),
            CreditInterface::class
        );

        return $object;
    }

    /**
     * Prepare data object from row data array
     *
     * @param array $dataArray
     * @return CreditInterface
     */
    private function prepareDataObjectFromRowData($dataArray)
    {
        $notFormattedDataObject = $this->prepareDataObjectFromModel($dataArray);
        $formattedData = $this->dataObjectProcessor->buildOutputDataArray(
            $notFormattedDataObject,
            CreditInterface::class
        );

        return $this->prepareDataObjectFromModel($formattedData);
    }

    /**
     * Get credit limit summary by customer ID
     *
     * @param int $customerId
     * @return array
     * @throws Select
     */
    public function loadByCustomerId($customerId)
    {
        $filter1 = $this->filterBuilder
            ->setField("customer_id")
            ->setValue($customerId)
            ->setConditionType("eq")->create();

        $filterGroup1 = $this->filterGroupBuilder
            ->addFilter($filter1)->create();

        $filter2 = $this->filterBuilder
            ->setField("credit")
            ->setValue(0)
            ->setConditionType("neq")->create();

        $filterGroup2 = $this->filterGroupBuilder
            ->addFilter($filter2)->create();

        $searchCriteria = $this->searchCriteriaBuilder
            ->setFilterGroups([$filterGroup1, $filterGroup2])
            ->create();
        $items = $this->getList($searchCriteria)->getItems();

        $result = [];
        foreach ($items as $item) {
            $result = [
                'credit_id' => $item->getCreditId(),
                'credit' => $item->getCredit(),
                'available' => ($item->getCredit() + $item->getBalance()),
                'balance' => $item->getBalance(),
                'customer_id' => $item->getCustomerId()
            ];
        }

        return $result;
    }
}


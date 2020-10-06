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
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
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
        ExtensibleDataObjectConverter $extensibleDataObjectConverter
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
            $creditData = $this->resource->loadByCustomerId($customerId);
            if (!$creditData) {
                throw NoSuchEntityException::singleField(CreditInterface::CUSTOMER_ID, $customerId);
            }
            $creditSummary = $this->prepareDataObjectFromRowData($creditData);
            if ($creditSummary->getSummaryId()) {
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
}


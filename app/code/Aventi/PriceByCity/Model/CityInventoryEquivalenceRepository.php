<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Aventi\PriceByCity\Model;

use Aventi\PriceByCity\Api\CityInventoryEquivalenceRepositoryInterface;
use Aventi\PriceByCity\Api\Data\CityInventoryEquivalenceInterfaceFactory;
use Aventi\PriceByCity\Api\Data\CityInventoryEquivalenceSearchResultsInterfaceFactory;
use Aventi\PriceByCity\Model\ResourceModel\CityInventoryEquivalence as ResourceCityInventoryEquivalence;
use Aventi\PriceByCity\Model\ResourceModel\CityInventoryEquivalence\CollectionFactory as CityInventoryEquivalenceCollectionFactory;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\ExtensibleDataObjectConverter;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Store\Model\StoreManagerInterface;

class CityInventoryEquivalenceRepository implements CityInventoryEquivalenceRepositoryInterface
{

    protected $resource;

    protected $dataObjectHelper;

    protected $extensibleDataObjectConverter;
    private $storeManager;

    protected $cityInventoryEquivalenceFactory;

    protected $dataObjectProcessor;

    protected $searchResultsFactory;

    private $collectionProcessor;

    protected $extensionAttributesJoinProcessor;

    protected $cityInventoryEquivalenceCollectionFactory;

    protected $dataCityInventoryEquivalenceFactory;


    /**
     * @param ResourceCityInventoryEquivalence $resource
     * @param CityInventoryEquivalenceFactory $cityInventoryEquivalenceFactory
     * @param CityInventoryEquivalenceInterfaceFactory $dataCityInventoryEquivalenceFactory
     * @param CityInventoryEquivalenceCollectionFactory $cityInventoryEquivalenceCollectionFactory
     * @param CityInventoryEquivalenceSearchResultsInterfaceFactory $searchResultsFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param StoreManagerInterface $storeManager
     * @param CollectionProcessorInterface $collectionProcessor
     * @param JoinProcessorInterface $extensionAttributesJoinProcessor
     * @param ExtensibleDataObjectConverter $extensibleDataObjectConverter
     */
    public function __construct(
        ResourceCityInventoryEquivalence $resource,
        CityInventoryEquivalenceFactory $cityInventoryEquivalenceFactory,
        CityInventoryEquivalenceInterfaceFactory $dataCityInventoryEquivalenceFactory,
        CityInventoryEquivalenceCollectionFactory $cityInventoryEquivalenceCollectionFactory,
        CityInventoryEquivalenceSearchResultsInterfaceFactory $searchResultsFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        StoreManagerInterface $storeManager,
        CollectionProcessorInterface $collectionProcessor,
        JoinProcessorInterface $extensionAttributesJoinProcessor,
        ExtensibleDataObjectConverter $extensibleDataObjectConverter
    ) {
        $this->resource = $resource;
        $this->cityInventoryEquivalenceFactory = $cityInventoryEquivalenceFactory;
        $this->cityInventoryEquivalenceCollectionFactory = $cityInventoryEquivalenceCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataCityInventoryEquivalenceFactory = $dataCityInventoryEquivalenceFactory;
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
        \Aventi\PriceByCity\Api\Data\CityInventoryEquivalenceInterface $cityInventoryEquivalence
    ) {
        /* if (empty($cityInventoryEquivalence->getStoreId())) {
            $storeId = $this->storeManager->getStore()->getId();
            $cityInventoryEquivalence->setStoreId($storeId);
        } */
        
        $cityInventoryEquivalenceData = $this->extensibleDataObjectConverter->toNestedArray(
            $cityInventoryEquivalence,
            [],
            \Aventi\PriceByCity\Api\Data\CityInventoryEquivalenceInterface::class
        );
        
        $cityInventoryEquivalenceModel = $this->cityInventoryEquivalenceFactory->create()->setData($cityInventoryEquivalenceData);
        
        try {
            $this->resource->save($cityInventoryEquivalenceModel);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the cityInventoryEquivalence: %1',
                $exception->getMessage()
            ));
        }
        return $cityInventoryEquivalenceModel->getDataModel();
    }

    /**
     * {@inheritdoc}
     */
    public function get($cityInventoryEquivalenceId)
    {
        $cityInventoryEquivalence = $this->cityInventoryEquivalenceFactory->create();
        $this->resource->load($cityInventoryEquivalence, $cityInventoryEquivalenceId);
        if (!$cityInventoryEquivalence->getId()) {
            throw new NoSuchEntityException(__('CityInventoryEquivalence with id "%1" does not exist.', $cityInventoryEquivalenceId));
        }
        return $cityInventoryEquivalence->getDataModel();
    }

    /**
     * {@inheritdoc}
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    ) {
        $collection = $this->cityInventoryEquivalenceCollectionFactory->create();
        
        $this->extensionAttributesJoinProcessor->process(
            $collection,
            \Aventi\PriceByCity\Api\Data\CityInventoryEquivalenceInterface::class
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
        \Aventi\PriceByCity\Api\Data\CityInventoryEquivalenceInterface $cityInventoryEquivalence
    ) {
        try {
            $cityInventoryEquivalenceModel = $this->cityInventoryEquivalenceFactory->create();
            $this->resource->load($cityInventoryEquivalenceModel, $cityInventoryEquivalence->getEntityId());
            $this->resource->delete($cityInventoryEquivalenceModel);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the CityInventoryEquivalence: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($cityInventoryEquivalenceId)
    {
        return $this->delete($this->get($cityInventoryEquivalenceId));
    }
}

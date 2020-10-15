<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Aventi\PriceByCity\Model;

use Aventi\PriceByCity\Api\Data\PriceByCityInterfaceFactory;
use Aventi\PriceByCity\Api\Data\PriceByCitySearchResultsInterfaceFactory;
use Aventi\PriceByCity\Api\PriceByCityRepositoryInterface;
use Aventi\PriceByCity\Model\ResourceModel\PriceByCity as ResourcePriceByCity;
use Aventi\PriceByCity\Model\ResourceModel\PriceByCity\CollectionFactory as PriceByCityCollectionFactory;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\ExtensibleDataObjectConverter;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Store\Model\StoreManagerInterface;

class PriceByCityRepository implements PriceByCityRepositoryInterface
{

    protected $resource;

    protected $dataObjectHelper;

    protected $extensibleDataObjectConverter;
    protected $priceByCityCollectionFactory;

    private $storeManager;

    protected $dataObjectProcessor;

    protected $searchResultsFactory;

    protected $dataPriceByCityFactory;

    protected $priceByCityFactory;

    private $collectionProcessor;

    protected $extensionAttributesJoinProcessor;


    /**
     * @param ResourcePriceByCity $resource
     * @param PriceByCityFactory $priceByCityFactory
     * @param PriceByCityInterfaceFactory $dataPriceByCityFactory
     * @param PriceByCityCollectionFactory $priceByCityCollectionFactory
     * @param PriceByCitySearchResultsInterfaceFactory $searchResultsFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param StoreManagerInterface $storeManager
     * @param CollectionProcessorInterface $collectionProcessor
     * @param JoinProcessorInterface $extensionAttributesJoinProcessor
     * @param ExtensibleDataObjectConverter $extensibleDataObjectConverter
     */
    public function __construct(
        ResourcePriceByCity $resource,
        PriceByCityFactory $priceByCityFactory,
        PriceByCityInterfaceFactory $dataPriceByCityFactory,
        PriceByCityCollectionFactory $priceByCityCollectionFactory,
        PriceByCitySearchResultsInterfaceFactory $searchResultsFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        StoreManagerInterface $storeManager,
        CollectionProcessorInterface $collectionProcessor,
        JoinProcessorInterface $extensionAttributesJoinProcessor,
        ExtensibleDataObjectConverter $extensibleDataObjectConverter
    ) {
        $this->resource = $resource;
        $this->priceByCityFactory = $priceByCityFactory;
        $this->priceByCityCollectionFactory = $priceByCityCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataPriceByCityFactory = $dataPriceByCityFactory;
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
        \Aventi\PriceByCity\Api\Data\PriceByCityInterface $priceByCity
    ) {
        /* if (empty($priceByCity->getStoreId())) {
            $storeId = $this->storeManager->getStore()->getId();
            $priceByCity->setStoreId($storeId);
        } */
        
        $priceByCityData = $this->extensibleDataObjectConverter->toNestedArray(
            $priceByCity,
            [],
            \Aventi\PriceByCity\Api\Data\PriceByCityInterface::class
        );
        
        $priceByCityModel = $this->priceByCityFactory->create()->setData($priceByCityData);
        
        try {
            $this->resource->save($priceByCityModel);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the priceByCity: %1',
                $exception->getMessage()
            ));
        }
        return $priceByCityModel->getDataModel();
    }

    /**
     * {@inheritdoc}
     */
    public function get($priceByCityId)
    {
        $priceByCity = $this->priceByCityFactory->create();
        $this->resource->load($priceByCity, $priceByCityId);
        if (!$priceByCity->getId()) {
            throw new NoSuchEntityException(__('PriceByCity with id "%1" does not exist.', $priceByCityId));
        }
        return $priceByCity->getDataModel();
    }

    /**
     * {@inheritdoc}
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    ) {
        $collection = $this->priceByCityCollectionFactory->create();
        
        $this->extensionAttributesJoinProcessor->process(
            $collection,
            \Aventi\PriceByCity\Api\Data\PriceByCityInterface::class
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
        \Aventi\PriceByCity\Api\Data\PriceByCityInterface $priceByCity
    ) {
        try {
            $priceByCityModel = $this->priceByCityFactory->create();
            $this->resource->load($priceByCityModel, $priceByCity->getStorepriceId());
            $this->resource->delete($priceByCityModel);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the PriceByCity: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($priceByCityId)
    {
        return $this->delete($this->get($priceByCityId));
    }
}

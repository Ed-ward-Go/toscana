<?php
declare(strict_types=1);

namespace Aventi\PriceByCity\Model;

use Aventi\PriceByCity\Api\Data\StorePriceInterfaceFactory;
use Aventi\PriceByCity\Api\Data\StorePriceSearchResultsInterfaceFactory;
use Aventi\PriceByCity\Api\StorePriceRepositoryInterface;
use Aventi\PriceByCity\Model\ResourceModel\StorePrice as ResourceStorePrice;
use Aventi\PriceByCity\Model\ResourceModel\StorePrice\CollectionFactory as StorePriceCollectionFactory;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\ExtensibleDataObjectConverter;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Store\Model\StoreManagerInterface;

class StorePriceRepository implements StorePriceRepositoryInterface
{

    protected $extensibleDataObjectConverter;
    protected $storePriceCollectionFactory;

    protected $resource;

    protected $dataObjectHelper;

    private $storeManager;

    protected $storePriceFactory;

    protected $searchResultsFactory;

    protected $dataObjectProcessor;

    protected $dataStorePriceFactory;

    protected $extensionAttributesJoinProcessor;

    private $collectionProcessor;


    /**
     * @param ResourceStorePrice $resource
     * @param StorePriceFactory $storePriceFactory
     * @param StorePriceInterfaceFactory $dataStorePriceFactory
     * @param StorePriceCollectionFactory $storePriceCollectionFactory
     * @param StorePriceSearchResultsInterfaceFactory $searchResultsFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param StoreManagerInterface $storeManager
     * @param CollectionProcessorInterface $collectionProcessor
     * @param JoinProcessorInterface $extensionAttributesJoinProcessor
     * @param ExtensibleDataObjectConverter $extensibleDataObjectConverter
     */
    public function __construct(
        ResourceStorePrice $resource,
        StorePriceFactory $storePriceFactory,
        StorePriceInterfaceFactory $dataStorePriceFactory,
        StorePriceCollectionFactory $storePriceCollectionFactory,
        StorePriceSearchResultsInterfaceFactory $searchResultsFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        StoreManagerInterface $storeManager,
        CollectionProcessorInterface $collectionProcessor,
        JoinProcessorInterface $extensionAttributesJoinProcessor,
        ExtensibleDataObjectConverter $extensibleDataObjectConverter
    ) {
        $this->resource = $resource;
        $this->storePriceFactory = $storePriceFactory;
        $this->storePriceCollectionFactory = $storePriceCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataStorePriceFactory = $dataStorePriceFactory;
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
        \Aventi\PriceByCity\Api\Data\StorePriceInterface $storePrice
    ) {
        /* if (empty($storePrice->getStoreId())) {
            $storeId = $this->storeManager->getStore()->getId();
            $storePrice->setStoreId($storeId);
        } */
        
        $storePriceData = $this->extensibleDataObjectConverter->toNestedArray(
            $storePrice,
            [],
            \Aventi\PriceByCity\Api\Data\StorePriceInterface::class
        );
        
        $storePriceModel = $this->storePriceFactory->create()->setData($storePriceData);
        
        try {
            $this->resource->save($storePriceModel);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the storePrice: %1',
                $exception->getMessage()
            ));
        }
        return $storePriceModel->getDataModel();
    }

    /**
     * {@inheritdoc}
     */
    public function get($storePriceId)
    {
        $storePrice = $this->storePriceFactory->create();
        $this->resource->load($storePrice, $storePriceId);
        if (!$storePrice->getId()) {
            throw new NoSuchEntityException(__('StorePrice with id "%1" does not exist.', $storePriceId));
        }
        return $storePrice->getDataModel();
    }

    /**
     * {@inheritdoc}
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    ) {
        $collection = $this->storePriceCollectionFactory->create();
        
        $this->extensionAttributesJoinProcessor->process(
            $collection,
            \Aventi\PriceByCity\Api\Data\StorePriceInterface::class
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
        \Aventi\PriceByCity\Api\Data\StorePriceInterface $storePrice
    ) {
        try {
            $storePriceModel = $this->storePriceFactory->create();
            $this->resource->load($storePriceModel, $storePrice->getStorepriceId());
            $this->resource->delete($storePriceModel);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the StorePrice: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($storePriceId)
    {
        return $this->delete($this->get($storePriceId));
    }
}


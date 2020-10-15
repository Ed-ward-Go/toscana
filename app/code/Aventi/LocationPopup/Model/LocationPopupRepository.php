<?php
declare(strict_types=1);

namespace Aventi\LocationPopup\Model;

use Aventi\LocationPopup\Api\Data\LocationPopupInterfaceFactory;
use Aventi\LocationPopup\Api\Data\LocationPopupSearchResultsInterfaceFactory;
use Aventi\LocationPopup\Api\LocationPopupRepositoryInterface;
use Aventi\LocationPopup\Model\ResourceModel\LocationPopup as ResourceLocationPopup;
use Aventi\LocationPopup\Model\ResourceModel\LocationPopup\CollectionFactory as LocationPopupCollectionFactory;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\ExtensibleDataObjectConverter;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Store\Model\StoreManagerInterface;

class LocationPopupRepository implements LocationPopupRepositoryInterface
{

    protected $extensibleDataObjectConverter;
    protected $dataObjectHelper;

    protected $resource;

    private $storeManager;

    protected $locationPopupFactory;

    protected $searchResultsFactory;

    protected $dataObjectProcessor;

    protected $dataLocationPopupFactory;

    protected $locationPopupCollectionFactory;

    protected $extensionAttributesJoinProcessor;

    private $collectionProcessor;


    /**
     * @param ResourceLocationPopup $resource
     * @param LocationPopupFactory $locationPopupFactory
     * @param LocationPopupInterfaceFactory $dataLocationPopupFactory
     * @param LocationPopupCollectionFactory $locationPopupCollectionFactory
     * @param LocationPopupSearchResultsInterfaceFactory $searchResultsFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param StoreManagerInterface $storeManager
     * @param CollectionProcessorInterface $collectionProcessor
     * @param JoinProcessorInterface $extensionAttributesJoinProcessor
     * @param ExtensibleDataObjectConverter $extensibleDataObjectConverter
     */
    public function __construct(
        ResourceLocationPopup $resource,
        LocationPopupFactory $locationPopupFactory,
        LocationPopupInterfaceFactory $dataLocationPopupFactory,
        LocationPopupCollectionFactory $locationPopupCollectionFactory,
        LocationPopupSearchResultsInterfaceFactory $searchResultsFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        StoreManagerInterface $storeManager,
        CollectionProcessorInterface $collectionProcessor,
        JoinProcessorInterface $extensionAttributesJoinProcessor,
        ExtensibleDataObjectConverter $extensibleDataObjectConverter
    ) {
        $this->resource = $resource;
        $this->locationPopupFactory = $locationPopupFactory;
        $this->locationPopupCollectionFactory = $locationPopupCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataLocationPopupFactory = $dataLocationPopupFactory;
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
        \Aventi\LocationPopup\Api\Data\LocationPopupInterface $locationPopup
    ) {
        /* if (empty($locationPopup->getStoreId())) {
            $storeId = $this->storeManager->getStore()->getId();
            $locationPopup->setStoreId($storeId);
        } */
        
        $locationPopupData = $this->extensibleDataObjectConverter->toNestedArray(
            $locationPopup,
            [],
            \Aventi\LocationPopup\Api\Data\LocationPopupInterface::class
        );
        
        $locationPopupModel = $this->locationPopupFactory->create()->setData($locationPopupData);
        
        try {
            $this->resource->save($locationPopupModel);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the locationPopup: %1',
                $exception->getMessage()
            ));
        }
        return $locationPopupModel->getDataModel();
    }

    /**
     * {@inheritdoc}
     */
    public function get($locationPopupId)
    {
        $locationPopup = $this->locationPopupFactory->create();
        $this->resource->load($locationPopup, $locationPopupId);
        if (!$locationPopup->getId()) {
            throw new NoSuchEntityException(__('LocationPopup with id "%1" does not exist.', $locationPopupId));
        }
        return $locationPopup->getDataModel();
    }

    /**
     * {@inheritdoc}
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    ) {
        $collection = $this->locationPopupCollectionFactory->create();
        
        $this->extensionAttributesJoinProcessor->process(
            $collection,
            \Aventi\LocationPopup\Api\Data\LocationPopupInterface::class
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
        \Aventi\LocationPopup\Api\Data\LocationPopupInterface $locationPopup
    ) {
        try {
            $locationPopupModel = $this->locationPopupFactory->create();
            $this->resource->load($locationPopupModel, $locationPopup->getLocationpopupId());
            $this->resource->delete($locationPopupModel);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the LocationPopup: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($locationPopupId)
    {
        return $this->delete($this->get($locationPopupId));
    }
}


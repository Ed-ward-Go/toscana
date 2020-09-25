<?php


namespace Aventi\PickUpWithOffices\Model;

use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Magento\Framework\Api\DataObjectHelper;
use Aventi\PickUpWithOffices\Model\ResourceModel\Office as ResourceOffice;
use Aventi\PickUpWithOffices\Model\ResourceModel\Office\CollectionFactory as OfficeCollectionFactory;
use Aventi\PickUpWithOffices\Api\OfficeRepositoryInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Store\Model\StoreManagerInterface;
use Aventi\PickUpWithOffices\Api\Data\OfficeSearchResultsInterfaceFactory;
use Magento\Framework\Api\ExtensibleDataObjectConverter;
use Magento\Framework\Exception\CouldNotSaveException;
use Aventi\PickUpWithOffices\Api\Data\OfficeInterfaceFactory;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;

class OfficeRepository implements OfficeRepositoryInterface
{

    protected $resource;

    protected $extensionAttributesJoinProcessor;

    protected $extensibleDataObjectConverter;
    protected $officeCollectionFactory;

    protected $dataObjectProcessor;

    private $storeManager;

    private $collectionProcessor;

    protected $dataObjectHelper;

    protected $officeFactory;

    protected $dataOfficeFactory;

    protected $searchResultsFactory;


    /**
     * @param ResourceOffice $resource
     * @param OfficeFactory $officeFactory
     * @param OfficeInterfaceFactory $dataOfficeFactory
     * @param OfficeCollectionFactory $officeCollectionFactory
     * @param OfficeSearchResultsInterfaceFactory $searchResultsFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param StoreManagerInterface $storeManager
     * @param CollectionProcessorInterface $collectionProcessor
     * @param JoinProcessorInterface $extensionAttributesJoinProcessor
     * @param ExtensibleDataObjectConverter $extensibleDataObjectConverter
     */
    public function __construct(
        ResourceOffice $resource,
        OfficeFactory $officeFactory,
        OfficeInterfaceFactory $dataOfficeFactory,
        OfficeCollectionFactory $officeCollectionFactory,
        OfficeSearchResultsInterfaceFactory $searchResultsFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        StoreManagerInterface $storeManager,
        CollectionProcessorInterface $collectionProcessor,
        JoinProcessorInterface $extensionAttributesJoinProcessor,
        ExtensibleDataObjectConverter $extensibleDataObjectConverter
    ) {
        $this->resource = $resource;
        $this->officeFactory = $officeFactory;
        $this->officeCollectionFactory = $officeCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataOfficeFactory = $dataOfficeFactory;
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
        \Aventi\PickUpWithOffices\Api\Data\OfficeInterface $office
    ) {
        /* if (empty($office->getStoreId())) {
            $storeId = $this->storeManager->getStore()->getId();
            $office->setStoreId($storeId);
        } */
        
        $officeData = $this->extensibleDataObjectConverter->toNestedArray(
            $office,
            [],
            \Aventi\PickUpWithOffices\Api\Data\OfficeInterface::class
        );
        
        $officeModel = $this->officeFactory->create()->setData($officeData);
        
        try {
            $this->resource->save($officeModel);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the office: %1',
                $exception->getMessage()
            ));
        }
        return $officeModel->getDataModel();
    }

    /**
     * {@inheritdoc}
     */
    public function getById($officeId)
    {
        $office = $this->officeFactory->create();
        $this->resource->load($office, $officeId);
        if (!$office->getId()) {
            throw new NoSuchEntityException(__('Office with id "%1" does not exist.', $officeId));
        }
        return $office->getDataModel();
    }

    /**
     * {@inheritdoc}
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    ) {
        $collection = $this->officeCollectionFactory->create();
        
        $this->extensionAttributesJoinProcessor->process(
            $collection,
            \Aventi\PickUpWithOffices\Api\Data\OfficeInterface::class
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
        \Aventi\PickUpWithOffices\Api\Data\OfficeInterface $office
    ) {
        try {
            $officeModel = $this->officeFactory->create();
            $this->resource->load($officeModel, $office->getOfficeId());
            $this->resource->delete($officeModel);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the Office: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($officeId)
    {
        return $this->delete($this->getById($officeId));
    }
}

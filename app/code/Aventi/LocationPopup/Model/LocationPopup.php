<?php
declare(strict_types=1);

namespace Aventi\LocationPopup\Model;

use Aventi\LocationPopup\Api\Data\LocationPopupInterface;
use Aventi\LocationPopup\Api\Data\LocationPopupInterfaceFactory;
use Magento\Framework\Api\DataObjectHelper;

class LocationPopup extends \Magento\Framework\Model\AbstractModel
{

    protected $dataObjectHelper;

    protected $locationpopupDataFactory;

    protected $_eventPrefix = 'aventi_locationpopup_locationpopup';

    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param LocationPopupInterfaceFactory $locationpopupDataFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param \Aventi\LocationPopup\Model\ResourceModel\LocationPopup $resource
     * @param \Aventi\LocationPopup\Model\ResourceModel\LocationPopup\Collection $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        LocationPopupInterfaceFactory $locationpopupDataFactory,
        DataObjectHelper $dataObjectHelper,
        \Aventi\LocationPopup\Model\ResourceModel\LocationPopup $resource,
        \Aventi\LocationPopup\Model\ResourceModel\LocationPopup\Collection $resourceCollection,
        array $data = []
    ) {
        $this->locationpopupDataFactory = $locationpopupDataFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Retrieve locationpopup model with locationpopup data
     * @return LocationPopupInterface
     */
    public function getDataModel()
    {
        $locationpopupData = $this->getData();
        
        $locationpopupDataObject = $this->locationpopupDataFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $locationpopupDataObject,
            $locationpopupData,
            LocationPopupInterface::class
        );
        
        return $locationpopupDataObject;
    }
}


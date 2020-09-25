<?php


namespace Aventi\PickUpWithOffices\Model;

use Magento\Framework\Api\DataObjectHelper;
use Aventi\PickUpWithOffices\Api\Data\OfficeInterfaceFactory;
use Aventi\PickUpWithOffices\Api\Data\OfficeInterface;

class Office extends \Magento\Framework\Model\AbstractModel
{

    protected $officeDataFactory;

    protected $dataObjectHelper;

    protected $_eventPrefix = 'aventi_pickupwithoffices_office';

    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param OfficeInterfaceFactory $officeDataFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param \Aventi\PickUpWithOffices\Model\ResourceModel\Office $resource
     * @param \Aventi\PickUpWithOffices\Model\ResourceModel\Office\Collection $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        OfficeInterfaceFactory $officeDataFactory,
        DataObjectHelper $dataObjectHelper,
        \Aventi\PickUpWithOffices\Model\ResourceModel\Office $resource,
        \Aventi\PickUpWithOffices\Model\ResourceModel\Office\Collection $resourceCollection,
        array $data = []
    ) {
        $this->officeDataFactory = $officeDataFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Retrieve office model with office data
     * @return OfficeInterface
     */
    public function getDataModel()
    {
        $officeData = $this->getData();
        
        $officeDataObject = $this->officeDataFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $officeDataObject,
            $officeData,
            OfficeInterface::class
        );
        
        return $officeDataObject;
    }
}

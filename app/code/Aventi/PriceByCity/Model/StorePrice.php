<?php
declare(strict_types=1);

namespace Aventi\PriceByCity\Model;

use Aventi\PriceByCity\Api\Data\StorePriceInterface;
use Aventi\PriceByCity\Api\Data\StorePriceInterfaceFactory;
use Magento\Framework\Api\DataObjectHelper;

class StorePrice extends \Magento\Framework\Model\AbstractModel
{

    protected $dataObjectHelper;

    protected $_eventPrefix = 'aventi_pricebycity_storeprice';
    protected $storepriceDataFactory;


    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param StorePriceInterfaceFactory $storepriceDataFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param \Aventi\PriceByCity\Model\ResourceModel\StorePrice $resource
     * @param \Aventi\PriceByCity\Model\ResourceModel\StorePrice\Collection $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        StorePriceInterfaceFactory $storepriceDataFactory,
        DataObjectHelper $dataObjectHelper,
        \Aventi\PriceByCity\Model\ResourceModel\StorePrice $resource,
        \Aventi\PriceByCity\Model\ResourceModel\StorePrice\Collection $resourceCollection,
        array $data = []
    ) {
        $this->storepriceDataFactory = $storepriceDataFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Retrieve storeprice model with storeprice data
     * @return StorePriceInterface
     */
    public function getDataModel()
    {
        $storepriceData = $this->getData();
        
        $storepriceDataObject = $this->storepriceDataFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $storepriceDataObject,
            $storepriceData,
            StorePriceInterface::class
        );
        
        return $storepriceDataObject;
    }
}


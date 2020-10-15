<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Aventi\PriceByCity\Model;

use Aventi\PriceByCity\Api\Data\CityInventoryEquivalenceInterface;
use Aventi\PriceByCity\Api\Data\CityInventoryEquivalenceInterfaceFactory;
use Magento\Framework\Api\DataObjectHelper;

class CityInventoryEquivalence extends \Magento\Framework\Model\AbstractModel
{

    protected $_eventPrefix = 'aventi_city_inventory_equivalence';
    protected $dataObjectHelper;

    protected $cityinventoryequivalenceDataFactory;


    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param CityInventoryEquivalenceInterfaceFactory $cityinventoryequivalenceDataFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param \Aventi\PriceByCity\Model\ResourceModel\CityInventoryEquivalence $resource
     * @param \Aventi\PriceByCity\Model\ResourceModel\CityInventoryEquivalence\Collection $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        CityInventoryEquivalenceInterfaceFactory $cityinventoryequivalenceDataFactory,
        DataObjectHelper $dataObjectHelper,
        \Aventi\PriceByCity\Model\ResourceModel\CityInventoryEquivalence $resource,
        \Aventi\PriceByCity\Model\ResourceModel\CityInventoryEquivalence\Collection $resourceCollection,
        array $data = []
    ) {
        $this->cityinventoryequivalenceDataFactory = $cityinventoryequivalenceDataFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Retrieve cityinventoryequivalence model with cityinventoryequivalence data
     * @return CityInventoryEquivalenceInterface
     */
    public function getDataModel()
    {
        $cityinventoryequivalenceData = $this->getData();
        
        $cityinventoryequivalenceDataObject = $this->cityinventoryequivalenceDataFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $cityinventoryequivalenceDataObject,
            $cityinventoryequivalenceData,
            CityInventoryEquivalenceInterface::class
        );
        
        return $cityinventoryequivalenceDataObject;
    }
}

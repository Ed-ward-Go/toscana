<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Aventi\PriceByCity\Model;

use Aventi\PriceByCity\Api\Data\PriceByCityInterface;
use Aventi\PriceByCity\Api\Data\PriceByCityInterfaceFactory;
use Magento\Framework\Api\DataObjectHelper;

class PriceByCity extends \Magento\Framework\Model\AbstractModel
{

    protected $_eventPrefix = 'aventi_pricebycity_storeprice';
    protected $pricebycityDataFactory;

    protected $dataObjectHelper;


    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param PriceByCityInterfaceFactory $pricebycityDataFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param \Aventi\PriceByCity\Model\ResourceModel\PriceByCity $resource
     * @param \Aventi\PriceByCity\Model\ResourceModel\PriceByCity\Collection $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        PriceByCityInterfaceFactory $pricebycityDataFactory,
        DataObjectHelper $dataObjectHelper,
        \Aventi\PriceByCity\Model\ResourceModel\PriceByCity $resource,
        \Aventi\PriceByCity\Model\ResourceModel\PriceByCity\Collection $resourceCollection,
        array $data = []
    ) {
        $this->pricebycityDataFactory = $pricebycityDataFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Retrieve pricebycity model with pricebycity data
     * @return PriceByCityInterface
     */
    public function getDataModel()
    {
        $pricebycityData = $this->getData();
        
        $pricebycityDataObject = $this->pricebycityDataFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $pricebycityDataObject,
            $pricebycityData,
            PriceByCityInterface::class
        );
        
        return $pricebycityDataObject;
    }
}

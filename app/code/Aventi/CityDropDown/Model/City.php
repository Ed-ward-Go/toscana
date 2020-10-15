<?php


namespace Aventi\CityDropDown\Model;

use Magento\Framework\Api\DataObjectHelper;
use Aventi\CityDropDown\Api\Data\CityInterfaceFactory;
use Aventi\CityDropDown\Api\Data\CityInterface;

class City extends \Magento\Framework\Model\AbstractModel
{

    protected $cityDataFactory;

    protected $dataObjectHelper;

    protected $_eventPrefix = 'aventi_citydropdown_city';

    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param CityInterfaceFactory $cityDataFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param \Aventi\CityDropDown\Model\ResourceModel\City $resource
     * @param \Aventi\CityDropDown\Model\ResourceModel\City\Collection $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        CityInterfaceFactory $cityDataFactory,
        DataObjectHelper $dataObjectHelper,
        \Aventi\CityDropDown\Model\ResourceModel\City $resource,
        \Aventi\CityDropDown\Model\ResourceModel\City\Collection $resourceCollection,
        array $data = []
    ) {
        $this->cityDataFactory = $cityDataFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Retrieve city model with city data
     * @return CityInterface
     */
    public function getDataModel()
    {
        $cityData = $this->getData();
        
        $cityDataObject = $this->cityDataFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $cityDataObject,
            $cityData,
            CityInterface::class
        );
        
        return $cityDataObject;
    }
}

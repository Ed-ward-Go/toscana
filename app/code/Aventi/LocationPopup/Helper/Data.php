<?php

namespace Aventi\LocationPopup\Helper;

use Magento\Framework\App\Helper\AbstractHelper;

class Data extends AbstractHelper
{
    /**
     * @var \Magento\Framework\Session\SessionManagerInterface
     */

    private $coreSession;

    /**
     * @var \Magento\Framework\Json\Helper\Data
     */
    protected $jsonHelper;

    /**
     * @var \Magento\Framework\Api\SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;
    /**
     * @var \Magento\Framework\Api\FilterBuilder
     */
    private $filterBuilder;
    /**
     * @var \Magento\Framework\Api\Search\FilterGroupBuilder
     */
    private $filterGroupBuilder;
    /**
     * @var \Aventi\CityDropDown\Model\CityRepository
     */
    private $cityRepository;
    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;
    /**
     * @var \Magento\Framework\Api\Search\SortOrder
     */
    private $sortOrder;

    protected $country;

    private $region;

    const PATH_COUNTRY = 'location/config/country';
    const PATH_REGION = 'location/config/region';
    const PATH_CITY = 'location/config/city';
    const PATH_POSTCODE = 'location/config/postcode';

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\Session\SessionManagerInterface $coreSession,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \Magento\Directory\Model\Country $country,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder,
        \Magento\Framework\Api\FilterBuilder $filterBuilder,
        \Magento\Framework\Api\SortOrder $sortOrder,
        \Magento\Framework\Api\Search\FilterGroupBuilder $filterGroupBuilder,
        \Aventi\CityDropDown\Model\CityRepository $cityRepository,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Directory\Model\Region $region
    ) {
        parent::__construct($context);
        $this->coreSession = $coreSession;
        $this->jsonHelper = $jsonHelper;
        $this->country = $country;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->filterBuilder = $filterBuilder;
        $this->filterGroupBuilder = $filterGroupBuilder;
        $this->sortOrder = $sortOrder;
        $this->cityRepository = $cityRepository;
        $this->logger = $logger;
        $this->region = $region;
    }

    public function getCountry($store = null)
    {
        return $this->scopeConfig->getValue(self::PATH_COUNTRY, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $store);
    }

    public function getRegion($store = null)
    {
        return $this->scopeConfig->getValue(self::PATH_REGION, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $store);
    }

    public function getCity($store = null)
    {
        return $this->scopeConfig->getValue(self::PATH_CITY, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $store);
    }

    public function getPostCode($store = null)
    {
        return $this->scopeConfig->getValue(self::PATH_POSTCODE, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $store);
    }

    /**
     * @param $array
     */
    public function setValue($array)
    {
        $this->coreSession->start();

        $this->coreSession->setLocation($array);
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        $this->coreSession->start();
        $result = $this->coreSession->getLocation();
        if (!$result) {
            $result = [
                "city" => $this->getCity(),
                "region" => $this->getRegion(),
                "postcode" => $this->getPostCode(),
                "default" => true
            ];
        }
        return $result;
    }

    /**
     * @return mixed
     */
    public function unsValue()
    {
        $this->coreSession->start();
        return $this->coreSession->unsLocation();
    }

    public function getDefaultRegion()
    {
        $regions = $this->toOptionArray($this->getCountry());
        $options = '';
        $region = (!is_null($this->getValue())) ? $this->getValue()['region'] : $this->getRegion();
        foreach ($regions as $key => $value) {
            $selected = '';
            if ($value['value'] == $region) {
                $selected = "selected";
            }
            $options .= '<option value="' . $value['value'] . '" ' . $selected . '>' . $value['label'] . '</option>';
        }
        return $options;
    }

    public function getDefaultCities()
    {
        $region = (!is_null($this->getValue())) ? $this->getValue()['region'] : $this->getRegion();
        $cities = $this->getCitiesByregion($region);
        $options = '';

        $postcode = (!is_null($this->getValue())) ? $this->getValue()['postcode'] : $this->getPostCode();
        foreach ($cities as $key => $value) {
            $selected = '';
            if ($value['postalCode'] == $postcode) {
                $selected = "selected";
            }
            $options .= '<option data-postcode="' . $value['postalCode'] . '" value="' . $value['name'] . '" ' . $selected . '>' . $value['name'] . '</option>';
        }
        return $options;
    }

    public function toOptionArray($option)
    {
        $arr = $this->_toArray($option);
        $ret = [];

        foreach ($arr as $key => $value) {
            $ret[] = [
                'value' => $value['value'],
                'label' => $value['title']
            ];
        }

        return $ret;
    }

    private function _toArray($code)
    {
        $regionCollection = $this->country->loadByCode($code)->getRegions();
        $regions = $regionCollection->loadData()->toOptionArray(true);
        return $regions;
    }

    public function getCitiesByregion($region_id)
    {
        try {
            $region = $region_id;
            $filterGroup = $this->filterGroupBuilder;
            $sortOrder = $this->sortOrder;
            $filterGroup->addFilter(
                $this->filterBuilder
                    ->setField('region_id')
                    ->setConditionType('like')
                    ->setValue($region)
                    ->create()
            );

            $sortOrder
                ->setField("name")
                ->setDirection("ASC");

            $searchCriteria = $this->searchCriteriaBuilder
                ->setFilterGroups([$filterGroup->create()])
                ->create();

            $searchCriteria->setSortOrders([$sortOrder]);
            $cities = $this->cityRepository->getList($searchCriteria)->getItems();
            $items = [];
            foreach ($cities as $city) {
                $items[] =  [
                    'name' => $city->getName(),
                    'id' => $city->getCityId(),
                    'postalCode' => $city->getPostalCode()
                ];
            }
            return $items;
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->logger->error($e->getMessage());
        } catch (\Exception $e) {
            $this->logger->critical($e);
        }
    }

    public function getRegionName($id)
    {
        return $this->region->load($id)->getName();
    }
}
